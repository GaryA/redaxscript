<?php
/**
 * class to provide a navigation list from a table
 *
 * @since 2.2.0
 *
 * @package Redaxscript
 * @category Navigation
 * @author Gary Aylward
 */

namespace Redaxscript\Navigation;
use Redaxscript\Db;
use Redaxscript\Navigation;

class Table extends Navigation
{
	/**
	 * initialise the class
	 *
	 * @since 2.2.0
	 */

	public function init()
	{
		$this->_navigationArray = array();
		$query = $this->_buildQuery();
		/* query result */
		/*
		$resultSet = mysql_query($query);
		*/
		$resultSet = $query -> find_many();

		$this->_buildArray($resultSet, $this->_navigationArray);
		$this->render();
	}

	/**
	 * build the database query
	 *
	 * @since 2.2.0
	 *
	 * @return object
	 */

	protected function _buildQuery()
	{
		switch ($this->_type)
		{
			case 'categories':
				$queryParent = 'parent';
				break;
			case 'articles':
				$queryParent = 'category';
				break;
			case 'comments':
				$queryParent = 'article';
				break;
			default:
				$queryParent = '';
				break;
		}

		/* fallback */

		if (!array_key_exists('order', $this->_options) || $this->_options['order'] === '')
		{
			$this->_options['order'] = s('order');
		}
		if (!array_key_exists('limit', $this->_options) || $this->_options['limit'] === '')
		{
			$this->_options['limit'] = s('limit');
		}

		/* query contents */

		$query = Db::forPrefixTable($this->_type) -> where_any_is(array(
			array('language' => $this->_registry->get('language'), 'status' => 1),
			array('language' => '', 'status' => 1)));
/*
		$query = 'SELECT * FROM ' . PREFIX . $this->_type . ' WHERE (language = \'' . $this->_registry->get('language') . '\' || language = \'\') && status = 1';
*/
		/* setup parent */

		if ($queryParent)
		{
			if (array_key_exists('parent', $this->_options))
			{
/*

				$query .= ' && ' . $queryParent . ' = ' . $this->_options['parent'];
*/
				$query = $query -> where($queryParent, $this->_options['parent']);

			}
		}

		/* setup query filter */

		if ($this->_type === 'categories' || $this->_type === 'articles')
		{
			/* setup filter alias option */

			if (array_key_exists('filter_alias', $this->_options))
			{
/*				$query .= ' && alias IN (' . $this->_options['filter_alias'] . ')';
*/
				$query = $query -> where_in('alias', $this->_options['filter_alias']);

			}

			/* setup filter rank option */

			if (array_key_exists('filter_rank', $this->_options))
			{
/*				$query .= ' && rank IN (' . $this->_options['filter_rank'] . ')';
*/
				$query = $query -> where_in('rank', $this->_options['filter_rank']);

			}
		}

		/* setup rank and limit */

/*		$query .= ' ORDER BY rank ' . $this->_options['order'] . ' LIMIT ' . $this->_options['limit'];
*/
		$query = $query -> order_by_asc('rank');
		$query = $query -> order_by_desc('rank');
		$query = $query -> limit($this->_options['limit']);

		return $query;
	}

	/**
	 * build the navigation array (recursively)
	 *
	 * @since 2.2.0
	 *
	 * @param array $resultSet result set from db query
	 * @param array &$array pass the current level of the navigationArray by reference
	 * @param int $filter parent id acts as filter for sub-categories
	 */

	protected function _buildArray($resultSet, &$array, $filter = 0)
	{
		$index = 0;
		$errorCount = 0;
		if (count($resultSet) > 0)
		{
			/* iterate over the record set */

			foreach ($resultSet as $record)
			{
				if (check_access($record->access, $this->_registry->get('myGroups')))
				{
					if (($this->_type === 'categories' && (int)$record->parent === $filter) || $this->_type === 'articles' || $this->_type === 'comments')
					{
						/* filter categories (and articles?) */

						if ($this->_type === 'comments')
						{
							/* create comment description from author and start of text */

							$array[$index]['description'] = $array[$index]['title'] = truncate($record->author . $this->_language->get('colon') . ' ' . strip_tags($record->text), 80, '...');
						}
						else
						{
							$array[$index]['description'] = $record->description;

							if ($array[$index]['description'] === '')
							{
								/* if there is no description use the title */

								$array[$index]['description'] = $record->title;
							}
							$array[$index]['title'] = $record->title;

							/* create route to item */

							if ($this->_type === 'categories' && (int)$record->parent === 0)
							{
								$array[$index]['route'] = $record->alias;
							}
							else
							{
								$array[$index]['route'] = build_route($this->_type, $record->id);
							}
						}

						/* set active flag */

						if ($this->_registry->get('lastParameter') === $record->alias && $this->_type !== 'comments')
						{
							$array[$index]['active'] = true;
						}
						else
						{
							$array[$index]['active'] = false;
						}

						if ($this->_type === 'categories' && array_key_exists('children', $this->_options) && $this->_options['children'] === 1)
						{
							/* recurse into sub-categories */

							$this->_buildArray($resultSet, $array[$index], (int)$record->id);
						}
						$index++;
					}
				}
				else
				{
					/* access denied */

					$errorCount++;
				}
			}
			if ($errorCount === count($resultSet))
			{
				$array[$index]['error'] = true;
			}
		}
	}

	/**
	 * render the navigation array as html
	 *
	 * @since 2.2.0
	 */

	public function render()
	{
		$prefix = hook(__FUNCTION__ . '_start');
		$output = '';
		$error = '';

		/* switch table */

		switch ($this->_type)
		{
			case 'categories':
				$wording_single = 'category';
				break;
			case 'articles':
				$wording_single = 'article';
				break;
			case 'comments':
				$wording_single = 'comment';
				break;
			default:
				$wording_single = '';
				break;
		}

		$output = $this->_buildLinks($this->_navigationArray, $output);

		if ($output === '')
		{
			$error = $this->_language->get($wording_single . '_no') . $this->_language->get('point');
		}

		/* build id string */

		$idString = $this->_buildIdString();

		/* build class string */

		$classString = $this->_buildClassString();

		/* handle error */

		if ($error && (!array_key_exists('parent', $this->_options) || $this->_options['parent'] === ''))
		{
			$output = '<ul' .$idString . $classString . '><li>' . $error . '</li></ul>';
		}

		$output = $prefix . $output . hook(__FUNCTION__ . '_end');
		$this->_output = $output;
	}

	/**
	 * build a nested list of hyperlinks (recursively)
	 *
	 * @since 2.2.0
	 *
	 * @param array &$array level of navigationArray to be processed by reference
	 * @param string $output pass in the output string to append links to
	 * @param int $level level of the heirarchy
	 *
	 * @return string
	 */

	protected function _buildLinks(&$array, $output, $level = 0)
	{
		$index = 0;
		if (array_key_exists($index, $array))
		{
			/* build id string */

			$idString = $this->_buildIdString();

			/* build class string */

			if ($level === 0)
			{
				$classString = $this->_buildClassString();
			}
			else
			{
				$classString = ' class="list_children"';
			}
			$output .= '<ul' .$idString . $classString . '>';
			while (array_key_exists($index, $array))
			{
				/* build class string */

				if ($array[$index]['active'])
				{
					$class_string = ' class="item_active"';
				}
				else
				{
					$class_string = '';
				}

				if (array_key_exists('error', $array[$index]))
				{
					$output .= '<li' . $class_string . '>' . $this->_language->get('access_no') . $this->_language->get('point') . '</li>';
				}
				else
				{

					/* collect item output */

					$output .= '<li' . $class_string . '>' . '<a href="' . REWRITE_ROUTE . $array[$index]['route'] . '" title="' . $array[$index]['description'] . '">' . $array[$index]['title'] . '</a>';

					/* get sub-list items */

					$output = $this->_buildLinks($array[$index], $output, $level + 1);

					$output .= '</li>';
				}
				$index++;
			}
			$output .= '</ul>';
		}
		return $output;
	}
}

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

class Redaxscript_Navigation_Table extends Redaxscript_Navigation
{
	/**
	 * initialise the class
	 *
	 * @since 2.2.0
	 *
	 * @param int $iteration iteration level through nested lists
	 */

	public function init($iteration = 0)
	{
		$prefix = hook(__FUNCTION__ . '_start');
		$output = '';
		$error = '';

		/* fallback */

		if (!array_key_exists('order', $this->_options[$iteration]) || $this->_options[$iteration]['order'] === '')
		{
			$this->_options[$iteration]['order'] = s('order');
		}
		if (!array_key_exists('limit', $this->_options[$iteration]) || $this->_options[$iteration]['limit'] === '')
		{
			$this->_options[$iteration]['limit'] = s('limit');
		}

		/* switch table */

		switch ($this->_type)
		{
			case 'categories':
				$wording_single = 'category';
				$query_parent = 'parent';
				break;
			case 'articles':
				$wording_single = 'article';
				$query_parent = 'category';
				break;
			case 'comments':
				$wording_single = 'comment';
				$query_parent = 'article';
				break;
		}

		$query = $this->_buildQuery($query_parent, $iteration);

		/* query result */

		$result = mysql_query($query);
		$num_rows = mysql_num_rows($result);
		if ($result === false || (bool)$num_rows === false)
		{
			$error = $this->_language->get($wording_single . '_no') . $this->_language->get('point');
		}
		else if ($result)
		{
			/* collect output */

			$counter = 0;
			while ($r = mysql_fetch_assoc($result))
			{
				$access = $r['access'];
				$check_access = check_access($access, $this->_registry->get('myGroups'));

				/* if access granted */

				if ($check_access === 1)
				{
					if ($r)
					{
						foreach ($r as $key => $value)
						{
							$$key = stripslashes($value);
						}
					}
					$alias = !isset($alias) ? '' : $alias;

					/* build class string */

					if ($this->_registry->get('lastParameter') === $alias && $this->_type !== 'comments')
					{
						$class_string = ' class="item_active"';
					}
					else
					{
						$class_string = '';
					}

					/* prepare metadata */

					if ($this->_type === 'comments')
					{
						$description = $title = truncate($author . $this->_language->get('colon') . ' ' . strip_tags($text), 80, '...');
					}
					if ($description === '')
					{
						$description = $title;
					}

					/* build route */

					if ($this->_type === 'categories' && $parent === 0 || $this->_type === 'articles' && $category === 0)
					{
						$route = $alias;
					}
					else
					{
						$route = build_route($this->_type, $id);
					}

					/* collect item output */

					$output .= '<li' . $class_string . '>' . anchor_element('internal', '', '', $title, $route, $description);

					/* collect children list output */

					if ($this->_type === 'categories' && array_key_exists('children', $this->_options[$iteration]) && $this->_options[$iteration]['children'] === 1)
					{
						$this->_options[$iteration + 1] = array(
							'parent' => $id,
							'class' => 'list_children'
						);
						$this->init($iteration + 1);
						$output .= $this->get();
					}
					$output .= '</li>';
				}
				else
				{
					$counter++;
				}
			}

			/* handle access */

			if ($num_rows === $counter)
			{
				$error = $this->_language->get('access_no') . $this->_language->get('point');
			}
		}

		/* build id string */

		if (array_key_exists('id', $this->_options[$iteration]))
		{
			$id_string = ' id="' . $this->_options[$iteration]['id'] . '"';
		}
		else
		{
			$id_string = '';
		}

		/* build class string */

		if (array_key_exists('class', $this->_options[$iteration]))
		{
			$class_string = ' class="' . $this->_options[$iteration]['class'] . '"';
		}
		else
		{
			$class_string = ' class="list_' . $this->_type . '"';
		}

		/* handle error */

		if ($error && (!array_key_exists('parent', $this->_options[$iteration]) || $this->_options[$iteration]['parent'] === ''))
		{
			$output = '<ul' .$id_string . $class_string . '><li>' . $error . '</li></ul>';
		}

		/* else collect list output */

		else if ($output)
		{
			$output = '<ul' .$id_string . $class_string . '>' . $output . '</ul>';
		}
		$output = $prefix . $output . hook(__FUNCTION__ . '_end');
		$this->_output = $output;
	}

	/**
	 * build the database query
	 *
	 * @since 2.2.0
	 * @deprecated 2.2.0
	 *
	 * @param string $query_parent
	 * @param int $iteration
	 *
	 * @return string
	 */

	protected function _buildQuery($query_parent = '', $iteration = 0)
	{
		/* query contents */

		$query = 'SELECT * FROM ' . PREFIX . $this->_type . ' WHERE (language = \'' . $this->_registry->get('language') . '\' || language = \'\') && status = 1';

		/* setup parent */

		if ($query_parent)
		{
			if (array_key_exists('parent', $this->_options[$iteration]))
			{
				$query .= ' && ' . $query_parent . ' = ' . $this->_options[$iteration]['parent'];
			}
			else if ($this->_type == 'categories')
			{
				$query .= ' && ' . $query_parent . ' = 0';
			}
		}

		/* setup query filter */

		if ($this->_type === 'categories' || $this->_type === 'articles')
		{
			/* setup filter alias option */

			if (array_key_exists('filter_alias', $this->_options[$iteration]))
			{
				$query .= ' && alias IN (' . $this->_options[$iteration]['filter_alias'] . ')';
			}

			/* setup filter rank option */

			if (array_key_exists('filter_rank', $this->_options[$iteration]))
			{
				$query .= ' && rank IN (' . $this->_options[$iteration]['filter_rank'] . ')';
			}
		}

		/* setup rank and limit */

		$query .= ' ORDER BY rank ' . $this->_options[$iteration]['order'] . ' LIMIT ' . $this->_options[$iteration]['limit'];
		return $query;
	}
}
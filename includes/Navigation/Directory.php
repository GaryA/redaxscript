<?php
/**
 * class to provide a navigation list from a directory listing
 *
 * @since 2.2.0
 *
 * @package Redaxscript
 * @category Navigation
 * @author Gary Aylward
 */

class Redaxscript_Navigation_Directory extends Redaxscript_Navigation
{
	/**
	 * initialise the class
	 *
	 * @since 2.2.0
	 *
	 * @param int $iteration not used
	 */

	public function init($iteration = 0)
	{
		$prefix = hook(__FUNCTION__ . '_start');
		$output = '';

		/* define option variables */

		if (is_array($this->_options))
		{
			foreach ($this->_options as $key => $value)
			{
				$key = 'option_' . $key;
				$$key = $value;
			}
		}

		/* templates directory object */

		$directory_array = $this->_directory->get();

		/* collect templates output */

		foreach ($directory_array as $value)
		{
			$class_string = ' class="' . $this->_type . '_' . $value;
			if ($value == $this->_registry->get($this->_type))
			{
				$class_string .= ' item_active';
			}
			$class_string .= '"';
			$output .= '<li' . $class_string . '>' . anchor_element('internal', '', '', $value, $this->_registry->get('fullRoute') . $this->_registry->get($this->_type . 'Route') . $value, '', 'rel="nofollow"') . '</li>';
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
			$class_string = ' class="list_' . $this->_type . 's"';
		}

		/* collect list output */

		if ($output)
		{
			$output = '<ul' .$id_string . $class_string . '>' . $output . '</ul>';
		}
		$output = $prefix . $output . hook(__FUNCTION__ . '_end');
		$this->_output = $output;
	}
}
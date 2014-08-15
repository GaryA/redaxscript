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

namespace Redaxscript\Navigation;
use Redaxscript\Navigation;
use Redaxscript;

class Directory extends Navigation
{
	/**
	 * initialise the class
	 *
	 * @since 2.2.0
	 */

	public function init()
	{
		$prefix = Redaxscript\Hook::trigger(__FUNCTION__ . '_start');
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
			if ($this->_type === 'language')
			{
				$value = substr($value, 0, 2);
				$link_text = $this->_language->get($value, '_index');
			}
			else
			{
				$link_text = $value;
			}
			$class_string = ' class="' . $this->_type . '_' . $value;
			if ($value === $this->_registry->get($this->_type))
			{
				$class_string .= ' item_active';
			}
			$class_string .= '"';
			$output .= '<li' . $class_string . '>' . anchor_element('internal', '', '', $link_text, $this->_registry->get('fullRoute') . $this->_registry->get($this->_type . 'Route') . $value, '', 'rel="nofollow"') . '</li>';
		}

		/* build id string */

		if (array_key_exists('id', $this->_options))
		{
			$id_string = ' id="' . $this->_options['id'] . '"';
		}
		else
		{
			$id_string = '';
		}

		/* build class string */

		if (array_key_exists('class', $this->_options))
		{
			$class_string = ' class="' . $this->_options['class'] . '"';
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
		$output = $prefix . $output . Redaxscript\Hook::trigger(__FUNCTION__ . '_end');
		$this->_output = $output;
	}
}
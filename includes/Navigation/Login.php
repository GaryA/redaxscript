<?php
/**
 * class to provide a navigation list of login options
 *
 * @since 2.2.0
 *
 * @package Redaxscript
 * @category Navigation
 * @author Gary Aylward
 */

namespace Redaxscript\Navigation;
use Redaxscript\Navigation;

class Login extends Navigation
{
	/**
	 * initialise the class
	 *
	 * @since 2.2.0
	 */

	public function init()
	{
		$this->_buildArray();
		$this->render();
	}

	protected function _buildArray()
	{
		$this->_navigationArray = array();
		if ($this->_registry->get('loggedIn') === $this->_registry->get('token') && $this->_registry->get('firstParameter') !== 'logout')
		{
			$this->_navigationArray[0]['description'] = $this->_navigationArray[0]['title'] = $this->_language->get('logout');
			$this->_navigationArray[0]['route'] = 'logout';
			$this->_navigationArray[1]['description'] = $this->_navigationArray[1]['title'] = $this->_language->get('administration');
			$this->_navigationArray[1]['route'] = 'administration';
		}
		else
		{
			$this->_navigationArray[0]['description'] = $this->_navigationArray[0]['title'] = $this->_language->get('login');
			$this->_navigationArray[0]['route'] = 'login';
			if (s('reminder') == 1)
			{
				$this->_navigationArray[1]['description'] = $this->_navigationArray[1]['title'] = $this->_language->get('reminder');
				$this->_navigationArray[1]['route'] = 'reminder';
			}
			if (s('registration') == 1)
			{
				$this->_navigationArray[2]['description'] = $this->_navigationArray[2]['title'] = $this->_language->get('registration');
				$this->_navigationArray[2]['route'] = 'registration';
			}
		}
	}

	public function render()
	{
		$index = 0;
		$output = '<ul class="list_login">';
		while(array_key_exists($index,$this->_navigationArray))
		{
			$classString = ' class="item_' . $this->_navigationArray[$index]['route'] . '"';
			$output .= '<li' . $classString . '>' . '<a href="' . REWRITE_ROUTE . $this->_navigationArray[$index]['route'] . '" title="' . $this->_navigationArray[$index]['description'] . '">' . $this->_navigationArray[$index]['title'] . '</a>';
			$index++;
		}
		$output .= '</ul>';
		$this->_output = $output;
	}
}
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

class Redaxscript_Navigation_Login extends Redaxscript_Navigation
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
		if ($this->_registry->get('loggedIn') == $this->_registry->get('token') && $this->_registry->get('firstParameter') != 'logout')
		{
			$output = '<li class="item_logout">' . anchor_element('internal', '', '', l('logout'), 'logout', '', 'rel="nofollow"') . '</li>';
			$output .= '<li class="item_administration">' . anchor_element('internal', '', '', l('administration'), 'admin', '', 'rel="nofollow"') . '</li>';
		}
		else
		{
			$output = '<li class="item_login">' . anchor_element('internal', '', '', l('login'), 'login', '', 'rel="nofollow"') . '</li>';
			if (s('reminder') == 1)
			{
				$output .= '<li class="item_reminder">' . anchor_element('internal', '', '', l('reminder'), 'reminder', '', 'rel="nofollow"') . '</li>';
			}
			if (s('registration') == 1)
			{
				$output .= '<li class="item_registration">' . anchor_element('internal', '', '', l('registration'), 'registration', '', 'rel="nofollow"') . '</li>';
			}
		}
		$output = '<ul class="list_login">' . $output . '</ul>';
		$this->_output = $output;
	}
}
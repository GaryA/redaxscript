<?php

/**
 * get parameter
 *
 * @param string $input
 * @return string
 */

function get_parameter($input = '')
{
	static $parameter;

	/* get parameter */

	if ($parameter == '')
	{
		// GaryA - ensure $parameter is initialised in cases where 'p' does not exist in $_GET
		$parameter = (array_key_exists('p', $_GET)) ? explode('/', $_GET['p']) : array(0 => '');

		/* clean parameter */

		$parameter = array_map('clean_alias', $parameter);
		$parameter = array_map('clean_mysql', $parameter);
	}

	/* if admin parameter */

	if ($parameter[0] == 'admin')
	{
		$admin = 1;
	}
	// GaryA - test whether parameters exist and are numeric or not before switch statement
	$p0_not_numeric = is_numeric($parameter[0]) === false;
	$p1_numeric = count($parameter) > 1 && is_numeric($parameter[1]);
	$p1_not_numeric = count($parameter) > 1 && is_numeric($parameter[1]) === false;
	$p2_numeric = count($parameter) > 2 && is_numeric($parameter[2]);
	$p2_not_numeric = count($parameter) > 2 && is_numeric($parameter[2]) === false;
	$p3_numeric = count($parameter) > 3 && is_numeric($parameter[3]);
	$p3_not_numeric = count($parameter) > 3 && is_numeric($parameter[3]) === false;

	/* switch parameter */

	switch (true)
	{
		case $input == 'first' && $p0_not_numeric:
			$output = $parameter[0];
			break;
		case $input == 'first_sub' && $p1_numeric:
		case $input == 'second' && $p1_not_numeric:
		case $input == 'admin' && $admin == 1:
			$output = $parameter[1];
			break;
		case $input == 'second_sub' && $p2_numeric:
		case $input == 'third' && $p2_not_numeric:
		case $input == 'table' && $admin == 1:
			$output = $parameter[2];
			break;
		case $input == 'third_sub' && $p3_numeric:
		case $input == 'id' && $admin == 1 && $p3_numeric:
		case $input == 'alias' && $admin == 1 && $p3_not_numeric:
			$output = $parameter[3];
			break;
		case $input == 'last' && is_numeric(end($parameter)):
			$output = prev($parameter);
			break;
		case $input == 'last' && is_numeric(end($parameter)) == '':
		case $input == 'last_sub' && is_numeric(end($parameter)):
		case $input == 'token' && end($parameter) == TOKEN:
			$output = end($parameter);
			break;
		default:
			$output = '';
			break;
	}
	return $output;
}

/**
 * get route
 *
 * @param integer $mode
 * @return string
 */

function get_route($mode = '')
{
	$output = ''; // GaryA - initialise variable
	/* switch admin parameter */

	switch (ADMIN_PARAMETER)
	{
		case 'up':
		case 'down':
		case 'publish':
		case 'unpublish':
		case 'enable':
		case 'disable':
		case 'install':
		case 'uninstall':
		case 'delete':
		case 'process':
			$output = 'admin/view/' . TABLE_PARAMETER;
			break;
		case 'update':
			$output = 'admin/edit/' . TABLE_PARAMETER;
			break;
		default:
			// GaryA - ensure $parameter is initialised even if 'p' does not exist in $_GET
			$parameter = (array_key_exists('p', $_GET)) ? explode('/', $_GET['p']) : array();

			/* clean parameter */

			$parameter = array_map('clean_alias', $parameter);
			$parameter = array_map('clean_mysql', $parameter);

			/* mode one */

			if ($mode == 1)
			{
				$last_value = end($parameter);
				if (is_numeric($last_value))
				{
					$parameter_keys = array_keys($parameter);
					$last = end($parameter_keys);
					unset($parameter[$last]);
				}
			}
			$parameter_keys = array_keys($parameter);
			$last = end($parameter_keys);
			foreach ($parameter as $key => $value)
			{
				$output .= $value;
				if ($last != $key)
				{
					$output .= '/';
				}
			}
	}
	return $output;
}

/**
 * get file
 *
 * @return string
 */

function get_file()
{
	$output = basename($_SERVER['SCRIPT_NAME']);
	return $output;
}

/**
 * get root
 *
 * @return string
 */

function get_root()
{
	$host = 'http://' . $_SERVER['HTTP_HOST'];
	$directory = dirname($_SERVER['SCRIPT_NAME']);
	$output = $directory == '/' || $directory == '\\' ? $host : $host . $directory;
	return $output;
}

/**
 * get user ip
 *
 * @return string
 */

function get_user_ip()
{
	$output = $_SERVER['REMOTE_ADDR'];
	return $output;
}

/**
 * get user agent
 *
 * @param integer $mode
 * @return string
 */

function get_user_agent($mode = '')
{
	/* switch mode */

	switch ($mode)
	{
		case 2:
			$type = 'agent_engines';
			break;
		case 3:
			$type = 'agent_systems';
			break;
		case 4:
			$type = 'agent_mobiles';
			break;
		default:
			$type = 'agent_browsers';
			break;
	}
	$list = explode(', ', b($type));
	$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

	/* collect output */
	$output = ''; // GaryA - initialise variable
	foreach ($list as $value)
	{
		if (stristr($user_agent, $value))
		{
			/* get browser version */

			if ($mode == 1)
			{
				$output = floor(substr($user_agent, strpos($user_agent, $value) + strlen($value) + 1, 3));

				/* opera fallback */

				if ($output > 100)
				{
					$output = substr($output, 0, 1);
				}
			}
			else
			{
				$output = $value;
			}
		}
	}
	if ($output)
	{
		return $output;
	}
}

/**
 * get token
 *
 * @return string
 */

function get_token()
{
	$a = session_id();
	$b = $_SERVER['REMOTE_ADDR'];
	$c = $_SERVER['HTTP_USER_AGENT'];
	$d = $_SERVER['HTTP_HOST'];
	$output = sha1($a . $b . $c . $d);
	return $output;
}
?>
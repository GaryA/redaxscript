<?php

/**
 * breadcrumb
 *
 * @since 2.1.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Gary Aylward
 */

function breadcrumb()
{
	$registry = Redaxscript_Registry::instance();
	$breadcrumb = new Redaxscript_Breadcrumb($registry);
	echo $breadcrumb->render();
}

/**
 * helper class
 *
 * @since 2.1.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Kim Kha Nguyen
 */

function helper_class()
{
	$registry = Redaxscript_Registry::instance();
	$helper = new Redaxscript_Helper($registry);
	echo $helper->getClass();
}

/**
 * helper subset
 *
 * @since 2.1.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Kim Kha Nguyen
 */

function helper_subset()
{
	$registry = Redaxscript_Registry::instance();
	$helper = new Redaxscript_Helper($registry);
	echo $helper->getSubset();
}

/**
 * migrate constants
 *
 * @since 2.1.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Henry Ruhs
 * @author Gary Aylward
 *
 * @return array
 */

function migrate_constants()
{
	/* get user constants */

	$constants = get_defined_constants(true);
	$constants_user = $constants['user'];

	/* process constants user */

	foreach ($constants_user as $key => $value)
	{
		/* transform to camelcase */

		$key = mb_convert_case($key, MB_CASE_TITLE);
		$key[0] = strtolower($key[0]);

		/* remove underline */

		$key = str_replace('_', '', $key);

		/* store in array */

		$output[$key] = $value;
	}
	return $output;
}

/**
 * shortcut
 *
 * @since 2.2.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Henry Ruhs
 *
 * @param string $name
 *
 * @return string
 */

function l($name = null)
{
	$registry = Redaxscript_Registry::instance();
	$language = new Redaxscript_Language($registry);
	$output = $language->init($name);
	return $output;
}

/**
 * login list
 *
 * @since 2.2.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Gary Aylward
 */

function login_list()
{
	$registry = Redaxscript_Registry::instance();
	$login = new Redaxscript_Navigation_Login($registry);
	echo $login->get();
}

/**
 * templates list
 *
 * @since 2.2.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Gary Aylward
 */

function templates_list($options = array())
{
	$directory = New Redaxscript_Directory('templates', array(
		'admin',
		'install'
	));
	$registry = Redaxscript_Registry::instance();
	$navigation = new Redaxscript_Navigation_Directory($registry, 'template', $options, $directory);
	echo $navigation->get();
}

/**
 * languages list
 *
 * @since 2.2.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Gary Aylward
 */

function languages_list($options = array())
{
	$directory = New Redaxscript_Directory('languages', 'misc.php');
	$registry = Redaxscript_Registry::instance();
	$navigation = new Redaxscript_Navigation_Directory($registry, 'language', $options, $directory);
	echo $navigation->get();
}

/**
 * navigation list
 *
 * @since 2.2.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Gary Aylward
 */

function navigation_list($table = '', $options = array())
{
	$registry = Redaxscript_Registry::instance();
	$navigation = new Redaxscript_Navigation_Table($registry, $table, $options);
	echo $navigation->get();
}

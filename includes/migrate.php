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
	$registry = Redaxscript\Registry::getInstance();
	$language = Redaxscript\Language::getInstance();
	$breadcrumb = new Redaxscript\Breadcrumb($registry, $language);
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
	$registry = Redaxscript\Registry::getInstance();
	$helper = new Redaxscript\Helper($registry);
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
	$registry = Redaxscript\Registry::getInstance();
	$helper = new Redaxscript\Helper($registry);
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
 * language shortcut
 *
 * @since 2.2.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Henry Ruhs
 *
 * @param string $key
 * @param string $index
 *
 * @return string
 */

function l($key = null, $index = null)
{
	$language = Redaxscript\Language::getInstance();
	$output = $language->get($key, $index);
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
	$login = new Redaxscript\Navigation\Login(Redaxscript\Registry::getInstance(), Redaxscript\Language::getInstance());
	echo $login->getOutput();
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
	$directory = New Redaxscript\Directory('templates', array(
		'admin',
		'install'
	));
	$navigation = new Redaxscript\Navigation\Directory(Redaxscript\Registry::getInstance(), Redaxscript\Language::getInstance(), 'template', $options, $directory);
	echo $navigation->getOutput();
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
	$navigation = new Redaxscript\Navigation\Directory(Redaxscript\Registry::getInstance(), Redaxscript\Language::getInstance(), 'language', $options, $directory);
	echo $navigation->getOutput();
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
	$navigation = new Redaxscript\Navigation\Table(Redaxscript\Registry::getInstance(), Redaxscript\Language::getInstance(), $table, $options);
	echo $navigation->getOutput();
}

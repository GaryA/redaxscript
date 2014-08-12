<?php

/**
 * navigation abstract base class
 */

namespace Redaxscript;

abstract class Navigation
{
	/**
	 * instance of the registry class
	 *
	 * @var object
	 */

	protected $_registry;

	/**
	 * instance of the language class
	 *
	 * @var object
	 */

	protected $_language;

	/**
	 * array of options
	 *
	 * @var array
	 */

	protected $_options;

	/**
	 * instance of the directory class
	 *
	 * @var object
	 */

	protected $_directory;

	/**
	 * type of navigation list
	 *
	 * @var string
	 */

	protected $_type;

	/**
	 * formatted navigation list
	 *
	 * @var string
	 */

	protected $_output;

	protected $_navigationArray;

	/**
	 * constructor of the class
	 *
	 * @since 2.2.0
	 *
	 * @param Redaxscript_Registry $registry instance of the registry class
	 * @param Redaxscript_Language $language instance of the language class
	 * @param string $type type of directory object
	 * @param array $options array of options to control the list
	 * @param Redaxscript_Directory $directory instance of the directory class
	 */

	public function __construct(Redaxscript_Registry $registry, Redaxscript_Language $language, $type = '', $options = array(), Redaxscript_Directory $directory = null)
	{
		$this->_registry = $registry;
		$this->_language = $language;
		$this->_directory = $directory;
		$this->_type = $type;
		$this->_options = $options;
		$this->init();
	}

	/**
	 * init the class
	 *
	 * @since 2.2.0
	 */

	abstract public function init();

	/**
	 * get the navigation list
	 *
	 * @since 2.2.0
	 */

	public function getOutput()
	{
		return $this->_output;
	}

	/**
	 * build id string
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */

	protected function _buildIdString()
	{
		if (array_key_exists('id', $this->_options))
		{
			$idString = ' id="' . $this->_options['id'] . '"';
		}
		else
		{
			$idString = '';
		}
		return $idString;
	}

	/**
	 * build class string
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */

	protected function _buildClassString()
	{
		if (array_key_exists('class', $this->_options))
		{
			$classString = ' class="' . $this->_options['class'] . '"';
		}
		else
		{
			$classString = ' class="list-test-here' . $this->_type . '"';
		}
		return $classString;
	}
}

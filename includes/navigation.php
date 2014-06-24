<?php

/**
 * navigation abstract base class
 */

abstract class Redaxscript_Navigation
{
	/**
	 * instance of the registry class
	 *
	 * @var object
	 */

	protected $_registry;

	protected $_options;

	protected $_directory;

	protected $_type;

	/**
	 * formatted navigation list
	 *
	 * @var string
	 */

	protected $_output;

	/**
	 * constructor of the class
	 *
	 * @since 2.2.0
	 *
	 * @param Redaxscript_Registry $registry instance of the registry class
	 * @param string $type type of directory object
	 * @param array $options array of options to control the list
	 * @param Redaxscript_Directory $directory instance of the directory class
	 */

	public function __construct(Redaxscript_Registry $registry, $type = '', $options = array(), Redaxscript_Directory $directory = null)
	{
		$this->_registry = $registry;
		$this->_directory = $directory;
		$this->_type = $type;
		$this->_options = array(0 => $options, 1 => $options);
		$this->init();
	}

	/**
	 * init the class
	 *
	 * @since 2.2.0
	 *
	 * @param int $iteration
	 */

	abstract public function init($iteration = 0);

	/**
	 * get the navigation list
	 *
	 * @since 2.2.0
	 */

	public function get()
	{
		return $this->_output;
	}
}

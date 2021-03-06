<?php

/**
 * parent class to handle directories in the filesystem
 *
 * @since 2.0.0
 *
 * @category Directory
 * @package Redaxscript
 * @author Henry Ruhs
 * @author Gary Aylward
 */

class Redaxscript_Directory
{
	/**
	 * name of the directory
	 *
	 * @var string
	 */

	private $_directory;

	/**
	 * array of directories
	 *
	 * @var array
	 */

	private $_directoryArray;

	/**
	 * array of files to exclude
	 *
	 * @var array
	 */

	protected $_exclude = array(
		'.',
		'..'
	);

	/**
	 * local cache for directories
	 *
	 * @var array
	 */

	private static $_cache;

	/**
	 * constructor of the class
	 *
	 * @since 2.0.0
	 *
	 * @param string $directory name of the directory
	 * @param string|array $exclude files to exclude
	 */

	public function __construct($directory = null, $exclude = array())
	{
		$this->_directory = $directory;

		/* handle exclude */

		if (!is_array($exclude))
		{
			$exclude = array(
				$exclude
			);
		}
		$this->_exclude = array_unique(array_merge($this->_exclude, $exclude));

		/* call init */

		$this->init();
	}

	/**
	 * init the class
	 *
	 * @since 2.0.0
	 */

	public function init()
	{
		/* scan directory */

		$this->_directoryArray = $this->_scan($this->_directory);
	}

	/**
	 * get the directory
	 *
	 * @since 2.0.0
	 *
	 * @param integer $key item from the directory
	 * @return string|array
	 */

	public function get($key = null)
	{
		/* return single value */

		if (array_key_exists($key, $this->_directoryArray))
		{
			return $this->_directoryArray[$key];
		}

		/* else return array */

		else
		{
			return $this->_directoryArray;
		}
	}

	/**
	 * scan a directory
	 *
	 * @since 2.0.0
	 *
	 * @param string $directory name of the directory
	 * @return array
	 */

	protected function _scan($directory = null)
	{
		/* use from static cache */

		if (is_array(self::$_cache) && array_key_exists($directory, self::$_cache))
		{
			$directoryArray = self::$_cache[$directory];
		}

		/* else scan directory */

		else
		{
			$directoryArray = scandir($directory);

			/* save to static cache */

			self::$_cache[$directory] = $directoryArray;
		}
		$directoryArray = array_values(array_diff($directoryArray, $this->_exclude));
		return $directoryArray;
	}

	/**
	 * create a directory
	 *
	 * @since 2.1.0
	 *
	 * @param string $directory name of the directory
	 * @param integer $mode file access mode
	 * @return boolean
	 */

	public function create($directory = null, $mode = 0777)
	{
		$path = $this->_directory . '/' . $directory;
		$output = false;

		/* check path does not exist */

		if (!file_exists($path))
		{
			mkdir($path, $mode);

			/* validate directory was created */

			if (is_dir($path))
			{
				$output = true;
			}
		}
		return $output;
	}

	/**
	 * remove a directory
	 *
	 * @since 2.0.0
	 *
	 * @param string $directory name of the directory
	 */

	public function remove($directory = null)
	{
		/* handle parent directory */

		if (!$directory)
		{
			$path = $this->_directory;
			$directoryArray = $this->_directoryArray;
		}

		/* else handle child directory or single file */

		else
		{
			$path = $this->_directory . '/' . $directory;
			if (is_dir($path))
			{
				$directoryArray = $this->_scan($path);
			}
			else
			{
				$directoryArray = array();
			}
		}

		/* walk directory array */

		foreach ($directoryArray as $children)
		{
			$childrenPath = $path . '/' . $children;

			/* remove if directory */

			if (is_dir($childrenPath))
			{
				$this->remove($directory . '/' . $children);
			}

			/* else unlink file */

			else if (is_file($childrenPath))
			{
				unlink($childrenPath);
			}
		}

		/* remove if directory */

		if (is_dir($path))
		{
			rmdir($path);
		}

		/* else unlink file */

		else if (is_file($path))
		{
			unlink($path);
		}
	}
}
?>

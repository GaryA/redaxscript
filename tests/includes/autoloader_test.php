<?php

/**
 * Redaxscript Autoloader Test
 *
 * @since 2.1.0
 *
 * @package Redaxscript
 * @category Tests
 * @author Henry Ruhs
 */

class Redaxscript_Autoloader_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * testDirectory
	 *
	 * @since 2.1.0
	 */

	public function testDirectory()
	{
		/* setup */

		$autoloader = new Redaxscript_Autoloader;
		$autoloader::init('includes');

		/* result */

		$result = $this->readAttribute($autoloader, '_directory');

		/* compare */

		$this->assertEquals('includes', $result);
	}

	/**
	 * testAutoloader
	 *
	 * @since 2.1.0
	 */

	public function testAutoloader()
	{
		/* result */

		$result = spl_autoload_functions();

		/* compare */

		$this->assertInternalType('array', $result);
	}
}
?>

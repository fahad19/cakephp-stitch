<?php

App::uses('Stitch', 'Stitch.Lib');

class StitchTest extends CakeTestCase {

	public function testAll() {
		$pluginPath = App::pluginPath('Stitch');
		$config = array(
			'paths' => array(
				$pluginPath . 'Test/Fixture/package1',
			),
			'vendors' => array(
				$pluginPath . 'Test/Fixture/vendors/vendor.js',
			),
			'write' => $pluginPath . 'Test/Fixture/app.js',
		);

		if (file_exists($config['write'])) {
			unlink($config['write']);
		}

		$package = new Stitch($config);

		$this->assertArrayHasKey('identifier', $package->config);
		$this->assertArrayHasKey('vendors', $package->config);
		$this->assertArrayHasKey('paths', $package->config);
		$this->assertArrayHasKey('write', $package->config);

		$moduleName = $package->moduleName('/full/path/to/file.js', '/full/path/');
		$this->assertEqual($moduleName, 'to/file');

		$compilerClass = $package->compilerClass('/full/path/to/file.js');
		$this->assertEqual($compilerClass, 'StitchCompilerJs');

		$js = $package->run(true);
		$this->assertContains('I am a vendor', $js);
		$this->assertContains('foo/bar.js', $js);

		$package->config['paths']['/full/path/to/MyPlugin/'] = array(
			'prefix' => 'my_plugin',
		);
		$moduleName = $package->moduleName('/full/path/to/MyPlugin/foo.js', '/full/path/to/MyPlugin/');
		$this->assertEqual($moduleName, 'my_plugin/foo');
	}

}

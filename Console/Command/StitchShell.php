<?php

App::uses('AppShell', 'Console/Command');
App::uses('Stitch', 'Stitch.Lib');

/**
 * Stitch Shell
 *
 * @category Shell
 * @package  Stitch
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     https://github.com/fahad19/cakephp-stitch
 */
class StitchShell extends AppShell {

	public function initialize() {

	}

	public function main() {
		$this->out('##############################################################');
		$this->out('CakePHP Stitch plugin for serving JavaScript files as CommonJS');
		$this->out('##############################################################');
		$this->out(' ');

		$this->out('Usage:');
		$this->out('------');
		$this->out('');
		
		$this->out('Compile and write to a file');
		$this->out('  $ ./Console/cake Stitch.stitch run');
		$this->out('');
	}

	public function run() {
		$package = new Stitch(Configure::read('Stitch'));
		$package->run();
		$this->out('File written at: ' . Configure::read('Stitch.write'));
	}

}
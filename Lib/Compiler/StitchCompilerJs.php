<?php

/**
 * Bower Shell
 *
 * @category Lib
 * @package  Stitch.Lib.Compiler
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     https://github.com/fahad19/cakephp-stitch
 */

class StitchCompilerJs {

	public function compile($path) {
		return file_get_contents($path);
	}

}
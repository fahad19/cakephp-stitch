<?php

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * Stitch
 *
 * @category Lib
 * @package  Stitch.Lib
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     https://github.com/fahad19/cakephp-stitch
 */
class Stitch {

	public $config = array();

	public function __construct($config = array()) {
		$this->config = Set::merge($this->getDefaults(), $config);
	}

	public function getDefaults() {
		$defaults = array(
			'identifier' => 'require',
			'paths' => array(),
			'vendors' => array(),
			'write' => WEBROOT_DIR . '/js/app.js',
		);
		return $defaults;
	}

	public function run($return = false) {
		$paths = $this->getPaths();
		$filesByPath = $this->getFilesByPath();

		$out = '';
		$i = 0;
		$modules = array();
		foreach ($filesByPath as $path => $files) {
			foreach ($files as $file) {
				$compilerClass = $this->compilerClass($file);
				if (!$compilerClass) {
					continue;
				}
				$module = $this->moduleName($file, $path, $paths);
				$modules[] = $module;

				if ($i > 0) {
					$out .= ',';
				}
				$out .= '"' . $module . '": function(exports, require, module) {' . $this->compile($file) . '}';
				$i++;
			}
		}

		$vendorsJs = '';
		foreach ($this->config['vendors'] as $vendor) {
			if (file_exists($vendor)) {
				$vendorsJs .= file_get_contents($vendor);
			}
		}

		$js = $this->js();
		$js = str_replace('{{IDENTIFIER}}', $this->config['identifier'], $js);
		$js = str_replace('{{MODULES}}', $out, $js);

		$app = $vendorsJs . $js;
		if ($return) {
			return $app;
		}
		$this->write($app);
	}

	public function js() {
		$out = <<<EOF
(function(/*! Stitch !*/) {
  if (!this.{{IDENTIFIER}}) {
    var modules = {}, cache = {}, require = function(name, root) {
      var path = expand(root, name), module = cache[path], fn;
      if (module) {
        return module.exports;
      } else if (fn = modules[path] || modules[path = expand(path, './index')]) {
        module = {id: path, exports: {}};
        try {
          cache[path] = module;
          fn(module.exports, function(name) {
            return require(name, dirname(path));
          }, module);
          return module.exports;
        } catch (err) {
          delete cache[path];
          throw err;
        }
      } else {
        throw 'module \'' + name + '\' not found';
      }
    }, expand = function(root, name) {
      var results = [], parts, part;
      if (/^\.\.?(\/|$)/.test(name)) {
        parts = [root, name].join('/').split('/');
      } else {
        parts = name.split('/');
      }
      for (var i = 0, length = parts.length; i < length; i++) {
        part = parts[i];
        if (part == '..') {
          results.pop();
        } else if (part != '.' && part != '') {
          results.push(part);
        }
      }
      return results.join('/');
    }, dirname = function(path) {
      return path.split('/').slice(0, -1).join('/');
    };
    this.{{IDENTIFIER}} = function(name) {
      return require(name, '');
    }
    this.{{IDENTIFIER}}.define = function(bundle) {
      for (var key in bundle)
        modules[key] = bundle[key];
    };
  }
  return this.{{IDENTIFIER}}.define;
}).call(this)({
	{{MODULES}}
});
EOF;
		return $out;
	}

	public function compilerClass($file) {
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		$class = 'StitchCompiler' . Inflector::camelize($ext);
		$classFile = App::pluginPath('Stitch') . 'Lib' . DS . 'Compiler' . DS . $class . '.php';
		if (file_exists($classFile)) {
			if (!class_exists($classFile)) {
				App::uses($class, 'Stitch.Lib/Compiler');
			}
			return $class;
		}
		return false;
	}

	public function moduleName($file, $base, $paths = null) {
		if (!$paths) {
			$paths = $this->getPaths();
		}

		if (substr($base, -1) != '/') {
			$base .= '/';
		}
		$module = str_replace($base, '', $file);
		$moduleE = explode('.', $module);
		$ext = array_pop($moduleE);
		$module = implode('.', $moduleE);

		if (isset($paths[$base]['prefix'])) {
			$module = $paths[$base]['prefix'] . '/' . $module;
		}

		return $module;
	}

	public function compile($path) {
		$compilerClass = $this->compilerClass($path);
		if (!class_exists($compilerClass)) {
			App::uses($compilerClass, 'Stitch.Lib.Compiler');
		}
		return $compilerClass::compile($path);
	}

	public function getFilesByPath() {
		$out = array();

		$paths = $this->getPaths();
		foreach ($paths as $path => $options) {
			$dir = new Folder($path);
			$out[$path] = $dir->findRecursive('.*');
		}
		return $out;
	}

	public function write($source) {
		$file = new File($this->config['write'], true, 0644);
		$file->write($source);
	}

	public function getPaths($paths = null) {
		if (!$paths) {
			$paths = $this->config['paths'];
		}

		$out = array();
		foreach ($paths as $k => $v) {
			if (is_string($v)) {
				$out[$v] = array();
			} else {
				$out[$k] = $v;
			}
		}
		return $out;
	}

}
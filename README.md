# CakePHP Stitch Plugin

Stitch your JavaScript files together and serve them in the browser as CommonJS.

This is a port of @sstephenson's [Stitch npm module](https://github.com/sstephenson/stitch).

## What is CommonJS?

Suppose you have two JavaScript files, under `app/webroot/js/` named `a.js` and `b.js`.

Content of `a.js`:

    var A = 'I am A';

    module.exports = A;

Now in `b.js`, we can `require()` A by doing this:

    var A = require('A');
    var B = 'I am B, btw';
    var C = require('another_folder_in_webroot_js/c');

    module.exports = B; // exporting is optional

## Requirements

Compatible with CakePHP 2.x

## Installation

Clone/Download this repository to `/app/Plugin/Stitch`, and load the plugin from `/app/Config/bootstrap.php`:

    CakePlugin::load('Stitch', array('bootstrap' => true));

## Configuration

See configuration values in `app/Config/Stitch/Config/bootstrap.php` file.

* **paths**: array of absolute paths where JavaScript files are located
* **vendors**: array of vendors that you want available globally, like jQuery or Underscore.js
* **write**: absolute path of file where to write the compiled JavaScript file

### Conflicting module IDs and prefixing

There would be cases when you have multiple paths, and end up having two or more same module IDs. In those cases, you can consider prefixing your module IDs.

For example, your paths include `/app/webroot/js/` and `/app/Plugin/MyPlugin/webroot/js`, and they both have `foo.js` under their respective js directories. Then you can prefix your plugin's path like this:

    Configure::write('Stitch.paths', array(
        '/app/webroot/js/',
        '/app/Plugin/MyPlugin/webroot/js/' => array(
            'prefix' => 'my_plugin',
        ),
    ));

Now in the browser, you can require() the files separately:

    require('foo');
    require('my_plugin/foo');

## Usage

At the moment, Stitch can perform the compiling via shell based on the configuration found in bootstrap:

    $ ./Console/cake Stitch.stitch run

The shell basically runs the compiler found in a separate Stitch class which is available as a library, and you are free to implement its functionality in various ways. For example, a new StitchHelper utilizing this library eliminating the need of running it via shell.

See unit tests for understanding more on how the library works.

## Extending

Stitch was written for handling JavaScript files only, files that end with `.js` as extensions. But you can extend it for supporting various extensions, like `.underscore` for Underscore.js templates and `.coffee` for CoffeeScript files. All you need to do is add new classes under `/app/Plugin/Stitch/Lib/Compiler/` and it will automatically map them based of file extensions.

### Naming convention for compiler classes

Compiler class names begin with StitchCompiler and the suffix is the camelized version of file extension.

* **.js**: StitchCompilerJs.php
* **.coffee**: StitchCompilerCoffee.php

## License

[MIT License](https://github.com/fahad19/cakephp-stitch/blob/master/LICENSE.txt)
<?php
spl_autoload_register(function($class) {
	$source = array(
		'src',
		'ext/CurlWrapper/src',
	);
	foreach ($source as $dir) {
		$file = __DIR__.'/'.$dir.'/'.str_replace('\\', '/', $class).'.php';
		if (is_file($file)) {
			require_once($file);
		}
	}
});

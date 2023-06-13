<?php 
	// check ajax/axios call (axios is giving an object!)
	$input = json_decode(file_get_contents('php://input'));
	if (!empty($input) && is_object($input) && isset($input->action)) {// && $_SERVER['HTTP_REFERER'] contain ?eatable.ch?;

	// start session & set global mysql config
	session_start();
	$GLOBALS['config'] = array(
		'mysql' => array(
			'host' => 'localhost',
			'dbuser' => 'root',
			'password' => '12345678',
			'db' => 'eatable_dev'
		)
	);

	// localization
	setlocale(LC_TIME, 'de_DE', 'deu', 'german');

	// make api work outside of root
	$GLOBALS['base'] = '';
	$GLOBALS['doc_root'] = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['base'];
	$GLOBALS['path'] = '//' . $_SERVER['HTTP_HOST'] . $GLOBALS['base'];
	set_include_path($GLOBALS['doc_root']);

	// load classes...
	spl_autoload_register(function($class) {
		require_once $GLOBALS['doc_root'] . '/classes/' . $class . '.php';
	});

	// load functions...
	require_once $GLOBALS['doc_root'] . '/functions/functions.php';

	// load ajax.php file if request is ajax call
	if ($input->action && is_object($input)) {
		include $GLOBALS['doc_root'] . '/inc/ajax.php';
	}
}
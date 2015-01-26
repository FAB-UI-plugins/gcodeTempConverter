<?php
require_once $_SERVER ['DOCUMENT_ROOT'] . '/fabui/ajax/config.php';
require_once $_SERVER ['DOCUMENT_ROOT'] . '/fabui/ajax/lib/utilities.php';

$_method = strtolower ( $_POST ['method'] );
$_configFile = "/var/www/fabui/application/plugins/gcodeTempConverter/assets/config/config.json"; // $_POST['configFile'];


// shell_exec('sudo chmod 666 '.$_configFile);

if ($_method == 'write') {
	$_jsonString = $_POST ['jsonString'];
	
	/**
	 * Write config file*
	 */
	if (!$myfile = fopen ( $_configFile, "w" )) {
		shell_exec('sudo chmod 666 '.$_configFile);
		$myfile = fopen ( $_configFile, "w" ) or die ( "Unable to open file!" . $_configFile );
	}

	fwrite ( $myfile, $_jsonString );
	fflush ( $myfile );
	fclose ( $myfile );
} elseif ($_method == 'read') {
	

	
	$rows = json_decode ( file_get_contents ( $_configFile ) );
	

	
	header ( 'Content-Type: application/json' );
	echo minify ( json_encode ( array (
			'data' => $rows 
	) ) );
}

?>

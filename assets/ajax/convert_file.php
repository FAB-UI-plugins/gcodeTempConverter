<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/fabui/ajax/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/fabui/ajax/lib/database.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/fabui/ajax/lib/utilities.php';

$_file_id      = $_POST['file_id'];
$_file_path    = $_POST["file_path"];

$_note         = urldecode($_POST["note"]);
$_name         = urldecode($_POST["name"]);
$_bed_temp_first = $_POST['bed_temp_first'];
$_bed_temp       = $_POST['bed_temp'];
$_ext_temp_first = $_POST['ext_temp_first'];
$_ext_temp 		 = $_POST['ext_temp'];


/** SAVE FILE */


$command = 'sudo python /var/www/fabui/application/plugins/gcodeTempConverter/assets/python/tempUpdater.py '.$_bed_temp_first.' '.$_bed_temp.' '.$_ext_temp_first.' '.$_ext_temp.' '.$_file_path;
// echo $command;
// return ;

$response = shell_exec($command);


$_file_size = filesize($_file_path);

$_response_items['success']   = true;
$_response_items['file_size'] = $_file_size;
$_response_items['command'] = $command;
// $_response_items['shell'] = $response;
// $_response_items['whoami'] = trim(shell_exec('whoami'));

/** GET TYPE OF PRINT */
$_print_type = print_type($_file_path);

/** SAVE NEW SIZE TO DB */
$db = new Database();

/** UPDATE DATA INFO */
$_data_update['file_size']  = $_file_size;
$_data_update['print_type'] = $_print_type;
$_data_update['note']       = $_note;
$_data_update['raw_name']   = $_name;

$db->update('sys_files', array('column' => 'id', 'value' => $_file_id, 'sign' => '='), $_data_update);
$db->close();


/** GCODE ANALYZER */
shell_exec('sudo php /var/www/fabui/script/gcode_analyzer.php '.$_file_id.' > /dev/null & echo $!');

/** JSON RESPONSE */
header('Content-Type: application/json');
echo minify(json_encode($_response_items));
 


?>
<?php
/*
 * This file is for saving photo
 *
 */

require "Connection.php";
require "JsonService.php";

$db = new Connection();
$JS = new JsonService();

if ($db === FALSE) {
	return $JS->response("No DB connection", [], 0, 'error');
}

// CHECK IF THE FILE AND ID WHERE POSTED
if(!$_FILES['photo']) return $JS->response("You should POST a photo", [], 0, 'error');
if(!$_POST['id']) return $JS->response("You should a person id", [], 0, 'error');

// ARRANGE PATH AND FILENAME
$expName = explode(".", $_FILES['photo']['name']);
$filename = strtotime("now").$_POST['id'].".".end($expName);
$target = "files/". $filename;
$id=$_POST['id'];
// CHECK IF PERSON EXIST
if(!$db->personExist($id)) return $JS->response("Person not exist", [], 0, 'error');

// MOVE FILE TO DESIGNED PATH
if(move_uploaded_file( $_FILES['photo']['tmp_name'], $target)) {
	$data = new stdClass;
	$data->photo = $filename;
	$data->id = $id;
	if($db->setPhoto($data)) return $JS->response("Fileuploaded");
}

return $JS->response("Error uploading file", [], 0, 'error');

?>
<?php

require "Connection.php";
require "JsonService.php";

$db = new Connection();
$JS = new JsonService();

if ($db === FALSE) {
	return $JS->response("No DB connection", [], 0, 'error');
}

$json = file_get_contents('php://input');

// CONVERT IT INTO A PHP OBJECT
$data = json_decode($json);

if(!$data) return $JS->response("You should POST data as JSON", [], 0, 'error');

if(!isset($data->name)||!isset($data->surnames)) return $JS->response("You should POST at least name and surnames as JSON", [], 0, 'error');

if(!isset($data->contact)) {
	$data->contact = [];
}

$info = $db->setData($data);
if($info) return $JS->response("return info", ["id"=>$info]);

return $JS->response("Person not saved", [], 0, 'error');
?>
<?php

require "Connection.php";
require "JsonService.php";

$db = new Connection();
$JS = new JsonService();

if ($db === FALSE) {
	return $JS->response("No DB connection", [], 0, 'error');
}

$gets = $_GET;

if(!$gets)  return $JS->response("getBy only works with params like: name, surnames or value (of contact)", [], 0, 'error');

// CHECK IF POSTED VALUES ARE VALID ONE
$values = ["name", "surnames", "value"];
foreach ($gets as $key => $value) {
	if(!in_array($key, $values)) return $JS->response("getBy only works with params like: name, surnames or value (of contact)", [], 0, 'error');
}
$info = $db->getDataBy($gets);

return $JS->response("return info", $info);

?>
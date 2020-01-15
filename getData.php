<?php

require "Connection.php";
require "JsonService.php";

$db = new Connection();
$JS = new JsonService();

if ($db === FALSE) {
	return $JS->response("No DB connection", [], 0, 'error');
}

$info = $db->getData();

return $JS->response("return info", $info);

?>
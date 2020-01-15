<?php

/**
 * This file is for connect database
 * REMEMBER to change the user, password and database variables
 */
class Connection
{

	private static $servername = "localhost";
	private static $username = "braian";
	private static $password = "password";
	private static $dbname = "agenda";
	private $conn;
	
	function __construct()
	{
		
		// CREATE CONNECTION
		$this->conn = new mysqli(self::$servername, self::$username, self::$password, self::$dbname);

		// CHECK CONNECTION
		if ($this->conn->connect_error) {
			return FALSE;
		}

		return TRUE;
	}

	public function personExist ($id) 
	{
		// QUERY TO GET INFORMATION
		$query = "SELECT * FROM agenda.person WHERE id = ".$id;
		$result = $this->conn->query($query);
		if ($result->num_rows > 0) return TRUE;
		
		return FALSE;
	}

	public function getdata () 
	{
		// QUERY TO GET INFORMATION
		$query = "SELECT * FROM agenda.person";
		$result = $this->conn->query($query);
		$info = [];
		// IF THERE IS SOME INFORMATION
		if ($result->num_rows > 0) {
			// FIND CONTACT INFO AND ADD IT INSIDE CONTACT
		    while($row = $result->fetch_assoc()) {
				$query = "SELECT * FROM agenda.contact JOIN contact_type on contact.contact_type_id = contact_type.id WHERE person_id = ".$row['id'];
				$res = $this->conn->query($query);
				$row['contact'] = [];
				if($res->num_rows > 0) {
					while($r = $res->fetch_assoc()) {
						array_push($row['contact'], $r);
					}
				}
		    	array_push($info, $row);
		    }
		}
		$this->conn->close();
		return $info;
	}

	public function getDataBy ($data) 
	{
		// GENERATE THE WHERE QUERY DEPENDING ON THE INFORMATION
		$inside_where = '';
		if(isset($data['value'])) {
			$inside_where = " and value like '%".$data['value']."%'";
		}
		$where = '';
		if(isset($data['name'])) {
			$where = "where name like '%".$data['name']."%'";
		}
		if(isset($data['surnames'])) {
			if($where == '') {
				$where = "where ";
			} else {
				$where .= " and ";
			}
			$where .= "surnames like '%".$data['surnames']."%'";
		}
		$query = "SELECT * FROM agenda.person $where";

		$result = $this->conn->query($query);
		$info = [];
		if ($result && $result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$query = "SELECT * FROM agenda.contact JOIN contact_type on contact.contact_type_id = contact_type.id WHERE person_id = ".$row['id'].$inside_where;
				$res = $this->conn->query($query);
				$row['contact'] = [];
				if($res->num_rows > 0) {
					while($r = $res->fetch_assoc()) {
						array_push($row['contact'], $r);
					}
				}
				if(count($row['contact'])) array_push($info, $row);
		    }
		}
		$this->conn->close();
		return $info;
	}

	public function setData ($obj)
	{
		// BEGIN TRANSACTION TO INSERT PERSON INFORMATION AND CONTACT INFORMATION
		$this->conn->begin_transaction();
		$insert1 = $this->conn->query("INSERT INTO `person`(`name`, `surnames`) VALUES ('".$obj->name."', '".$obj->surnames."')");
		$last_id = false;
		if($insert1) {
			$last_id = $this->conn->insert_id;
			foreach ($obj->contact as $v) {
				$insert2 = $this->conn->query("INSERT INTO `contact`(`person_id`, `contact_type_id`, `value`) VALUES (".$last_id.",".$v->contact_type_id.",'".$v->value."')");
				if($insert2===false) {
					$last_id = false;
					break;
				}
			}
		}
		// COMMIT OR ROLL BACK DEPENDING IF THERE IS AN ERROR
		if($last_id) {
		    $this->conn->commit();
		} else {
		    $this->conn->rollback();
		}
		$this->conn->close();
		return $last_id;
	}

	public function setPhoto ($obj)
	{
		// UPDATE PERSON TO SET THE ID
		$this->conn->begin_transaction();
		$insert1 = $this->conn->query("UPDATE `person` SET `photo` = '".$obj->photo."' WHERE `id` = ".$obj->id);
		if($insert1) {
		    $this->conn->commit();
		} else {
		    $this->conn->rollback();
		}
		$this->conn->close();
		return $insert1;
	}
}
?>
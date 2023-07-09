<?php

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}


function check_verified()
{

	$id = $_SESSION['id'];
	$query = "select * from users where id = '$id' limit 1";
	$row = database_run($query);

	if (is_array($row)) {
		$row = $row[0];

		if ($row->Email == $row->verified) {

			return true;
		}
	}

	return false;
}
function database_run($query, $vars = array())
{
	$string = "mysql:host=localhost;dbname=db";
	$con = new PDO($string, 'root', '');

	if (!$con) {
		return false;
	}

	$stm = $con->prepare($query);
	$check = $stm->execute($vars);

	if ($check) {

		$data = $stm->fetchAll(PDO::FETCH_OBJ);

		if (count($data) > 0) {
			return $data;
		}
	}

	return false;
}

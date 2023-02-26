<?php

include __DIR__ . "/../../vendor/autoload.php";
use Firebase\JWT\JWT;

require_once __DIR__ . "/../database/db.php";

define('KEY', '6fe6f886d2148c5d97e4bfc0741fc218');

function generate_data_token($userID, $userEmail)
{

	$time = time();
	$token = array(
		"iat" => $time,
		"exp" => $time + (60*60), //1 hour
		"data" => [
			"id"=> $userID,
			"email"=> $userEmail
		]
	);
	
	return $token;
}

function generate_jwt_token($data_token)
{
	$jwt = JWT::encode($data_token, KEY, 'HS256');
	return $jwt;
}

function validate_token_and_role($token, $roles)
{
	$valid = false;
	$message = "";
	try {
		$query = "SELECT id,password FROM users WHERE token = :token";
		$stmt = $con->prepare($query);
		$stmt->bindValue(':token', $token);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $exception) {
		$valid = false;
		$message = "db connection error";
	}

	if ($row) {
		$time = time();
		if ($row['token_exp'] > $time){
			if (in_array($row['role'], $roles)) {
				$valid = true;
			}
			else{
				$valid = false;
				$message = "You do not have sufficient permissions to perform this action";
			}
		}
		else{
			$valid = false;
			$message = "Expired Token";
		}
	}
	} else {
		$valid = false;
		$message = "Invalid Token";
	}
	$res = ["valid" => $valid, "message" => $message];
	return $res;
}

<?php

include __DIR__ . "/../../vendor/autoload.php";
use Firebase\JWT\JWT;

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

function validate_token($token)
{
	$valid = "";
	return $valid;
}

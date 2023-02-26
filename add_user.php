<?php

require_once("utils/headers.php");

$headers = apache_request_headers();
$error = false;
$permit_roles = ['alto','alto_medio','medio_alto'];


if (isset($headers['Authorization'])) {

    require_once("utils/token.php");

    $token = $headers['Authorization'];

    $validation = validate_token_and_role($token, $permit_roles);

    if(!$validation["valid"]){
		$error = true;
		$message = $validation["message"];
    }
	    
	if (isset($_POST['name']) && $_POST['name'] <> '' && isset($_POST['email']) && $_POST['email'] <> '' && isset($_POST['password']) && $_POST['password'] <> '' && isset($_POST['lastname']) && isset($_POST['role']) && $_POST['role'] <> '') {
		require_once("database/db.php");
		
		$name = $_POST['name'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$role = $_POST['role'];

		try {
			$query = "SELECT id FROM users WHERE email = :email";
			$stmt = $con->prepare($query);
			$stmt->bindValue(':email', $email);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $exception) {
			$error = true;
			$message =  $exception->getMessage();
		}

		if ($row) {
			$error = true;
			$message = "email already exist";
		} else {
			$hashed_password = password_hash($password, PASSWORD_BCRYPT);

			try {

				$query = "INSERT INTO users (name,lastname,email,password,role) VALUES (:name,:lastname,:email,:password,:role)";
				$stmt = $con->prepare($query);

				$stmt->bindValue(':name', $name);
				$stmt->bindValue(':lastname', $name);
				$stmt->bindValue(':email', $email);
				$stmt->bindValue(':password', $hashed_password);
				$stmt->bindValue(':role', $role);


				if ($stmt->execute()) {

				$user['id']   = $con->lastInsertId();
				$user['name']   = $name;
				$user['email']   = $email;

				$data = $user;
				} else {
				$error = true;
				$message = "DB Error Login Failed!";
				}
			} catch (PDOException $exception) {
				$error = true;
				$message =  $exception->getMessage();
			}
		}
	} else {
		$error = true;
		$message = "Missing data";
	}
} else {
	$error = true;
    $message = "Authorization required!";
}
/* -------------------------------------------------------------------------- */
/*                                  Response                                  */
/* -------------------------------------------------------------------------- */
if ($error) {
    $out = json_encode(array(
        "status"   => "error",
        "message"   => $message
    ));
} else {
    $out = json_encode(array(
        "status"   => "success",
        "data"   => $data
    ));
}
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo $out;

<?php
$error = false;

if (isset($_POST['email']) && $_POST['email'] <> '' && isset($_POST['password']) && $_POST['password'] <> '') {

    require_once("database/db.php");
    require_once("utils/token.php");

    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $query = "SELECT id,password FROM users WHERE email = :email";
        $stmt = $con->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        $error = true;
        $message =  $exception->getMessage();
    }

    if ($row) {

        if (password_verify($password, $row['password'])) {
              $data_token = generate_data_token($row['id'], $row['email']);
		    $jwt = generate_jwt_token($data_token);
              try {
			    
			    $query = "UPDATE users SET token = :token ,token_exp = :token_exp WHERE id = :id";
			    $stmt = $con->prepare($query);
			    
			    $stmt->bindValue(':token', $jwt);
			    $stmt->bindValue(':token_exp', $data_token['exp']);
			    $stmt->bindValue(':id', $row['id']);
			    
			    
			    if ($stmt->execute()) {
				    $data = ['token' => $jwt];
                    } else {
                        $error = true;
                        $message = "Nothing inserted!";
                    }
                } catch (PDOException $exception) {
                    $error = true;
                    $message =  $exception->getMessage();
                }

        } else {
            $error = true;
            $message = "Wrong password!";
        }
    } else {
        $error = true;
        $message = "User doesn't exist";
    }
} else {
    $error = true;
    $message = "Missing data";
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

<?php
$error = false;

$headers = apache_request_headers();

if (isset($headers['Authorization'])) {

    require_once("utils/token.php");

    $token = $headers['Authorization'];

    if (validate_token($token)) {

        if (isset($_POST['name']) && $_POST['name'] <> '' && isset($_POST['email']) && $_POST['email'] <> '' && isset($_POST['password']) && $_POST['password'] <> '') {
            require_once("database/db.php");

            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];

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

                    $query = "INSERT INTO users (name,email,password) VALUES (:name,:email,:password)";
                    $stmt = $con->prepare($query);

                    $stmt->bindValue(':name', $name);
                    $stmt->bindValue(':email', $email);
                    $stmt->bindValue(':password', $hashed_password);


                    if ($stmt->execute()) {

                        $user['id']   = $con->lastInsertId();
                        $user['name']   = $name;
                        $user['email']   = $email;

                        $data = $user;
                    } else {
                        $error = true;
                        $message = "Nothing inserted!";
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
        $message = "Access Token is not valid or has expired";
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

<?php 
session_start();
require_once 'config/connect.php'; 
//password test string
//$test_password=preg_match_all('@[A-Z]@+@[a-z]@+@[0-9]@+@[^\w]@', trim($_POST["password"]));
if(isset($_POST) & !empty($_POST)){
	//sign up varidation
$username_err = $password_err = $confirm_password_err = "";

        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($connection, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
					$username_err= "This username is already taken.";
					mysqli_stmt_close($stmt);
					header("location: login.php?message=3");
                } 
            
        }
        
    }
    elseif(strlen(trim($_POST["password"])) < 6){
		$password_err = "Password must have atleast 6 characters.";
        header("location: login.php?message=4");
		if(empty($password_err) && ($password != $confirm_password)){
			$confirm_password_err = "Password did not match.";
         header("location: login.php?message=6");
        }
    }
if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
		$email = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);
	    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$sql = "INSERT INTO users (email, password) VALUES ('$email', '$password')";
	$result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
	if($result){
		$_SESSION['customer'] = $email;
		$_SESSION['customerid'] = mysqli_insert_id($connection);
		header("location: profile.php");
	}else{
		header("location: login.php?message=7");
	}
		
}
}
?>
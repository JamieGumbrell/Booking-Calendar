<?php

session_start();
 
//check if the user is already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
	//redirect to calendar
	header("location: calendar.php");
	exit;
}

require "../db_config.php"; 
 
$username = $email = $password = $confirm_password = $username_err = $email_err = $password_err = $confirm_password_err = "";
 
//set form as POST
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
	//validate username
	$username = trim($_POST["username"]);
	if(empty($username)){
			$username_err = "Please enter a username.";     
	}elseif(strlen($username) > 50){
			$username_err = "Please choose a shorter username."; 
	}else{
		$username = trim($_POST["username"]);
	}

	//validate email
	$email = trim($_POST["email"]);
	if(empty($email)){
			$email_err = "Please enter an email.";
	}elseif(strlen($email) > 320){
			$email_err = "Email too long";
	}else{
		//valid email
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$email_err = "Invalid email";
		}else{
			//check user doesn't exist
			$sql = "SELECT userid FROM users WHERE email = :email";
			
			if($stmt = $pdo_connection->prepare($sql)){
				$stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
				$param_email = $email;
				if($stmt->execute()){
						if($stmt->rowCount() == 1){
							//account already exists error
							$email_err = "This email already has an existing account.";
						}
				} else{
						$_SESSION['error'] = "There was an issue connecting to the database. Please try again later!";
						header("Location: /");
				}
			}
		}  
		//close statement
		unset($stmt);
	}
	
	//validate password
	$password = trim($_POST["password"]);
	if(empty($password)){
			$password_err = "Please enter a password.";     
	} elseif(strlen($password) < 6){
			$password_err = "Password must have at least 6 characters.";
	}
	
	//validate confirm password
	if(empty(trim($_POST["confirm_password"]))){
		$confirm_password_err = "Please confirm password.";     
	} else{
		$confirm_password = trim($_POST["confirm_password"]);
		if(empty($password_err) && ($password != $confirm_password)){
				$confirm_password_err = "Password did not match.";
		}
	}
	
	//if no errors
	if(empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)){
		//add user to database
		$sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
		 
		if($stmt = $pdo_connection->prepare($sql)){
			$stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
			$stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
			$stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
			
			$param_username = $username;
			$param_email = $email;
			$param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
			
			if($stmt->execute()){
				//success, redirect to login page
				header("location: login.php");
			} else{
				$_SESSION['error'] = "There was an issue connecting to the database. Please try again later!";
				header("Location: /");
			}
		}
		//close statement
		unset($stmt);
	}
	//close connection
	unset($pdo_connection);
}
include "templates/header.php" 
?>
	<!-- Register Page Content -->
	<div class="container border col-6 offset-3 mt-3">
		<h2 class="my-3">Create Account</h2>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		
			<!-- Username -->
			<div class="form-group row">
				<label class="col-3">Username</label>
				<input type="text" name="username" class="form-control col-6" value="<?php echo $username; ?>">
				<span class="col-3 text-danger"><?php echo $username_err; ?></span>
			</div> 
			
			<!-- Email -->
			<div class="form-group row">
				<label class="col-3">Email</label>
				<input type="text" name="email" class="form-control col-6" value="<?php echo $email; ?>">
				<span class="col-3 text-danger"><?php echo $email_err; ?></span>
			</div>    
			
			<!-- Password -->
			<div class="form-group row">
				<label class="col-3">Password</label>
				<input type="password" name="password" class="form-control col-6" value="<?php echo $password; ?>">
				<span class="col-3 text-danger"><?php echo $password_err; ?></span>
			</div>
			
			<!-- Confirm Password -->
			<div class="form-group row">
				<label class="col-3">Confirm Password</label>
				<input type="password" name="confirm_password" class="form-control col-6" value="<?php echo $confirm_password; ?>">
				<span class="col-3 text-danger"><?php echo $confirm_password_err; ?></span>
			</div>
			
			<!-- Register Button -->
			<div class="form-group row">
				<input type="submit" class="btn btn-primary col-6 offset-3" value="Create Account">
			</div>
			
			<!-- Sign in Button -->
			<p class="text-center">Already have an account? <a href="login.php">Login here</a>.</p>
		</form>
	</div>    
<?php include "templates/footer.php"  ?>
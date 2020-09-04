<?php
require "templates/session.php";
require "../db_config.php"; 
 
$old_password = $new_password = $confirm_password = $old_password_err = $new_password_err = $confirm_password_err = "";
 
//set form as POST
if($_SERVER["REQUEST_METHOD"] == "POST"){
	//validate credentials
	if(empty($password_err)){
		//check for valid password
		$sql = "SELECT password FROM users WHERE userid = ".$_SESSION["id"];
		
		if($stmt = $pdo_connection->prepare($sql)){
			if($stmt->execute()){
				if($row = $stmt->fetch()){
					$hashed_password = $row["password"];
					$password = trim($_POST["old_password"]);
					if(!password_verify($password, $hashed_password)){
						// Display an error message if password is not valid
						$old_password_err = "The password you entered was not valid.";
					}
				}
			} else{
				$_SESSION['error'] = "There was an issue connecting to the database. Please try again later!";
				header("Location: /");
			}
		}
		//close statement
		unset($stmt);
	}
 
	//validate new password
	if(empty(trim($_POST["new_password"]))){
		$new_password_err = "Please enter the new password.";     
	} elseif(strlen(trim($_POST["new_password"])) < 6){
		$new_password_err = "Password must have atleast 6 characters.";
	} else{
		$new_password = trim($_POST["new_password"]);
	}
	
	//validate confirm password
	if(empty(trim($_POST["confirm_password"]))){
		$confirm_password_err = "Please confirm the password.";
	} else{
		$confirm_password = trim($_POST["confirm_password"]);
		if(empty($new_password_err) && ($new_password != $confirm_password)){
			$confirm_password_err = "Password did not match.";
		}
	}
		 
	//run only if no errors
	if(empty($old_password_err) && empty($new_password_err) && empty($confirm_password_err)){
		//update passowrd in database
		$sql = "UPDATE users SET password = :password WHERE userid = ".$_SESSION["id"];
		
		if($stmt = $pdo_connection->prepare($sql)){
			$stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
			$param_password = password_hash($new_password, PASSWORD_DEFAULT);
			if($stmt->execute()){
				//destroy session and send user back to login
				session_destroy();
				header("location: login.php");
				exit();
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
	<!-- Login Page Content -->
	<div class="container border col-6 offset-3 mt-3">
		<h2 class="my-3">Reset Password</h2>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
			<!-- Old Password -->
			<div class="form-group row <?php echo (!empty($old_password_err)) ? 'has-error' : ''; ?>">
				<label class="col-3">Old Password</label>
				<input type="password" name="old_password" class="form-control col-6" value="<?php echo $old_password; ?>">
				<span class="help-block"><?php echo $old_password_err; ?></span>
			</div>
			<!-- New Password -->
			<div class="form-group row <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
				<label class="col-3">New Password</label>
				<input type="password" name="new_password" class="form-control col-6" value="<?php echo $new_password; ?>">
				<span class="help-block"><?php echo $new_password_err; ?></span>
			</div>
			<!-- Confirm New Password -->
			<div class="form-group row <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
				<label class="col-3">Confirm Password</label>
				<input type="password" name="confirm_password" class="form-control col-6">
				<span class="help-block"><?php echo $confirm_password_err; ?></span>
			</div>
			<!-- Submit Button -->
			<div class="form-group row">
				<input type="submit" class="btn btn-primary offset-3 col-6" value="Submit">
			</div>
			<!-- Cancel Button -->
			<a class="btn btn-link col-3 offset-3 col-6 pb-3" href="/">Cancel</a>
		</form>
	</div>    
<?php 
//include footer
include "templates/footer.php"  
?>
<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
		//redirect to calendar
    header("Location: calendar.php");
    exit;
}

require "../db_config.php"; 
 
$email = $password = $email_err = $password_err = "";
 
//set form as POST
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
	//Check for empty email
	if(empty(trim($_POST["email"]))){
			$email_err = "Please enter email.";
	} else{
			$email = trim($_POST["email"]);
	}
	
	//Check for empty password
	if(empty(trim($_POST["password"]))){
			$password_err = "Please enter your password.";
	} else{
			$password = trim($_POST["password"]);
	}
	
	//Validate credentials
	if(empty($email_err) && empty($password_err)){
			
		//Get data with matching email
		$sql = "SELECT userid, username, email, password FROM users WHERE email = :email";
		
		if($stmt = $pdo_connection->prepare($sql)){
		
			$stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
			$param_email = trim($_POST["email"]);
			
			//Attempt to execute
			if($stmt->execute()){
				//Check user exists
				if($stmt->rowCount() == 1){
					if($row = $stmt->fetch()){
						$id = $row["userid"];
						$username = $row["username"];
						$email = $row["email"];
						$hashed_password = $row["password"];
						
						//verify password
						if(password_verify($password, $hashed_password)){
								
							//start login session
							session_start();
							
							// Store data in session variables
							$_SESSION["loggedin"] = true;
							$_SESSION["id"] = $id;
							$_SESSION["username"] = $username;                            
							
							//redirect user to calendar page
							header("location: /calendar.php");
						} else{
							//invalid password error
							$password_err = "The password you entered was not valid";
						}
					}
				} else{
					//account not found error
					$email_err = "No account found with that email";
				}
			} else{
				$_SESSION['error'] = "There was an issue connecting to the database. Please try again later!";
				header("Location: /");
			}
		}
		// Close statement
		unset($stmt);
	}
	// Close connection
	unset($pdo_connection);
}
include "templates/header.php" 
?>

		<!-- Login Page Content -->
    <div class="container border col-6 offset-3 mt-3">
			<h2 class="my-3">Login</h2>
			<div class="">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<!-- Email -->
					<div class="form-group row">
						<label class="col-3">Email</label>
						<input type="text" name="email" class="form-control col-6" value="<?php echo $email; ?>">
						<span class="col-3 <?php echo (!empty($email_err)) ? 'text-danger' : ''; ?>"><?php echo $email_err; ?></span>
					</div>  
					<!--  -->					
					<div class="form-group row">
						<label class="col-3">Password</label>
						<input type="password" name="password" class="form-control col-6">
						<span class="col-3 <?php echo (!empty($password_err)) ? 'text-danger' : ''; ?>"><?php echo $password_err; ?></span>
					</div>
					<!-- Login Button -->	
					<div class="form-group row">
						<input type="submit" class="btn btn-primary col-6 offset-3" value="Login">
					</div>
					<!-- Signup Link -->	
					<p class="text-center">Don't have an account? <a href="register.php">Sign up now</a>.</p>
				</form>
			</div>
    </div>
		
<?php 
//include footer
include "templates/footer.php"  
?>
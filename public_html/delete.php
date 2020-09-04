<?php 
//test if get is set and delete button is pressed
if (!isset($_GET["bookingId"]) || !isset($_POST["delete"])) {
	//return to today
	header("Location:/calendar.php");
}

require "templates/session.php";
require "../db_config.php"; 

//prepare to delete booking
$bookingId = $_GET["bookingId"];
try {
		$connection = new PDO($dsn, $username, $password, $options);
		
		$sql = "SELECT userid FROM bookings WHERE bookingid=$bookingId"; 
		$statement = $connection->prepare($sql);
		$statement->execute();
		
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		
		//check user is valid
		if($_SESSION['id'] != $result['userid']){
			$_SESSION['error'] = "You don't have permission to delete that booking!";
			header("Location:/calendar.php");
			exit();
		}else{
			//remove row from database where bookingid = GET bookingId
			$sql = "DELETE FROM bookings WHERE bookingid = $bookingId";

			$statement = $connection->prepare($sql);
			$statement->bindValue(':id', $id);
			$statement->execute();
		}
	} catch(PDOException $error) {
		$_SESSION['error'] = "That booking doesn't exist!";
		header("Location:/calendar.php");
	}
	
//return to today
header("Location:/calendar.php");

?>
		
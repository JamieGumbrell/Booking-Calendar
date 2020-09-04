<?php 
//if no GET then send to home
if(!isset($_GET['bookingId']) || empty($_GET['bookingId'])){
	header("Location: calendar.php");
}

require "templates/session.php";
require "../db_config.php"; 
require "templates/setting_values.php";

//default variables for start and end times
date_default_timezone_set("Australia/Canberra");
$d = isset($_GET['day']) ? $_GET['day'] : date('Y-m-d');
$start = date(strtotime($d." ".$calendar_start));
$end = date(strtotime($d." ".$calendar_end));

//initialize variables
$start_title_err = $end_time_err = $date_err = $venue_err = $start_time_err = $note_err = "";

//run if update button pressed
if (isset($_POST['submit'])) {
	//set variables from POST
	$bookingId = $_POST['bookingid'];
	$title = $_POST['title'];
	$userid = $_SESSION['id'];
	$date = $_POST['date'];
	$start_time = $_POST['start_time'];
	$end_time = $_POST['end_time'];
	$venue = $_POST['venue'];
	$color = $_POST['color'];
	$notes = $_POST['notes'];
	
	//check that booking still exists
	try {
		$connection = new PDO($dsn, $username, $password, $options);
		$bookingId = $_GET['bookingId'];
		$sql = "SELECT * FROM bookings WHERE bookingid=$bookingId"; 
		$statement = $connection->prepare($sql);
		$statement->execute();
		
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		if(empty($result['bookingid'])){
			$_SESSION['error'] = "That booking has been deleted and can't be updated!";
			header("Location:/calendar.php");
		}
	} catch(PDOException $error) {
		$_SESSION['error'] = "There was an issue connecting to the database. Please try again later!";
		header("Location: /");
	}
	
	//test if current user is the bookings owner
	if($userid == $_SESSION['id']){
		$valid_user = true;
	}else{
		$valid_user = false;
	}
	
	//Check for errors
	if(!(isset($_POST['title']) && !empty($_POST['title']))){
		$title_err = "Please input a booking title";
	} elseif(strlen($_POST['title']) > 50){
		$title_err = "Title is Too Long (Max: 50)";
	}
	
	if(empty(strtotime($_POST['date']))){
		$date_err = "That date cannot be selected";
	}
	
	if(strtotime($_POST['start_time']) >= strtotime($_POST['end_time'])){
		$end_time_err = 'End time must be after start time';
	}
	
	if(empty($_POST['start_time'])){
		$start_time_err = 'Please select a start time';
	}
	
	if(empty($_POST['end_time'])){
		$end_time_err = 'Please select an end time';
	}
	
	if(!(isset($_POST['venue']) && !empty($_POST['venue']))){
		$venue_err = "Please select a venue";
	}
	
	if(strlen($_POST['notes']) > 255){
		$note_err = "Notes are Too Long (Max: 255)";
	}
	
	//Only run if there are no errors
	if($title_err == "" && $date_err == "" && $start_time_err == "" && $end_time_err == "" && $venue_err == "" && $note_err == ""){
		
		//connect and update database
		try {
			$connection = new PDO($dsn, $username, $password, $options);
			
			$new_booking = array( 
			"title"    => $_POST['title'], 
			"date"      => $_POST['date'],
			"start_time"      => $_POST['start_time'],
			"end_time"      => $_POST['end_time'], 
			"venue"      => $_POST['venue'], 
			"color"      => $_POST['color'], 
			"notes"      => $_POST['notes'], 
			);
			
			$bookingId = $_GET['bookingId'];
			
    $sql = "UPDATE bookings
            SET title = :title, 
                date = :date, 
                start_time = :start_time, 
                end_time = :end_time, 
                venue = :venue,
                color = :color,
                notes = :notes
            WHERE bookingid = $bookingId";
			$statement = $connection->prepare($sql);
			$statement->execute($new_booking);
						
			//return back to home page on day of booking
			$_SESSION['success'] = "Booking has been updated successfully!";
			header("Location:/calendar.php?day=$date");

		} catch (PDOException $error) {
			$_SESSION['error'] = "There was an issue connecting to the database. Please try again later!";
			header("Location: /");
		}		
	}
} else { //run if update button isn't pressed

	//get row from database where bookingid = GET bookingId
	$bookingId = $_GET['bookingId'];
	try {
		$connection = new PDO($dsn, $username, $password, $options);
		
		$sql = "SELECT * FROM bookings WHERE bookingid=$bookingId"; 
		$statement = $connection->prepare($sql);
		$statement->execute();
		
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		if(empty($result['bookingid'])){
			$_SESSION['error'] = "That booking doesn't exist!";
			header("Location:/calendar.php");
		}
	} catch(PDOException $error) {
		$_SESSION['error'] = "There was an issue connecting to the database. Please try again later!";
		header("Location: /");
	}
	
	//set variables from database
	$title = $result['title'];
	$userid = $result['userid'];
	$date = $result['date'];
	$start_time = $result['start_time'];
	$end_time = $result['end_time'];
	$venue = $result['venue'];
	$color = $result['color'];
	$notes = $result['notes'];
	
	//test if current user is the bookings owner
	if($userid == $_SESSION['id']){
		$valid_user = true;
	}else{
		$valid_user = false;
	}
}

//page header
include "templates/header.php" 
?>

<script type="text/javascript" src="/assets/js/characterCount.js"></script>

	<br />
	<!-- Page Heading -->
	<div class="container col-10 offset-1 row my-0 p-0">
		<div class="col-8"><h2>Update Booking</h2></div>
		<div class="col-2 <?if(!$valid_user) echo 'offset-2'?>">
			<a class="form-control btn btn-primary" href="/calendar.php?day=<?echo $date?>">< Back</a>
		</div>
		
		<!-- Delete Button -->
		<!-- Display only if valid user -->
		<?php if($valid_user){ ?>
		<div class="col-2">
			<form method="post" action="delete.php?bookingId=<?echo $bookingId?>">
				<input type="submit" name="delete" class="form-control btn btn-danger" onclick="return confirm('Are you sure you want to delete this booking?');" value="Delete">
			</form>		
		</div>
		<?}?>
	</div>
  
	<!-- Form -->
	<div class="border col-10 offset-1 p-3 mt-3">
		<form method="post">
			<!-- Title -->
			<div class="form-group row">
				<div class="col-2 offset-1">
					<label for="title">Booking Title</label> 
				</div>
				<div class="col-6">
					<input class="form-control" type="text" name="title" value="<?echo $title?>" onkeyup="countTitle(this.value)"> 
				</div>
				<span id="count-title" class="text-secondary"><? echo strlen($title) ?>/50</span>
				<span class="text-danger"><?echo $title_err?></span>
			</div>
			
			<!-- Date -->
			<div class="form-group row">
				<div class="col-2 offset-1">
					<label for="date">Date</label> 
				</div>
				<div class="col-6">
					<input class="form-control" type="date" name="date" value="<?echo $date?>"> 
				</div>
				<span class="text-danger"><?echo $date_err?></span>
			</div>
			
			<!-- Start Time -->
			<div class="form-group row">
				<div class="col-2 offset-1">
					<label for="start_time">Start Time</label> 
				</div>
				<div class="col-6">
					<select class="form-control" name="start_time">
						<option class="text-secondary" value="">Select...</option>
						<?php 
						//set options for each time
						for($t = $start; $t <= $end; $t+=900){ ?>
							<option value="<?echo date("H:i:s",$t)?>" <?if($start_time == date("H:i:s",$t)) echo 'selected="selected"'?>><?echo date("H:i",$t)?></option>
						<?}?>
					</select>
				</div>
				<span class="col-3 text-danger"><?echo $start_time_err?></span>
			</div>
			
			<!-- End Time -->
			<div class="form-group row">
				<div class="col-2 offset-1">
					<label for="end_time">End Time</label>
				</div>
				<div class="col-6">
					<select class="form-control" name="end_time">
						<option class="text-secondary" value="">Select...</option>
						<?php 
						//set options for each time
						for($t = $start; $t <= $end; $t+=900){ ?>
							<option value="<?echo date("H:i:s",$t)?>" <?if($end_time == date("H:i:s",$t)) echo 'selected="selected"'?>><?echo date("H:i",$t)?></option>
						<?}?>
					</select>
				</div>
				<span class="col-3 text-danger"><?echo $end_time_err?></span>
			</div>
			
			<!-- Venue -->
			<div class="form-group row">
				<div class="col-2 offset-1">
					<label for="venue">Venue</label> 
				</div>
				<div class="col-6">
					<select class="form-control" name="venue">
						<option class="text-secondary" value="">Select...</option>
						<option value="lawn1" <?if($venue == 'lawn1') echo 'selected="selected"'?>>Lawn 1</option>
						<option value="lawn2" <?if($venue == 'lawn2') echo 'selected="selected"'?>>Lawn 2</option>
						<option value="lawn3" <?if($venue == 'lawn3') echo 'selected="selected"'?>>Lawn 3</option>
					</select>
				</div>
				<span class="text-danger"><?echo $venue_err?></span>
			</div> 
			
			<!-- Colour -->
			<div class="form-group row">
				<div class="col-2 offset-1">
					<label for="color">Colour</label> 
				</div>
				<div class="col-6">
					<select class="form-control" name="color">
						<option value="blue" <?if ($color == 'blue') echo 'selected="selected"'?>>Blue</option>
						<option value="green" <?if ($color == 'green') echo 'selected="selected"'?>>Green</option>
						<option value="red" <?if ($color == 'red') echo 'selected="selected"'?>>Red</option>
						<option value="orange" <?if ($color == 'orange') echo 'selected="selected"'?>>Orange</option>
						<option value="purple" <?if ($color == 'purple') echo 'selected="selected"'?>>Purple</option>
						<option value="pink" <?if ($color == 'pink') echo 'selected="selected"'?>>Pink</option>
						<option value="gray" <?if ($color == 'gray') echo 'selected="selected"'?>>Grey</option>
						<option value="yellow" <?if ($color == 'yellow') echo 'selected="selected"'?>>Yellow</option>
					</select>
				</div>
				<span class="text-danger"><?echo $color_err?></span>
			</div> 
			 
			<!-- Notes -->
			<div class="form-group row">
				<div class="col-2 offset-1">
					<label for="notes">Notes</label> 
				</div>
				<div class="col-6">
					<textarea class="form-control" rows="5" name="notes" onkeyup="countNotes(this.value)"><?echo $notes?></textarea>
				</div>
				<span id="count-notes" class="text-secondary"><? echo strlen($notes) ?>/255</span>
				<span class="col-3 text-danger"><?echo $note_err?></span>
			</div>
			
			<!-- Update Button -->
			<!-- Display only if valid user -->
			<?php if($valid_user){ ?>
			<div class="form-group row">
				<div class="col-4 offset-4">
					<input class="form-control btn btn-success" type="submit" name="submit" value="Update">
				</div>
			</div>
			<?}?>
		</form>
	</div>
<?php 
//page footer
include "templates/footer.php"
?>
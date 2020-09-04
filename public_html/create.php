<?php 
require "templates/session.php";
require "../db_config.php"; 
require "templates/setting_values.php";

//default variables for start and end times
date_default_timezone_set("Australia/Canberra");
$d = isset($_GET['day']) ? $_GET['day'] : date('Y-m-d');
$start = date(strtotime($d." ".$calendar_start));
$end = date(strtotime($d." ".$calendar_end));

//initialize variables
$start_title_err = $end_time_err = $date_err = $venue_err = $start_time_err = $note_err = $overlap_err = "";

//run if create button pressed
if (isset($_POST['submit'])) {
	
	//set variables from POST
	$title = $_POST['title'];
	$userid = $_SESSION['id'];
	$date = $_POST['date'];
	$start_time = $_POST['start_time'];
	$end_time = $_POST['end_time'];
	$venue = $_POST['venue'];
	$color = $_POST['color'];
	$notes = $_POST['notes'];
	
	//Check for errors
	
	//No Booking
	if(!(isset($title) && !empty($title))){
		$title_err = "Please input a booking title";
	} elseif(strlen($_POST['title']) > 50){
		$title_err = "Title is Too Long (Max: 50)";
	}
	
	//Time ouside avilable range
	if(empty(strtotime($date))){
		$date_err = "That date cannot be selected";
	}
	
	//Start Time after End Time
	if(strtotime($start_time) >= strtotime($end_time)){
		$end_time_err = 'End time must be after start time';
	}
	
	//Start Time not selected
	if(empty($start_time)){
		$start_time_err = 'Please select a start time';
	}
	
	//End Time not Selected
	if(empty($end_time)){
		$end_time_err = 'Please select an end time';
	}
	
	//Venue Not Selected
	if(!(isset($venue) && !empty($venue))){
		$venue_err = "Please select a venue";
	}
	
	if(strlen($notes) > 255){
		$note_err = "Notes are Too Long (Max: 255)";
	}
	
	//Only test if no other time errors
	if($start_time_err == "" && $end_time_err == ""){
		//Date Overlap Error
		try {
			$connection = new PDO($dsn, $username, $password, $options);

			$sql = "
				SELECT * FROM bookings
				WHERE (date = '$date')
				AND (venue = '$venue');
			"; 
			
			$statement = $connection->prepare($sql);
			$statement->execute();
			
			$result = $statement->fetchAll();
		} catch(PDOException $error) {
			$_SESSION['error'] = "There was an issue connecting to the database. Please try again later!";
			header("Location: /");
		}
		
		//test all bookings with same date and venue for overlapping times
		foreach($result as $row){
			$_start_time = strtotime($row['start_time']);
			$_end_time = strtotime($row['end_time']);
			
			if(
				($_start_time < strtotime($start_time) && $_end_time > strtotime($start_time)) || //Check overlap at start
				($_start_time < strtotime($end_time) && $_end_time > strtotime($end_time)) || //Check overlap at end
				($_start_time > strtotime($start_time) && $_end_time < strtotime($end_time)) || //Check current inside new
				($_start_time < strtotime($start_time) && $_end_time > strtotime($end_time)) || //Check new inside current
				($_start_time == strtotime($start_time) && $_end_time == strtotime($end_time)) //Check same times
			){
				$overlap_err = "The selected times overlap with another booking at the same venue!";
			}
		}
	}
	
	//Only run if there are no errors
	if($overlap_err == "" && $title_err == "" && $date_err == "" && $start_time_err == "" && $end_time_err == "" && $venue_err == "" && $note_err == ""){
		
		//connect to database and add row to bookings
		try {
			$connection = new PDO($dsn, $username, $password, $options);
			
			$new_booking = array( 
			"title"    => $_POST['title'], 
			"userid"     => $_SESSION['id'],
			"date"      => $_POST['date'],
			"start_time"      => $_POST['start_time'],
			"end_time"      => $_POST['end_time'], 
			"venue"      => $_POST['venue'], 
			"color"      => $_POST['color'], 
			"notes"      => $_POST['notes'], 
			);
			$sql = "INSERT INTO bookings (
					title,
					userid,
					date,
					start_time,
					end_time,
					venue,
					color,
					notes
			) VALUES (
					:title,
					:userid,
					:date,
					:start_time,
					:end_time,
					:venue,
					:color,
					:notes
			)"; 
			$statement = $connection->prepare($sql);
			$statement->execute($new_booking);
			
			//return back to home page on day of booking
			$_SESSION['success'] = "Booking has been created successfully!";
			header("Location:/calendar.php?day=$date");

		} catch (PDOException $error) {
			$_SESSION['error'] = "There was an issue connecting to the database. Please try again later!";
			header("Location: /calendar.php");
		}			
	}
}else{ //run if create button isn't pressed

	//set venue from GET
	if(isset($_GET['venue'])){
		$venue = $_GET['venue'];
	}
	
	//set date and time from GET
	if(isset($_GET['time'])){
		$start_time = date("H:i:s",$_GET['time']);
		$end_time = date("H:i:s",strtotime($start_time.' +2 hour'));
		$date = date("Y-m-d",$_GET['time']);
	}
	
	//set date from date GET
	if(isset($_GET['date'])){
		$date = $_GET['date'];
	}
	
}
//page header
include "templates/header.php" 
?>

	<script type="text/javascript" src="/assets/js/characterCount.js"></script>

	<br />
	<!-- Page Heading -->
	<div class="container col-10 offset-1 row my-0 p-0">
		<div class="col-10"><h2>Create Booking</h2></div>
		<div class="col-2">
			<a class="form-control btn btn-primary" href="/calendar.php?day=<?echo $date?>">< Back</a>
		</div>
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
				<div class="col-3">
					<span id="count-title" class="text-secondary"><? echo strlen($title) ?>/50</span><br />
					<span class="text-danger"><?echo $title_err?></span>
				</div>
			</div>
			
			<!-- Date -->
			<div class="form-group row">
				<div class="col-2 offset-1">
					<label for="date">Date</label> 
				</div>
				<div class="col-6">
					<input class="form-control" type="date" name="date" value="<?echo $date?>"> 
				</div>
				<span class="col-3 text-danger"><?echo $date_err?></span>
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
				<div class="col-3">
					<span class="text-danger"><?echo $end_time_err?></span>
					<span class="text-danger"><?echo $overlap_err?></span>
				</div>
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
				<span class="col-3 text-danger"><?echo $venue_err?></span>
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
			
			<!-- Create Button -->
			<div class="form-group row">
				<div class="col-4 offset-4">
					<input class="form-control btn btn-success" type="submit" name="submit" value="Create">
				</div>
			</div>
		</form>
	</div>
		
<?php 
//page footer
include "templates/footer.php"
?>
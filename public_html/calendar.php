<?php
require "templates/session.php";
require "../db_config.php"; 
require "templates/setting_values.php";

//default variables for start and end times
date_default_timezone_set("Australia/Canberra");

//check if GET date exists and is valid
if(isset($_GET['day']) && !empty($_GET['day']) && !empty(strtotime($_GET['day']))){
	//use date from GET
	$d = date('Y-m-d',strtotime($_GET['day']));
} else {
	//set date to current day
	$d = date('Y-m-d');
}

//get calendar start and end times from settings
$start = date(strtotime($d." ".$calendar_start));
$end = date(strtotime($d." ".$calendar_end));

//calculate prev and next days
$prev= date("Y-m-d", strtotime($d.' -1 day'));
$next= date("Y-m-d", strtotime($d.' +1 day'));

//Date display values
$date_day = date("l",strtotime($d));
$date = date("d F Y",strtotime($d));

//retrieve all bookings for current day
try {
	$connection = new PDO($dsn, $username, $password, $options);

	$sql = "
		SELECT bookings.* ,users.username 
		FROM bookings, users
		WHERE (date = '$d')
		AND (bookings.userid = users.userid);
	"; 
	
	$statement = $connection->prepare($sql);
	$statement->execute();
	
	$result = $statement->fetchAll();
} catch(PDOException $error) {
	$_SESSION['error'] = "There was an issue connecting to the database. Please try again later!";
	header("Location: /");
}
		
//include header
include "templates/header.php";

//popup error message
if(isset($_SESSION['error']) && !empty($_SESSION['error'])){?>
<div id="popup">
	<div class="container">
			<div class="rounded bg-danger offset-3 col-6 pb-3 pt-1">
				<strong class="mr-auto">An Error Has Occured</strong>
				<p><?echo $_SESSION['error']?></p>
				<button onclick="closePopup()" class="btn btn-light col-6 offset-3 text-dark">Close</button>
			</div>
	</div>
</div>
<?php 
unset($_SESSION['error']);

//popup success message
} elseif(isset($_SESSION['success']) && !empty($_SESSION['success'])){
?>
<div id="popup">
	<div class="container">
			<div class="rounded bg-success offset-3 col-6 pb-3 pt-1">
				<strong class="mr-auto">Success</strong>
				<p><?echo $_SESSION['success']?></p>
				<button onclick="closePopup()" class="btn btn-light col-6 offset-3 text-dark">Close</button>
			</div>
	</div>
</div>
<?php 
unset($_SESSION['success']);
}
?>
<!-- Hide popup script -->
<script>
function closePopup(){
	document.getElementById("popup").style.display = "none";
}
</script>

	<!-- Page Title -->
	<div class="row">
		<!-- Selection Calendar -->
		<div class="col-3">
			<?php include "templates/calendar_selector.php" ?>
		</div>
	
		<!-- Current Day and navigation -->
		<div class="col-6 text-center">
			<h2><?php echo $date_day?></h2>
			<h2><?php echo $date?></h2>
			<button class="btn btn-primary" onclick="location.href='?day=<?php echo $prev ?>'">< Previous</button>
			<button class="btn btn-primary" onclick="location.href='/calendar.php'">Today</button>
			<button class="btn btn-primary" onclick="location.href='?day=<?php echo $next ?>'">Next ></button>
		</div>
	</div>
		
	<!-- Bookings -->
	<div class="calendar">
		<!-- Table Headings -->
		<div class="row">
			<div class="col-3 offset-2">Lawn 1</div>
			<div class="col-3">Lawn 2</div>
			<div class="col-3">Lawn 3</div>
		</div>
		
		<?php for($t = $start; $t < $end; $t+=900){ ?>
				<div class="row">
					<!-- Table cells -->
					<div style="top:-15px" class="position-relative col-1 offset-1"><? echo date("H:i",$t); ?></div>
					<div class="col-3 border border-1" onclick="location.href='create.php?venue=lawn1&time=<? echo $t ?>'"></div>
					<div class="col-3 border border-1" onclick="location.href='create.php?venue=lawn2&time=<? echo $t ?>'"></div>
					<div class="col-3 border border-1" onclick="location.href='create.php?venue=lawn3&time=<? echo $t ?>'"></div>
					
					<!-- Overlayed Booking -->
					<?php foreach($result as $row) {
						if($row['start_time'] == date('H:i:s',$t)){ 
						$difference = ((strtotime($row['end_time']) - strtotime($row['start_time']))/900);
						?>
							<div class="booking-overlay col-3 
								<?php	
									//select correct column
									switch ($row['venue']){
										case "lawn1":
											echo "offset-2";
											break;
										case "lawn2":
											echo "offset-5";
											break;
										case "lawn3":
											echo "offset-8";
											break;
									}
								?> no-gutter" style="height:<? echo 40*$difference ?>px;">
								
								<div onclick="location.href='update.php?bookingId=<? echo $row['bookingid'] ?>'"class="btn btn-booking  
								<?php	
									//display selected color
									switch ($row['color']){
										case "red":
											echo "booking-red";
											break;
										case "orange":
											echo "booking-orange";
											break;
										case "blue":
											echo "booking-blue";
											break;
										case "green":
											echo "booking-green";
											break;
										case "gray":
											echo "booking-gray";
											break;
										case "purple":
											echo "booking-purple";
											break;
										case "pink":
											echo "booking-pink";
											break;
										case "yellow":
											echo "booking-yellow";
											break;
									}
								?> w-100 h-100 p-0 m-0">
								
									<!-- Booking Information -->
									<?php if($difference == 1){ ?>
										<div class="booking-title row">
											<?php if(!empty($row['userid'])){?>
												<i style="padding-left:0;" class="col-1 text-left fa fa-user px-0 py-2"></i>
											<?}?>
											<a class="pt-1 col-10 text-center booking-title"><? echo $row['title'] ?></a>
											<?php if(!empty($row['notes'])){?>
												<i class="col-1 text-right fa fa-sticky-note px-0 py-2"></i>
											<?}?>
										</div><!-- Title -->
										
									<? }else{ ?>
										<div class="text-center booking-title"><a><? echo $row['title'] ?></a></div><!-- Title -->
									<? } ?>
									<!-- Only display if time slot is at least 30min -->
									<?php if($difference > 1){ ?>
										<hr class="mt-0 mb-1"><!-- Horizontal Line -->
										<?php if(!empty($row['username'])){ ?>
											<div class="text-left pl-3 booking-user"><a><i class="fa fa-user"></i> <? echo $row['username'] ?></a></div><!-- User -->
										<? } ?>
									<? } ?>
									
									<!-- Only display if time slot is at least 1hr -->
									<?php if($difference > 3){ ?>
										<?php if(!empty($row['notes'])){ ?>
											<!-- Notes -->
											<div class="text-left pl-3 booking-notes">
												<a 
													style="<?echo '-webkit-line-clamp:'.(2*($difference-2)).'; max-height:'.(32*($difference-2)).'px;'?>">
														<b>Notes:</b>
														<br />
														<? echo $row['notes'] ?>
												</a>
											</div>
										<? } ?>
									<? } ?>
									
								</div>
							</div>
						<? } ?>
					<?php }; ?>
				</div>
		<?php } ?>
		<!-- Display time for last row -->
		<div class="row"><div style="top:-15px" class="position-relative col-1 offset-1"><? echo date("H:i",$end); ?></div></div>		
	</div>
	
	<!-- Create Booking Button -->
	<footer class="footer">
		<button class="btn btn-primary btn-xl-circle" onclick="location.href='create.php?date=<? echo $d ?>'">+</button>
	</footer>
	
	

	
<!-- Include Footer -->		
<?php include "templates/footer.php"?>
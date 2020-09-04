<?php
require "templates/session.php";

//if update button pressed
if (isset($_POST['submit'])) {
	
	//test for time errors
	if(strtotime($_POST['start_time']) >= strtotime($_POST['end_time'])){
		//display error message
		$time_err = 'End time must be after start time';
	}else{
		//update setting in file
		$st = $_POST['start_time'];
		$et = $_POST['end_time'];
		$myfile = fopen("templates/setting_values.php", "w") or die("Unable to open file!");
		$txt = "<?php
			\$calendar_start = '$st';
			\$calendar_end = '$et';
		?>";
		fwrite($myfile, $txt);
		fclose($myfile);
		
		//display confirmation message
		$_SESSION['success'] = "Settings have been updated!";
		header("Location: /calendar.php");
	}
}

require "templates/setting_values.php";
include "templates/header.php" 
?>

    <div class="container border col-6 offset-3 mt-3">
			<h2 class="my-3">Settings</h2>
				<form method="post">
				
					<!-- Start Time -->
					<div class="form-group row">
						<div class="col-2 offset-1">
							<label for="start_time">Start Time</label> 
						</div>
						<div class="col-6">
							<select class="form-control" name="start_time">
								<?php 
								//set options for each time
								for($t = date(strtotime("00:00:00")); $t <= date(strtotime("24:00:00")); $t+=900){ ?>
									<option value="<?echo date("H:i:s",$t)?>" <?if($calendar_start == date("H:i:s",$t)) echo 'selected="selected"'?>><?echo date("H:i",$t)?></option>
								<?}?>
							</select>
						</div>
					</div>
					
					<!-- End Time -->
					<div class="form-group row">
						<div class="col-2 offset-1">
							<label for="end_time">End Time</label>
						</div>
						<div class="col-6">
							<select class="form-control" name="end_time">
								<?php 
								//set options for each time
								for($t = date(strtotime("00:00:00")); $t <= date(strtotime("23:59:59")); $t+=900){ ?>
									<option value="<?echo date("H:i:s",$t)?>" <?if($calendar_end == date("H:i:s",$t)) echo 'selected="selected"'?>><?echo date("H:i",$t)?></option>
								<?}?>
							</select>
						</div>
					</div> 
					<!-- Message Displays -->
					<p class="col-12 text-center text-danger"><?echo $time_err?></p>
					
					<!-- Update Button -->
					<div class="form-group row">
						<input type="submit" name="submit" class="btn btn-primary col-6 offset-3" value="Update">
					</div>
				</form>
    </div>
		
<?php include "templates/footer.php"  ?>
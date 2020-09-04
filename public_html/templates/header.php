<!doctype html>
<html lang="en">

<head>

	<title>Booking Calendar</title>
	<meta charset="utf-8">
			
	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"><!-- CSS -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script><!-- jQuery -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script><!-- Popper JS -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>		<!-- JavaScript -->		
		
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- Custom Style Sheet -->
	<link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
  <h1>Booking Calendar</h1>
		
	<nav class="navbar navbar-expand navbar-dark">
    <ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="/">Home</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/calendar.php">Calendar</a>
			</li>
		</ul>
	
		<ul class="navbar-nav">
		
			<?
			session_start();
			if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
			?>
			<!-- Logged Out User -->
			<li class="nav-item">
				<a class="nav-link px-3" href="/register.php">Register</a>
			</li>
			<li class="nav-item">
				<a class="nav-link px-3" href="/login.php">Login</a>
			</li>
			<?}else{?>
			<!-- Logged In User -->
			<li class="nav-item">
				<a class="nav-text px-3">Welcome, <?echo $_SESSION['username']?></a>
			</li>
			<li class="nav-item">
				<i class="fa fa-cog px-3" onclick="location.href='/settings.php'"></i>
			</li>
			<li class="nav-item help">
				<i class="fa fa-question px-3"></i>
				<p class="help-content">To make a booking click on the calendar where you want to book or click the + in the bottom right corner.</p>
			</li>
			<li class="nav-item">
				<a class="nav-link px-3" href="/reset-password.php">Reset Password</a>
			</li>
			<li class="nav-item">
				<a class="nav-link px-3" href="/logout.php">Logout</a>
			</li>
			<?}?>
		</ul> 
	</nav>




<div class="col-12">
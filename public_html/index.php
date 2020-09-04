<!-- Include Header -->
<?php include "templates/header.php";

//Test for error session
session_start();
if(isset($_SESSION['error']) && !empty($_SESSION['error'])){?>
	<!-- Error popup for login error -->
	<div id="popup">
		<div class="container">
				<div class="rounded bg-danger offset-3 col-6 pb-3 pt-1">
					<strong class="mr-auto">An Error Has Occured</strong>
					<p><?echo $_SESSION['error']?></p>
					<button onclick="closePopup()" class="btn btn-light col-6 offset-3 text-dark">Close</button>
				</div>
		</div>
	</div>

	<!-- Close popup script -->
	<script>
	function closePopup(){
		document.getElementById("popup").style.display = "none";
	}
	</script>
<?php 
unset($_SESSION['error']);
} 
?>

<!-- Home page content -->
<div class="col-6 offset-3 text-center border border-dark mt-3">
<h2>Welcome</h2>
<p>To use the booking calendar first <a href="/login.php">login</a> or <a href="/register.php">register</a>. Then you will be able to select a date and time and make a booking.</p>
</div>

<!-- Include Footer -->		
<?php include "templates/footer.php"?>

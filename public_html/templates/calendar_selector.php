
<?php
//get date from GET if available and valid
$month = (isset($_GET['c']) && !empty($_GET['c'])  && !empty(strtotime($_GET['c']))) ? date("F Y",$_GET['c']) : date("F Y",strtotime($d));

$first_month = date(strtotime('1 '.$month));

$day = date("Y-m-d",strtotime(date("Y-m-d",$first_month).' -1 monday'));
?>


<table class="calendar-sm">
	<tr class="title text-center">
		<th class="btn btn-circle" onclick="location.href='?day=<?php echo $d ?>&c=<?echo strtotime($month. '-1 month')?>'"><</th>
		<th colspan="5"><?echo date("F Y",strtotime($month))?></th>
		<th class="btn btn-circle" onclick="location.href='?day=<?php echo $d ?>&c=<?echo strtotime($month. '+1 month')?>'">></th>
	</tr>
	<tr>
		<th>Mon</th>
		<th>Tue</th>
		<th>Wed</th>
		<th>Thu</th>
		<th>Fri</th>
		<th>Sat</th>
		<th>Sun</th>
	</tr>
<? for($i = 0; $i<6; $i++){?>
	<tr>
	<?for($j = 0; $j<7; $j++){?>
		<td>
			<button 
				onclick="location.href='?day=<?echo $day?>'" 
				class="btn btn-circle 
				<?php 
				if($day == date("Y-m-d")){
					echo 'btn-today';
				} 
				echo " ";
				if($day == date("Y-m-d",strtotime($d))){
					echo 'btn-info';
				}elseif($month == date("F Y",strtotime($day))){
					echo 'btn-primary';}else {echo 'btn-secondary';
				}
				?>
				">
				<?echo date("d",strtotime($day))?>
			</button>
		</td>
		<?$day=date("Y-m-d",strtotime($day." +1 day"))?>
	<?}?>
	</tr>
<?}?>
</table>
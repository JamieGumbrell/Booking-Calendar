//Booking Notes Character Count
function countNotes(str) {
	var length = str.length;
	if(length > 255){
		document.getElementById("count-notes").classList.add("text-danger");
		document.getElementById("count-notes").classList.remove("text-secondary");
	} else{
		document.getElementById("count-notes").classList.add("text-secondary");
		document.getElementById("count-notes").classList.remove("text-danger");
	}
	document.getElementById("count-notes").innerHTML = length + '/255';
}

//Booking Title Character Count
function countTitle(str) {
	var length = str.length;
	if(length > 50){
		document.getElementById("count-title").classList.add("text-danger");
		document.getElementById("count-title").classList.remove("text-secondary");
	} else{
		document.getElementById("count-title").classList.add("text-secondary");
		document.getElementById("count-title").classList.remove("text-danger");
	}
	document.getElementById("count-title").innerHTML = length + '/50';
}

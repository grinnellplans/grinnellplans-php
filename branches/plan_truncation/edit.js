function checkPlanLength() {
	//var editForm = document.getElementById('editform');
	var editText = document.getElementById('edit_textarea');
	var planLength = editText.value.length;
	var perc = Math.round( planLength * 100 / 65535 );
	if (perc != window.perc) {
		var fillMeter = document.getElementById('edit_fill_meter');
		var fillBarElements = fillMeter.getElementsByTagName('div');
		// Add or remove danger warning as appropriate
		if (perc >= 100 && window.perc < 100) {
			fillMeter.className += ' danger';
		} else if (window.perc >= 100 && perc < 100) {
			fillMeter.className = fillMeter.className.replace(/\bdanger\b/, '');
		}
		// Set the values of the progress bar and percent text
		for (var i = 0; i < fillBarElements.length; i++) {
			if (fillBarElements[i].className == 'full_amount') {
				fillBarElements[i].style.width = Math.min(perc, 100) + "%";
			} else if (fillBarElements[i].className == 'fill_percent') {
				fillBarElements[i].innerHTML = perc + "%";
			}
		}

		window.perc = perc;
	}
}
window.onload = checkPlanLength;

<?php
require_once("Plans.php");

class InterfaceRegistry {

	public static function getAll() {
		return array(
			array(1, "<b>Modern</b><br>Default interface."),
			array(2, "<b>Old Term</b>"),
			array(3, "<b>Centered</b><br>Autofinger list is on the right side."),
			array(6, "<b>Postmodern</b><br>New, tableless, and powerful."));
	}
}
?>

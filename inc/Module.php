<?php
class Module
{
	public static function dispatch_event($event)
	{
		foreach(glob("modules/*/$event.php") as $file) {
			include ($file);
		}
	}
}
?>

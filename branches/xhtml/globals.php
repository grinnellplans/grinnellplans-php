<?
/**
 * Made a new file for lack of better place to put this.  Let's get rid of those
 * register globals.
 */
//TODO actually turn off register globals

// Set myprivl by trying a bunch of stuff - see if it's in post or get or
// something, then compare it against what the cookie thinks
if (($myprivl
	|| ($myprivl = $_GET['myprivl'])
	|| ($myprivl = $_POST['myprivl'])
	|| 1)
		&& ($myprivl = setpriv($myprivl, $HTTP_COOKIE_VARS["thepriv"]))) {
		//do nothing
		//print("<!--success! get=$myprivl1, post=$myprivl2, orig=$myprivl, new=$myprivl3-->\n\n\n");
		//TODO maybe make sure it's a valid int?
	} else {
		// default
		$myprivl = 1;
	}
?>

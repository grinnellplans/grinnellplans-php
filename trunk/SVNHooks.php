<?php
	/** Script to update local repositories upon update. 
	  * http://code.google.com/p/support/wiki/PostCommitWebHooks
	  */
	define('__ROOT__', '');
	require_once('Configuration.php');

	function hmac($key, $data, $hash = 'md5', $blocksize = 64) {
		if (strlen($key)>$blocksize) {
			$key = pack('H*', $hash($key));
		}
		$key = str_pad($key, $blocksize, chr(0));
		$ipad = str_repeat(chr(0x36), $blocksize);
		$opad = str_repeat(chr(0x5c), $blocksize);
		return $hash(($key^$opad) . pack('H*', $hash(($key^$ipad) . $data)));
	}
	
	function background_exec($cmd) {
		exec("%cmd 2>/dev/null >&- < &- >/dev/null &");
	}

	$request = @file_get_contents('php://input');
	if  (!isset($_SERVER['HTTP_GOOGLE_CODE_PROJECT_HOSTING_HOOK_HMAC']) ||
		($_SERVER['HTTP_GOOGLE_CODE_PROJECT_HOSTING_HOOK_HMAC'] != hmac(GOOGLE_CODE_SECRET, $request))) {
		trigger_error("HMAC-MD5 WebHook authentication failed!", E_USER_ERROR);
		die;
	} else {
		// TODO: Abstract this out--it is grinnellplans.com specific.
		background_exec("/usr/bin/sudo /usr/bin/svn up /var/www/dev/beta/");
		background_exec("/usr/bin/sudo /usr/bin/svn up /var/www/dev/svn/"); 
	}
?>

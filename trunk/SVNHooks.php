<?php
	// http://code.google.com/p/support/wiki/PostCommitWebHooks
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

	$request = @file_get_contents('php://input');
	if (!isset($_SERVER['HTTP_GOOGLE_CODE_PROJECT_HOSTING_HOOK_HMAC']) ||
		($_SERVER['HTTP_GOOGLE_CODE_PROJECT_HOSTING_HOOK_HMAC'] != hmac(GOOGLE_CODE_SECRET, $request))) {
		die;
	} else {
		mail("thatha@thatha.org", "SVN updated", $request);
	}
?>

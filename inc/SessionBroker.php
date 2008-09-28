<?php
require_once ("Plans.php");
require_once ('Configuration.php');

/**
  * Modifies the run-time environment as to save _SESSION in the user-side cookie.
  * It encrypts the cookie to ensure its origin. Needs to be instantiated before using
  * _SESSION. 
  */
class SessionBroker {
	function SessionBroker() {
		ob_start();
		session_set_save_handler(array(&$this, 'open'),
									array(&$this, 'close'),
									array(&$this, 'read'),
									array(&$this, 'write'),
									array(&$this, 'destroy'),
									array(&$this, 'gc'));
		register_shutdown_function('session_write_close');
		session_set_cookie_params(0, '/', COOKIE_DOMAIN);
		session_start();
		setcookie(session_name(), "", 0, '/', COOKIE_DOMAIN);
	}
	
	function open($arg_str_save_path, $arg_str_session_name) {
		return true;
	}
	
	function close() {
		return true;
	}
	
	function read($arg_str_session_id) {
		$cypher = $_COOKIE[COOKIE_PAYLOAD];
		$td = mcrypt_module_open('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, SESSION_ENCRYPTION_KEY, $iv);
		$plain_text = rtrim(mdecrypt_generic($td, base64_decode($cypher)), "\0");
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return $plain_text;
	}
	
	function write($arg_str_session_id, $arg_str_session_data) {
		$td = mcrypt_module_open('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, SESSION_ENCRYPTION_KEY, $iv);
		$cypher = base64_encode(mcrypt_generic($td, $arg_str_session_data));
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		setcookie(session_name(), "", 0, "/", COOKIE_DOMAIN);
		setcookie(COOKIE_PAYLOAD, $cypher, 0, "/", COOKIE_DOMAIN);
		ob_end_flush();
		return true;
	}
	
	function destroy($arg_str_session_id) {
		setcookie(COOKIE_PAYLOAD, "", 0, "/", COOKIE_DOMAIN);
		return true;
	}
	
	function gc($arg_int_next_lifetime) {
		return true;
	}
}
?>
<? 

/** 
@file cookie_session.php 
*/ 
/** 
The domain of the cookie. 
If you are on localhost leave it empty. 
If you are on the production site put the name of the domain to enable SSO on the domain and all its subdomains. 
*/ 
define("COOKIE_DOMAIN", "grinnellplans.com"); 

/** 
The session data encryption key. 
It is recommended to change it on each site. 
*/ 
require_once('Configuration.php');

/** 
@class session. 
Needs Config file before it. 
This class handles php session to be saved in a cookie instead on disk. 
*/ 
class session 
{ 
//---------------------------------------------------------------------- 
    /** 
    Session constructor. 
    Sets this class as the session save handler to make php use it as its save method for saving php normal session. 
    It also registers session_write_close() as the shutdown function to make sure that session is written before the page closes. 
    And it starts session using session_start. so to implement session in any file just require_once this file. 
    @access Public. 
    */ 
    function session() { 
        ob_start(); 
        session_set_save_handler    (    array(&$this, 'open'), 
                                        array(&$this, 'close'), 
                                        array(&$this, 'read'), 
                                        array(&$this, 'write'), 
                                        array(&$this, 'destroy'), 
                                        array(&$this, 'gc') 
                                    ); 
        register_shutdown_function('session_write_close'); 
 	session_start();
    } 
//---------------------------------------------------------------------- 
    /** 
    Session storage function (open). 
    @param arg_str_save_path (not used). 
    @param arg_str_session_name (not used). 
    @return Boolean true/false. 
    @see close(). 
    @access Public. 
    */ 
    function open($arg_str_save_path, $arg_str_session_name) { 
        return true; 
    } 
//---------------------------------------------------------------------- 
    /** 
    Session storage function (close). 
    @return Boolean true/false. 
    @see open(). 
    @access Public. 
    */ 
    function close() 
    { 
        return true; 
    } 
//---------------------------------------------------------------------- 
    /** 
    Session storage function (read). 
    Selects the session data from cookie and decrypts it given the session id. 
    @param arg_str_session_id the 32 byte session id supplied by the client. 
    @return The session data as String or an empty string if there is no session data. 
    @see write(). 
    @access Public. 
    */     
    function read($arg_str_session_id) 
    { 
        $cypher = $_COOKIE[$arg_str_session_id]; 
        $td = mcrypt_module_open('tripledes', '', 'ecb', ''); 
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND); 
        mcrypt_generic_init($td, SESSION_ENCRYPTION_KEY, $iv); 
        $plain_text = rtrim(mdecrypt_generic($td, base64_decode($cypher)), "\0"); 
        mcrypt_generic_deinit($td); 
        mcrypt_module_close($td); 
        return $plain_text; 
    } 
//---------------------------------------------------------------------- 
    /** 
    Session storage function (write). 
    Writes the session data after the page code has finished to the cookie with the session id as the cookie name. 
    @param arg_str_session_id the 32 byte session id supplied by the client. 
    @param arg_str_session_data the session data to be written to cookie. 
    @return Boolean true/false. 
    @see read(). 
    @access Public. 
    */     
    function write($arg_str_session_id, $arg_str_session_data) 
    { 
        $td = mcrypt_module_open('tripledes', '', 'ecb', ''); 
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND); 
        mcrypt_generic_init($td, SESSION_ENCRYPTION_KEY, $iv); 
        $cypher = base64_encode(mcrypt_generic($td, $arg_str_session_data)); 
        mcrypt_generic_deinit($td); 
        mcrypt_module_close($td); 
        if(COOKIE_DOMAIN) setcookie(session_name(), session_id(), 0, "/", (COOKIE_DOMAIN ? "." . COOKIE_DOMAIN : NULL)); 
        setcookie($arg_str_session_id, $cypher, 0, "/", (COOKIE_DOMAIN ? "." . COOKIE_DOMAIN : NULL)); 
        ob_end_flush(); 
        return true; 
    } 
//---------------------------------------------------------------------- 
    /** 
    Session storage function (destroy). 
    This method is called when the code runs session_destroy(). It deletes the session data with the given session id from the cookie. 
    @param arg_str_session_id the 32 byte session id supplied by the client. 
    @return Boolean true/false. 
    @see write(). 
    @access Public. 
    */     
    function destroy($arg_str_session_id) 
    { 
	setcookie("PHPSESSID", "", 0, "/", (COOKIE_DOMAIN ? "." . COOKIE_DOMAIN : NULL));
        setcookie($arg_str_session_id, "", 0, "/", (COOKIE_DOMAIN ? "." . COOKIE_DOMAIN : NULL));
        setcookie($arg_str_session_id, ""); 
        return true; 
    } 
//----------------------------------------------------------------------     
    /** 
    Session storage function (gc). 
    @param arg_int_next_lifetime (not used). 
    @return Boolean true/false. 
    @access Public. 
    */ 
    function gc($arg_int_next_lifetime) 
    { 
        return true; 
    } 
//---------------------------------------------------------------------- 
}    // end class session 

$obj_session = new session(); /**< Create a new instance of the class Session and this is enough to start the new session.*/ 
?>

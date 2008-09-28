<?php

/* Substantial portions of this code come from Hugh E.
 * Williams and David Lane, "Web Database Applications with PHP and MySQL",
 * published by O'Reilly & Associates.
 */
   
// Trigger an error condition
function showerror() { 
  if (mysql_errno() || mysql_error())      
    trigger_error("MySQL error: " .
                  mysql_errno() . 
                  " : " . mysql_error(), 
                  E_USER_ERROR);
  else 
    trigger_error("Could not connect to DBMS", E_USER_ERROR);
}

// Abort on error. Deletes session variables to leave
// us in a clean state.
function errorHandler($errno, $errstr, $errfile, $errline) {
  switch ($errno) {
    case E_USER_NOTICE:
    case E_USER_WARNING:
    case E_WARNING:
    case E_NOTICE:
    case E_CORE_WARNING:
    case E_CORE_NOTICE:
    case E_COMPILE_WARNING:
      break;
    case E_USER_ERROR:
    case E_ERROR:
    case E_PARSE:
    case E_CORE_ERROR:
    case E_COMPILE_ERROR:
      session_start();

      if (session_is_registered("message"))
        session_unregister("message");

      if (session_is_registered("order_no"))
        session_unregister("order_no");

      $errorString = "Ride board system error: $errstr (# $errno).<br>\n" .
         "Please report the following to the administrator:<br>\n" .
         "Error in line $errline of file $errfile.<br>\n";

      // Send the error to the administrator by email
      error_log($errorString, 1, "kuper");
        ?>
        <?php
            // Stop the system
            die(
                "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"
            \"http://www.w3.org/TR/html4/loose.dtd\">
          <html>
          <head>
          <title>Ride Board</title>
          <link rel=\"stylesheet\" type=\"text/css\" href=\"login.css\">
          <META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">
          </head>
          <body>
          <div id=\"content\">
          The rideboard database is temporarily unavailable.<br><br>
          $errorString
          </div>
          </body>
          </html>");
    default:
      break;
    }
}
?>

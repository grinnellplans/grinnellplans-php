
<html>
<head>
<link rel="stylesheet" type="text/css" href="notes.css" />
<title>Notes</title>


<?php                                

    if (!($connection = @ mysql_pconnect('127.0.0.1','spurgeon','is23sziz'))) {
        echo mysql_error();
        exit;
    }
                 $databaseName = "test";
                 if (!(mysql_select_db($databaseName, $connection))) {
                     echo mysql_error();
                     exit;
                 }
                                 #    echo ("Apparently, MySQL is working!");
                                 ?>     





                                 <?php
                                 $sql = "select 1 as answer";

                                 if (!($result = mysql_query($sql, $connection))) {




                                      echo mysql_error(); 
                                     } else {
                                         $next_match = mysql_fetch_array($result) ;
                                             $title = $next_match['answer'];
                                             echo($title);             



                                     }

                                     print_r($result);
                                     print_r($next_match);
                                 ?>


                                 Hello
                                 <?php

                                     echo crypt('ab','ab');
                                     ?>

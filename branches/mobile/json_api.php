<?php
header("content-type:text/plain");

require_once('Plans.php');
new SessionBroker();

$debug = false; //set this to true to see messages in error_log


/**
 * This file provides an ajax interface to the plans database.  
 * It consists of one controller that looks for a task parameter
 * in the query string and processes accordingly.
 *
 * The response is an array containing a flag indicating success,
 * a field with any message needed, and whatever extra data the
 * operation requires.
 *
 * For authentication it relies on the internal plans session broker
 * and cookies.  There are stubs to explicetly encode the data and
 * pass it back to the client, but it is currently disabled.
 *
 * @author James Michael-Hill
 */
 
/**
 * The following calls are supported:
 *
 * GET REQUEST: json_api.php?task=login
 * POST PARAMETERS: string username, string password
 * RETURNS: array autofingerList, bool success, string message
 *
 * GET REQUEST: json_api.php?task=autofingerlist
 * POST PARAMETERS: none
 * RETURNS: array autofingerList, bool success, string message
 *
 * GET REQUEST: json_api.php?task=read
 * POST PARAMETERS: string username
 * OPTIONAL POST PARAMETERS: string readlinkreplacement, bool limitsize, bool partial
 * RETURNS: array plandata, bool success, string message
 */


$log = new ErrorConsoleLogger();
if (isset($_GET['task'])) {
  $task = strtolower($_GET['task']);
  $dbh=db_connect();
  
  $response = array("message" => "", "success" => false);
  
  /*
   *  It may be useful to have the cookie payload passed explicetly, but
   *  doing so would require modifying the SessionBroker to allow retreiving
   *  the payload at will instead of on close.  If it is necessary insert
   *  logic here for setting $manual_cookie to true
   */
   
  $manual_cookie = false;
  
  //Every response to task will be sent as a json encoded object.
  //The first property is a token for use with the next request
  if ($manual_cookie) {
    // for example:
    // $_SESSION = SessionBroker::Decode($_POST['token']);
  }
  
  $log->addToLog("RECEIVE: ". print_r($_POST,true));
  
  if ($task == 'login'){
    //This task requires a username and password to be posted
    $response = array_merge($response, doLoginTask());
  } 
  
  else if ($task == 'autofingerlist'){
    //This task requires login first
    if (User::logged_in()) {
			$response['autofingerList'] = getAutofingerList($dbh, $idcookie);
			$response['success'] = true;
    }
    else {
      $response['message'] = 'login required';
    }
  } //end of fetch autofinger list task
  
  else if ($task == 'read') {
    /*
     * This task expects a username to be posted, and requires login first
     *
     * If readlinkreplacement is posted, any links to read.php?username= will 
     * be replaced with the contents of readlinkreplacement.  The username can
     * be specified by including {username} in the replacement, eg:
     * readlinkreplacement = doReadPlan('{username}');
     * would turn all reference to read.php?username=sga to doReadPlan('sga');
     *
     * If limitsize is posted and evaluates to true then only the partial plan 
     * will be returned if it is above the threshold, and it will also return 
     * the amount of text remaining.
     *
     * If partial is posted and evaluates to true then only the latter portion 
     * of the plan will be returned
     */
    $response = array_merge($response, doReadTask());
  } //end read task
  
  
  if ($manual_cookie) {
    // for example
  	// $response['token'] = SessionBroker::Encode(); 
  }
  
  //Send any response we have
  print json_encode($response);
  $log->addToLog("RETURN: ". print_r($response,true));
  
} //end of controller, everything else should be a function

if ($debug === true) {
  $log->dumpLog();
}





/**
 * Return a users's plan, either complete, partial, or only the remaining text
 */
function doReadTask() {
  global $log;
  $response = array("message" => "", "success" => false);
  $searchname = $_POST['username'];
  $read_link = $_POST['readlinkreplacement'];
  $limit_size = $_POST['limitsize'];
  $partial = $_POST['partial'];
  
  /*
   * These two are used to define how much of a plan to return if the client
   * requested a limited plan.  The wiggle length is how much over the max length
   * a plan can be before it gets returned.  This way, if the user is prompted to
   * download more or shown how much data is remaining it will be signifigant,
   * instead of say, 2kb.
   */
  $MAX_PLAN_LEN = 10240;
  $WIGGLE_PLAN_LEN = 2048;
  
  
  if (! User::logged_in()) {
    $response['message'] = 'login required';
  }
  else {
    if (! isvaliduser($dbh, $searchname)) {
      $response['message'] = 'invalid user name';
    }
    else {
      $idcookie = User::id();
    
    	$searchnum = get_item($mydbh,"userid","accounts","username",$searchname);
  	
    	$my_result = mysql_query("Select priority From autofinger where
    			owner = '$idcookie' and interest = '$searchnum'");
    	$onlist = mysql_fetch_array($my_result);
    	if ($onlist) {
        update_read($dbh, $idcookie, $searchnum);//mark as having been read
  		  setReadTime($dbh, $idcookie,$searchnum); //and mark time that was read
  		}
		
  		$response_info= array();
  		$planinfo = get_items($mydbh,"username,pseudo,DATE_FORMAT(login, 
            '%c/%e/%y, %l:%i %p'),DATE_FORMAT(changed, 
            '%c/%e/%y, %l:%i %p'),plan,webview","accounts","userid", $searchnum);
            
      $planinfo[0][1] = stripslashes($planinfo[0][1]);
      $response_info['username'] = $planinfo[0][0];
      
      //these bits are akward, and depend on the date format above
      if ($planinfo[0][2] == null || stristr($planinfo[0][2], "0/0/00,")) {
        $response_info['last_login'] = "";
      }
      else {
        $response_info['last_login'] = $planinfo[0][2];

      }
      
      if ($planinfo[0][3] == null || stristr($planinfo[0][3], "0/0/00,")) {
        $response_info['last_updated'] = "";
      }
      else {
        $response_info['last_updated'] = $planinfo[0][3];
      }

      $response_info['pseudo'] = $planinfo[0][1] == null ? "" : $planinfo[0][1];
		
  		if ($read_link) {
  		  //NOTE:  If the planlove link ever changes, you may want to look at this pattern....
  		  $search = '/read\.php\?searchname=([\w]*)[^"|\']*/i';
  		  
  		  //We expect the read_link to have {username} in it somewhere, which we'll swap in for the username
  		  $replace = str_replace('{username}', '\1', $read_link);
  		  $planinfo[0][4] = preg_replace($search, $replace, $planinfo[0][4]);
  		}
  		
  		$planinfo[0][4] = stripslashes($planinfo[0][4]);
  		
  		if ($limit_size) { //they requested a partial plan
  		  $width = mb_strwidth($planinfo[0][4]); //we're preparing for multi byte characters
  		  if ($width > $MAX_PLAN_LEN) {
  		    $width_remaining = $width - $MAX_PLAN_LEN;
  		    if ($width_remaining > $WIGGLE_PLAN_LEN) {
  		      $response_info['partial'] = true;
  		      $response_info['plan'] = mb_strimwidth($planinfo[0][4], 0, $MAX_PLAN_LEN);
  		      $response_info['remaining'] = $width_remaining;
  		    }
  		    else {
  		      $response_info['plan'] = $planinfo[0][4];
  		    }
  		  }
  		  else {
  		    $response_info['partial'] = false;
		      $response_info['plan'] =$planinfo[0][4];
  		  }
  		  $log->addToLog("PLAN WIDTH: ". mb_strwidth($planinfo[0][4]) );
  		}
  		else if ($partial) { //they requested only the last part of the plan
  		  $response_info['remainingplan'] = mb_substr($planinfo[0][4], $MAX_PLAN_LEN);
  		}
  		else {
  		  $response_info['partial'] = false;
  		  $response_info['plan'] = $planinfo[0][4];
  		}
		
  		$response['plandata'] = $response_info;
		  $response['success'] = true;
	  }
	}
	return $response;
}

/**
 * Called for login tasks, returns an array with success = true if they logged
 * in and their autofinger list
 */
function doLoginTask() {
  $response = array("message" => "", "success" => false);
  
  $user = User::login($_POST['username'], $_POST['password']);
	if (!$user) {
		$response['message'] = "Invalid username or password.";
	} 
	else {
    $response['success'] = true;
    
		$geoip = Net_GeoIP::getInstance(GEO_DATABASE);
		try {
			$location = $geoip->lookupLocation($_SERVER['REMOTE_ADDR']);
			$user->Location->country = $location->countryCode;
			$user->Location->region = $location->region;
			$user->Location->city = $location->city;
			$user->Location->latitude = $location->latitude;
			$user->Location->longitude = $location->longitude;

			$user->Location->save();
		} catch (Exception $e) {
			// Ignore
		}
		$user->save();
		$response['autofingerList'] = getAutofingerList();
	}
	return $response;
}

/**
 * Get the autofinger list for a given user
 *
 * @return An array with one element for each autofinger level, and each element is an array of usernames
 **/
function getAutofingerList() {
  $idcookie = User::id();
  if ($idcookie === false) {
    return;
  }
  
  $af_levels = array("1", "2", "3");
  $af_list = array();
  
  foreach($af_levels as $level) {
    $container = array();
    $container['level'] = $level;
    $container['usernames'] = array();
    
    $privarray = mysql_query("Select autofinger.interest,accounts.username
		From autofinger, accounts where owner = '$idcookie' and priority =
		'$level' and updated = '1' and autofinger.interest=accounts.userid");

  	while($new_row = mysql_fetch_assoc($privarray)) {
  		$container['usernames'][] = $new_row["username"];
  	}
  	
  	$af_list[] = $container;
  }
  
  return $af_list;
}

class ErrorConsoleLogger {
  private $log;
  
  function ErrorConsoleLogger(){ 
    $log = array();
  }
  
  function addToLog($msg) {
    $this->log[] = $msg;
  }
  
  function dumpLog(){
    foreach ($this->log as $msg) {
      error_log($msg);
    }
  }
}
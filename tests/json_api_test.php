<?php
//NOTE:  This has a url listed in TestOfJSONApi which points to a local dev URL
//       This should point to your local environment.
require_once('simpletest/autorun.php');
require_once('simpletest/web_tester.php');
require_once('../Configuration.php');


class TestOfJSONApi extends WebTestCase {
    private $url;
    
    function __construct(){ //setup URL
      $this->url = __WEBROOT__ . '/json_api.php';
    }
    
    function testConnection() { //Verify the server is running
        $response = $this->get($this->url);
        $this->assertTrue(($response !== false), "Check for server up");
    }
    
    function testValidLogin() { //Verify that the login works
      $response = $this->loginAs('youngian');
      $response = json_decode($response);
      $this->assertSuccess($response);
      $this->assertTrue(is_array($response->autofingerList), "Check for autofinger list");
    }
    
    function testFailedLogin() {
      $response = $this->post($this->url."?task=login", array("username" => TEST_USER, "password" => 'incorrectpassword'));
      $response = json_decode($response);
      $this->assertFailure($response);
    }
    
    function testAutofingerListLoginRequired() {
      $response = $this->post($this->url."?task=autofingerlist");
      $response = json_decode($response);
      $this->assertFailure($response);
    }
    
    function testAutofingerList() {
      $this->loginAs('youngian');
      $response = $this->post($this->url."?task=autofingerlist");
      $response = json_decode($response);
      $this->assertSuccess($response);
    }
    
    function testLoginRequiredRead() {
      $response = $this->post($this->url."?task=read", array("username"=>'johnso58'));
      $response = json_decode($response);
      $this->assertFailure($response);
    }
    
    function testReadingCompletePlan() {
      $this->loginAs('youngian');
      $response = $this->post($this->url."?task=read", array("username"=>'wrightjo'));
      $response = json_decode($response);
      $this->assertSuccess($response);
      $this->assertTrue(($response->partial == false), "checking for full response");
      $this->assertTrue(is_object($response->plandata), "returning plan data");
    }
    
    function testReadingPartialPlan() {
      $this->loginAs('youngian');
      $response = $this->post($this->url."?task=read", array("username"=>'johnso58', 'limitsize'=> true));
      $response = json_decode($response);
      $this->assertSuccess($response);
      $this->assertTrue(($response->plandata->partial == true), "checking for partial response");
      $this->assertTrue(is_int($response->plandata->remaining), "checking for remaining size");
      $this->assertTrue(is_object($response->plandata), "returning plan data");
    }
    
    function testReadLink() {
      $this->loginAs('youngian');
      $response = $this->post($this->url."?task=read", array("username"=>'remarjen', 'readlinkreplacement' => 'fredlinktest{username}'));
      $response = json_decode($response);
      $this->assertSuccess($response);
      $this->assertTrue(is_string($response->plandata->plan), "returning content");
      $this->assertTrue(stristr($response->plandata->plan, "fredlinktest"), "checking for replacement text");
      $this->assertFalse(stristr($response->plandata->plan, "read.php"), "checking for read.php in plan text");
    }
    
    function testReadingPartialPlanOnly() {
      $this->loginAs('youngian');
      $response = $this->post($this->url."?task=read", array("username"=>'johnso58', 'partial'=> true));
      $response = json_decode($response);
      $this->assertSuccess($response);
      $this->assertTrue(is_string($response->plandata->remainingplan), "checking returning remaining plan");
    }
    
    function testReadingPartialPlanOnlyOnShortPlan() {
      $this->loginAs('youngian');
      $response = $this->post($this->url."?task=read", array("username"=>'cow', 'partial'=> true));
      $response = json_decode($response);
      $this->assertSuccess($response);
      $this->asserttrue(empty($response->plandata->remainingplan), "checking returning remaining plan");
    }
    
    
    private function assertSuccess($response) {
      $this->assertTrue(($response->success == 1), "Check for valid response");
      $this->assertTrue(empty($response->message), "Check for empty message");
    }
    
    private function assertFailure($response) {
      $this->assertFalse($response->success, "Check for invalid response");
      $this->assertFalse(empty($response->message), "Check for message");
    }
    
    private function loginAs($username) {
        return $this->post($this->url."?task=login", array("username"=>$username, "password" => $username ));
    }
    

}

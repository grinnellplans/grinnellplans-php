<?php
require_once('simpletest/autorun.php');
require_once('simpletest/web_tester.php');
require_once('../Configuration.php');

/**
 * A Simpletest base class for Plans.
 *
 * Provides helpful functionality like logging in/out, converting URLs 
 * appropriately, and so on.
 */
class PlansTestCase extends WebTestCase {

	public function setUp() {
		$this->logIn();
	}

	public function tearDown() {
		$this->logOut();
	}

	/**
	 * Get the URL for a page
	 *
	 * This will convert links automatically to the appropriate path for the 
	 * installation under test.
	 *
	 * @param string The relative path to the file, from the Plans root
	 * @return string The URL to the page
	 */
	public function getRelative($relative_path) {
      $this->get(__WEBROOT__ . '/' . $relative_path);
	}

	/**
	 * Log in as TEST_USER
	 */
	public function logIn() {
		$this->getRelative('index.php');
		$this->setField('username', TEST_USER);
		$this->setField('password', TEST_PASSWORD);
		$this->click('Login');
	}

	/**
	 * Log out
	 */
	public function logOut() {
		$this->getRelative('index.php?logout=1');
	}

	/**
	 * Log in as a guest
	 */
	public function logInGuest() {
		// For convenience, log out of the normal user right here
		$this->logOut();
		$this->getRelative('index.php');
		$this->click('Guest');
	}

}

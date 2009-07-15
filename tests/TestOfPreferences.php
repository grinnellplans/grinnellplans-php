<?php
require_once('simpletest/autorun.php');
require_once('simpletest/web_tester.php');
require_once('../Configuration.php');


class TestOfPreferences extends WebTestCase {

	public function setUp() {
		$this->logIn();
	}

	public function tearDown() {
		$this->logOut();
	}

	/**
	 * Get the URL for a page
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

	public function logInGuest() {
		// For convenience, log out of the normal user right here
		$this->logOut();
		$this->getRelative('index.php');
		$this->click('Guest');
	}

	/*
	public function assertAccessDenied() {
		$this->assertPattern('/not allowed/i');
	}
	 */

	public function testPreferencesPage() {
		$this->getRelative('customize.php');
		$this->assertText('Preferences');
		$this->assertLink('Change Auto List');
		$this->assertLink('Change Password');
		$this->assertLink('Change Name');
		$this->assertLink('Guest Readable');
		$this->assertLink('Interfaces');
		$this->assertLink('Styles');
		$this->assertLink('Edit Text Box Size');
		$this->assertLink('Optional Links');
	}

	public function testPreferencesPageInaccessibleToGuest() {
		$this->logInGuest();
		$this->getRelative('customize.php');
		$this->assertPattern('/not allowed/i');
		// There should be none of those links
		$this->assertNoLink('Change Auto List');
		$this->assertNoLink('Change Password');
		$this->assertNoLink('Change Name');
		$this->assertNoLink('Guest Readable');
		$this->assertNoLink('Interfaces');
		$this->assertNoLink('Styles');
		$this->assertNoLink('Edit Text Box Size');
		$this->assertNoLink('Optional Links');
	}

	/* Incomplete
	public function testAutoreadPageSubmit() {
		$this->getRelative('autoread.php');
		$this->clickLink('v');
		$this->setFieldByName('109', '2'); 
		$this->click('Submit');
		$this->assertField('109', '2');
		$this->setFieldByName('109', '0'); 
		$this->click('Submit');
		$this->assertField('109', '0');
	}
	 */

	public function testAutoreadPageInaccessibleToGuest() {
		$this->logInGuest();
		$this->getRelative('autoread.php');
		$this->assertPattern('/do not have an autoread/i');
	}
}

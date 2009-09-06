<?php
require_once('PlansTestCase.php');

class TestOfPreferences extends PlansTestCase {

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

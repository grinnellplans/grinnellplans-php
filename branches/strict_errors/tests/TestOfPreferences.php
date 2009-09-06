<?php
require_once('PlansTestCase.php');

class TestOfPreferences extends PlansWebTestCase {

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

	public function testAutoreadPageSetLevel() {
		$this->getRelative('autoread.php');
		$this->clickLink('v');
		$this->setFieldByName('109', '2'); 
		$this->click('Submit');
		$this->updatePlan('vanvalke');
		$this->getRelative('home.php');
		$autoread2 = $this->getElementsBySelector('#autoread ul li.first+li ul li');
		$this->assertTrue(in_array('vanvalke', $autoread2), 'User must appear on autoread level 2');
		$autoread1 = $this->getElementsBySelector('#autoread ul li.first ul li');
		$this->assertFalse(in_array('vanvalke', $autoread1), 'User must not appear on autoread level 1');
	}

	public function testAutoreadPageDeleteLevel() {
		$this->getRelative('autoread.php');
		$this->clickLink('v');
		$this->setFieldByName('109', '0'); 
		$this->click('Submit');
		$this->updatePlan('vanvalke');
		$this->getRelative('home.php');
		$autoreads = $this->getElementsBySelector('#autoread ul ul li');
		$this->assertFalse(in_array('vanvalke', $autoreads), 'User must not appear on any autoreads');
	}

	public function testAutoreadPageInaccessibleToGuest() {
		$this->logInGuest();
		$this->getRelative('autoread.php');
		$this->assertPattern('/do not have an autoread/i');
	}
}

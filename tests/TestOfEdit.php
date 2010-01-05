<?php
require_once('PlansTestCase.php');

class TestOfEdit extends PlansWebTestCase {

    /**
     * Issue #111
     */
	public function testTextSameAfterSubmit() {
        $text = 'Here is some plan text. It should remain unchanged between edits. ';
		$this->getRelative('edit.php');
		$this->clickLink('v');
		$this->setFieldByName('plan', $text); 
		$this->click('Change Plan');
		$this->getRelative('edit.php');
        $this->assertFieldByName('plan', $text);
	}

}

<?php

class Accounts extends BaseAccounts
{
    public function setUp()
    {
        $this->hasAccessor('edit_rows', 'getEditRows');
        $this->hasAccessor('edit_cols', 'getEditCols');

        $this->hasOne('JsStatus', array('local' => 'userid',
            'foreign' => 'userid'));

        $this->hasOne('Location', array('local' => 'userid',
            'foreign' => 'user_id'));

        $this->hasOne('Plans as Plan', array('local' => 'userid',
            'foreign' => 'user_id'));
    }

    public function getEditCols() {
        $cols = $this->_get('edit_cols');
		// If it's not set, default to 70
        if ($cols < 1) {
            return 70;
        }
        return $cols;
    }

    public function getEditRows() {
        $rows = $this->_get('edit_rows');
		// If it's not set, default to 14
        if ($rows < 1) {
            return 14;
        }
        return $rows;
    }

}

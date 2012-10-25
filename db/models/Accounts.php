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

        $this->hasOne('Perms', array('local' => 'userid',
            'foreign' => 'userid'));

        $this->hasOne('Plans as Plan', array('local' => 'userid',
            'foreign' => 'user_id'));

        $this->hasMany('Autofinger as Interests', array(
            'local' => 'userid',
            'foreign' => 'owner',
        ));
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

    public function getAutofinger($updated = true) {
        // retrieve this user's autoread lists.
        //
        // returns arrays of usernames nested within an array of autoread 
        // priority levels.
        //
        // by default, show only unread plans.
        // set $updated = false to show un-updated plans.
        // or  $updated = null to show all plans.
        $id = $this->get('userid');
        $q = Doctrine_Query::create()
            ->select('f.priority, a.username')
            ->from('Autofinger f')
            ->leftJoin('f.Interest a')
            ->where('f.owner = ?', $id);
        if(!is_null($updated)) {
            $q->andWhere('f.updated = ?', $updated);
        };
            $q->orderBy('f.priority, a.changed desc');
        // perform query and organize result.
        $result = array();
        // make sure there are empty arrays for the 3 levels
        for($i = 1; $i <= 3; $i++){
            $result[$i] = array();
        };
        foreach($q->execute() as $row) {
            $result[$row->priority][] = $row->Interest->username;
        };
        return $result;

    }

}

<?php

class Accounts extends BaseAccounts
{
    public function setUp()
    {
        $this->hasAccessor('edit_rows', 'getEditRows');
        $this->hasAccessor('edit_cols', 'getEditCols');
        $this->hasAccessor('stylesheet', 'getStylesheet');
        $this->hasAccessor('interface', 'getInterface');

        $this->hasOne('JsStatus', array('local' => 'userid',
            'foreign' => 'userid'));

        $this->hasOne('Location', array('local' => 'userid',
            'foreign' => 'user_id'));

        $this->hasOne('Perms', array('local' => 'userid',
            'foreign' => 'userid'));

        $this->hasOne('Plans as Plan', array('local' => 'userid',
            'foreign' => 'user_id'));

        $this->hasOne('Display', array('local' => 'userid',
            'foreign' => 'userid'));

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

    public function getStylesheet() {
        // retrieve the path to this user's stylesheet.
        //
        // directly accessing $user->Display->Style->path won't pick 
        // up custom stylesheets, so use this method instead.
        $id = $this->get('userid');
        // check for a custom stylesheet
        $q = Doctrine_Query::create()
            ->select('s.stylesheet')
            ->from('Stylesheet s')
            ->where('s.userid = ?', $id);
        $row = $q->fetchOne();
        if ($row) {
            $css = $row['stylesheet'];
        } else {
            $q = Doctrine_Query::create()
                    ->select('d.userid, s.path')
                    ->from('Display d')
                    ->leftJoin('d.Style s')
                    ->where('d.userid = ?', $id);
            $row = $q->fetchOne();
            $css = $row->Style->path;
        };
        return $css;
    }

    public function getInterface() {
        // retrieve the path to this user's interface.
        $id = $this->get('userid');
        $q = Doctrine_Query::create()
                ->select('d.userid, i.path')
                ->from('Display d')
                ->leftJoin('d.Interface i')
                ->where('d.userid = ?', $id);
        $row = $q->fetchOne();
        return $row->Interface->path;
    }

}

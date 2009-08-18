<?php

class Accounts extends BaseAccounts
{
    public function setUp()
    {
        $this->hasOne('JsStatus', array('local' => 'userid',
            'foreign' => 'userid'));

        $this->hasOne('Location', array('local' => 'userid',
            'foreign' => 'user_id'));

        $this->hasOne('Plans as Plan', array('local' => 'userid',
            'foreign' => 'user_id'));
    }


}

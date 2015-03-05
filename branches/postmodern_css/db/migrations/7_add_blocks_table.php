<?php
class AddBlocksTable extends Doctrine_Migration_Base {
    public function migrate($direction) {
        $columns = array(
            'blocked_user_id' => array(
                'type' => 'integer',
                'length' => 3,
                'unsigned' => 1,
                'notnull' => 1,
                'default' => 0
            ),

            'blocking_user_id' => array(
                'type' => 'integer',
                'length' => 3,
                'unsigned' => 1,
                'notnull' => 1,
                'default' => 0
            )
        );
        $options = array(
            'type' => 'MyISAM',
            'indexes' => array(
                'unique' => array('fields' => array('blocking_user_id', 'blocked_user_id'), 'type' => 'unique'),
                'lover' => array('fields' => array('blocking_user_id')),
                'lovee' => array('fields' => array('blocked_user_id'))
            )
        );
        $this->table($direction,'blocks',$columns, $options);
    }
}

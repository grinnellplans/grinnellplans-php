<?php

/**
 * BasePlanlove
 * 
 * This class has been copied by Ian. Too lazy to auto-generate it.
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
 */
abstract class BaseBlock extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('blocks');
        $this->hasColumn('blocking_user_id', 'integer', 3, array(
             'type' => 'integer',
             'length' => 3,
             'unsigned' => 1,
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('blocked_user_id', 'integer', 3, array(
             'type' => 'integer',
             'length' => 3,
             'unsigned' => 1,
             'primary' => true,
             'autoincrement' => false,
             ));
    }

}

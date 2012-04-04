<?php
class PlanloveTable extends Doctrine_Migration_Base {
    public function migrate($direction) {
        $columns = array(
            'lover_id' => array(
                'type' => 'integer',
                'length' => 3,
                'unsigned' => 1,
                'notnull' => 1,
                'default' => 0
            ),

            'lovee_id' => array(
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
                'unique' => array('fields' => array('lover_id', 'lovee_id'), 'type' => 'unique'),
                'lover' => array('fields' => array('lover_id')),
                'lovee' => array('fields' => array('lovee_id'))
            )
        );
        $this->table($direction,'planlove',$columns, $options);
	
    }
/*
 * This function is disabled, because Doctrine provides no way to run it in batches and no way to free 
 * RAM associated with query sets (other than the usual end-of-script free). 
 * In testing this migration on a copy of the production database, the migration was repeatedly terminated
 * before it could complete because my dev server ran out of RAM.
 * Therefore, to load the planlove table initially, run batches in separate PHP processes.
 * A script to do so is available at /trunk/db/load_planlove.php. It accepts two args: the batch size and the page to load
    public function postUp() {
        //Hit each plan, allowing Doctrine to update the tables for us
	//Do them in batches of 100, so we don't run out of RAM.
	gc_enable();
        $batchsize = 100;
        $plans = true;
        //Yay for abusing for loops.
        for ($page = 0; $plans; $page++) {
            $plans = Doctrine_Query::create()->select('a.*, p.*')->from('Accounts a')->leftJoin('a.Plan p')->limit($batchsize)->offset($page * $batchsize)->orderBy('a.userid asc')->execute();
            foreach($plans as $plan) {
                set_time_limit(20);
                if ($plan->Plan) {
                    $plan->Plan->edit_text = $plan->Plan->edit_text;
                    $plan->Plan->save();
                }//if the user has a Plan. Not all do. (!)
            }//for each plan in the batch
            gc_collect_cycles();
        }//for each batch
    } */
}

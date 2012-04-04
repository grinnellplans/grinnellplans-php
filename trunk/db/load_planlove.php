<?php
// Script to initially load the planlove table, in batches so the server doesn't run out of RAM.
// Run it like this: $ for i in {0..75} ; do echo $i; php db/load_quicklove.php $i; done 

if (PHP_SAPI != 'cli') die();

require_once ('bootstrap.php');
if ($argc == 2 || $argc == 3) {
    $page = $argv[1];
    if ($argc = 2) {
        $batchsize = 100;
    } else {
        $batchsize = $argv[2];
    }
    $plans = Doctrine_Query::create()->select('a.*, p.*')->from('Accounts a')->leftJoin('a.Plan p')
        ->limit($batchsize)->offset($page * $batchsize)->orderBy('a.userid asc')->execute();
    foreach($plans as $plan) {
        set_time_limit(20);
        if ($plan->Plan) {
            //A trivial change, enough to force Doctrine to update itself.
            $plan->Plan->edit_text = $plan->Plan->edit_text;
            $plan->Plan->save();
        }//if the user has a Plan. Not all do. (!)
    } //foreach plan
}

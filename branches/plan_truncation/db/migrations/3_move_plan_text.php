<?php

class MovePlanText extends Doctrine_Migration_Base
{
    public function preUp()
    {
        $q = Doctrine_Query::create()
            ->select('a.userid, a.plan, a.edit_text')
            ->from('Accounts a');
        $plans = $q->execute();

        foreach ($plans as $plan) {
            $p = new Plans();
            $p->user_id = $plan->userid;
            $p->plan = $plan->plan;
            $p->edit_text = $plan->edit_text;
            $p->save();
        }
    }

    public function up()
    {
        $this->removeColumn('accounts', 'plan');
        $this->removeColumn('accounts', 'edit_text');
    }

    public function down()
    {
        $this->addColumn('accounts', 'plan', 'string', array('length' => '2147483647'));
        $this->addColumn('accounts', 'edit_text', 'string', array('length' => '2147483647'));
    }

    public function postDown()
    {
        $q = Doctrine_Query::create()
            ->select('p.user_id, p.plan, p.edit_text')
            ->from('Plans p');
        $plans = $q->execute();

        foreach ($plans as $plan) {
            $q = Doctrine_Query::create()
                ->update('Accounts')
                ->set('plan', '?', $plan->plan)
                ->set('edit_text', '?', $plan->edit_text)
                ->where('userid = ?', $plan->user_id);
            $q->execute();
        }
    }
}


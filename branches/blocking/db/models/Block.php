<?php

class Block extends BaseBlock
{
    public static function addBlock($blocker, $to_block) {
        $block = new Block();
        $block->blocking_user_id = $blocker;
        $block->blocked_user_id = $to_block;
        $block->save();
    }
    public static function removeBlock($blocker, $to_unblock) {
        Doctrine_Query::create()
            ->delete("Block b")
            ->where("b.blocking_user_id = ?", $blocker)
            ->andWhere("b.blocked_user_id = ?", $to_unblock)
            ->execute();
    }

    public static function isBlocking($blocker, $blocked) {
        $q = Doctrine_Query::create()
            ->select("count(*) as c")
            ->from("Block")
            ->where("blocking_user_id = ?", $blocker)
            ->andWhere("blocked_user_id = ?", $blocked);
        return ($q->fetchOne()->c > 0);
    }
}

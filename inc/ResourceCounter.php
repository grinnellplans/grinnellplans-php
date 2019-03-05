<?php
class ResourceCounter {
    private static function now() {
        list($utime, $time) = explode(' ', microtime());
        return ((float)$utime + (float)$time);
    }
    private static $time_statrt;
    function __construct() {
        ResourceCounter::$time_statrt = ResourceCounter::now();
    }
    public static function time_elapsed() {
        return ResourceCounter::now() - ResourceCounter::$time_statrt;
    }
}
?>
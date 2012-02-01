<?php

/*
 * Debug Class
 * 
 */
class Debug {
    private static $_debug = false;
    
    /**
     * Set debug mode
     *
     * @access public
     * @param bool $debug Set to TRUE to enable debug messages
     * @return void
     */
    public static function setDebug($debug) {
	self::$_debug = $debug;
    }
    
    public static function getDebug(){
	return self::$_debug;
    }
}

?>

<?php
/*
Plugin Name: MSD Bot Blocker
Description: Blocks bots on staging servers
Version: 0.1
Author: Catherine M OBrien Sandrick (CMOS)
Author URI: http://msdlab.com/biological-assets/catherine-obrien-sandrick/
License: GPL v2
*/

add_filter('robots_txt','msdlab_bot_blocker', 15, 2);
function msdlab_bot_blocker($output){
    $server = $_SERVER["SERVER_NAME"];
    $pattern = "@(?:[^\.]+\.)?msdlab2.com@";
    ts_data($output);
    if(preg_match($pattern, $server)){   
        $output = "User-agent: *\n";
        $output .= "Disallow: /\n";
    }   
    return $output;
}

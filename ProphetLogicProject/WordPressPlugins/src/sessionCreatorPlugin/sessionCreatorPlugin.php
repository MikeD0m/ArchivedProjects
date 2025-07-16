<?php
/*
Plugin Name: Custom Session Handler
Description: Initializes sessions for the entire site.
*/

function init_sessions() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'init_sessions');

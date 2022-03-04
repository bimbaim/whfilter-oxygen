<?php

/*
Plugin Name: OxyWhello
Author: Muhammad Ibrahim
Author URI: https://baimquraisy.com
Description: Add custom elements to Oxygen.
Version: 1.0
*/


//* Define constants
define("CT_OXYGEN_WHELLO_VERSION", 	"1.4");
define( 'OXYWHELLO_FILE', 		trailingslashit( dirname( __FILE__ ) ) . 'plugin.php' );
define( 'OXYWHELLO_DIR', 		plugin_dir_path( OXYWHELLO_FILE) );
define( 'OXYWHELLO_URL', 		plugins_url( '/', OXYWHELLO_FILE ) );


add_action('plugins_loaded', 'my_oxygen_elements_init');

function my_oxygen_elements_init()
{

    if (!class_exists('OxygenElement')) {
        return;
    }

    foreach ( glob(plugin_dir_path(__FILE__) . "elements/*.php" ) as $filename)
    {
        include $filename;
    }

}

/**
 * Enqueue Scripts & Style
 */
function oxygen_whello_scripts() {
	
    wp_enqueue_script( 'oxywhello-js', OXYWHELLO_URL . '/assets/js/isotope.min.js', array('jquery'), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'oxygen_whello_scripts' );

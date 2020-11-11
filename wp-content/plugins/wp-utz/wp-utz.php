<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       WP User Level Timezone
 * Plugin URI:        http://www.wpcode.center/user-timezone-plugin/
 * Description:       Allows the site-level timezone to be changed at the user profile level 
 * Version:           1.0.0
 * Author:            WP Code
 * Author URI:        http://www.wpcode.center/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * The code that runs during plugin activation.
 */
function activate_wp_utz() 
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-utz-activator.php';
    WP_UTZ_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wp_utz() 
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-utz-deactivator.php';
    WP_UTZ_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_utz');
register_deactivation_hook( __FILE__, 'deactivate_wp_utz');

/**
 * The core plugin class 
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-utz.php';

/**
 * Begins execution of the plugin.
 */
$wp_utz_class = new WP_UTZ();
$wp_utz_class->run();




/**
 * Returns the Date/Time offset for user input which is assumed to be in the
 * user timezone.  Helpful for mysql date queries
 *
 * If the WP site TZ is UTC+0 and users TZ is UTC-6, passing in and date/time 
 * of '2020-06-04 12:00:00' will return date/time of '2020-06-04 18:00:00' 
 *
 * @param string   $datetime Valid Date/time string, Assumes this date/time is 
 *                           related to user's TZ
 * @param string   $format   Optional. '', 'mysql', 'timestamp', custom format
 *                           ie, 'm/d/Y'.  If blank then WP date time formating
 *                           is used
 *
 * @return string: Integer if $format is 'timestamp', string otherwise.
 */
function wp_utz_input_offset($datetime, $format = '')
{
    global $wp_utz_class;
    
    if (empty($datetime))
    {
        $datetime = current_time('mysql');
    }

    return $wp_utz_class->admin->input_offset($datetime, $format);
}

/**
 * Returns the GMT offset based on the user's settings
 *
 * If the user has a timezone set in their profile ther GMT offset 
 * will be returned. If not, the WP sites GMT offset will be returned
 *
 * @param boleen   $display  Optional. 1 or 0, default 1, if true then label is 
 *                           returned with offset value
 * @param string   $label    Optional. Formated string used for return
 *
 * @return string: If $display then label + offset, ie: 'GMT-6', otherwise
 *                 just offset value, ie: '-6'
 */
function wp_utz_tz_offset($display = 1, $label = 'GMT')
{
    global $wp_utz_class;
    return $wp_utz_class->admin->tz_offset($display, $label);
}

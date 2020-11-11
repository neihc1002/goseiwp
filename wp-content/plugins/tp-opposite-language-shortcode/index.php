<?php
/*
Plugin Name: TranslatePress - Show opposite language in language switcher
Plugin URI: https://translatepress.com/
Description: The language switcher shortcode will show the opposite language instead of the current language. This feature works if there is only one additional language besides default language.
Version: 1.0.0
Author: Cozmoslabs, Razvan Mocanu
Author URI: https://cozmoslabs.com/
Text Domain: translatepress-multilingual
Domain Path: /languages
License: GPL2

== Copyright ==
Copyright 2019 Cozmoslabs (www.cozmoslabs.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

add_action('wp_enqueue_scripts', 'trpc_op_ls_enqueue_style');
function trpc_op_ls_enqueue_style(){
    wp_enqueue_style('trp-opposite-language', plugin_dir_url(__FILE__) . 'tp-opposite-language.css', array('trp-language-switcher-style'), '1.0.0');
}


add_filter( 'trp_ls_shortcode_current_language', 'trpc_op_ls_current_language', 10, 4 );
function trpc_op_ls_current_language( $current_language, $published_languages, $TRP_LANGUAGE, $settings ){
    if ( count ( $published_languages ) == 2 ) {
        foreach ($published_languages as $code => $name) {
            if ($code != $TRP_LANGUAGE) {
                $current_language['code'] = $code;
                $current_language['name'] = $name;
                break;
            }
        }
    }
    return $current_language;
}

add_filter( 'trp_ls_shortcode_other_languages', 'trpc_op_ls_other_language', 10, 4 );
function trpc_op_ls_other_language( $other_language, $published_languages, $TRP_LANGUAGE, $settings ){
    if ( count ( $published_languages ) == 2 ) {
        $other_language = array();
        foreach ($published_languages as $code => $name) {
            if ($code != $TRP_LANGUAGE) {
                $other_language[$code] = $name;
                break;
            }
        }
    }
    return $other_language;
}

add_filter( 'trp_ls_shortcode_show_disabled_language', 'trp_op_ls_hide_disabled_language', 10, 4 );
function trp_op_ls_hide_disabled_language($return, $current_language, $current_language_preference, $settings){
    if ( count( $settings['publish-languages'] ) == 2 ){
        return false;
    }
    return $return;
}


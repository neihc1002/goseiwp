<?php
/**
 * Plugin Name: Masker for Elementor
 * Plugin URI: https://1.envato.market/maskerelementor
 * Description: Masker masks your images by creative & extra-ordinary custom shapes.
 * Author: Merkulove
 * Version: 1.0.0
 * Author URI: https://1.envato.market/7BP55
 * Requires PHP: 5.6
 * Requires at least: 3.0
 * Tested up to: 5.4
 **/

/**
 * Masker masks your images by creative & extra-ordinary custom shapes.
 * Exclusively on Envato Market: https://1.envato.market/maskerelementor
 *
 * @encoding        UTF-8
 * @version         1.0.0
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Envato License https://1.envato.market/KYbje
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/** Include plugin autoloader for additional classes. */
require __DIR__ . '/src/autoload.php';

use Elementor\Plugin;
use Merkulove\MaskerElementor\EnvatoItem;
use Merkulove\MaskerElementor\Helper;
use Merkulove\MaskerElementor\PluginHelper;
use Merkulove\MaskerElementor\PluginUpdater;
use Merkulove\MaskerElementor\Settings;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * SINGLETON: Core class used to implement a Masker plugin.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since 1.0.0
 **/
final class MaskerElementor {

	/**
	 * Plugin version.
	 *
	 * @string version
	 * @since 1.0.0
	 **/
	public static $version = '';

	/**
	 * Use minified libraries if SCRIPT_DEBUG is turned off.
	 *
	 * @since 1.0.0
	 **/
	public static $suffix = '';

	/**
	 * URL (with trailing slash) to plugin folder.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $url = '';

	/**
	 * PATH to plugin folder.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $path = '';

	/**
	 * Plugin base name.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $basename = '';

    /**
     * The one true Masker.
     *
     * @var MaskerElementor
     * @since 1.0.0
     **/
    private static $instance;

    /**
     * Sets up a new plugin instance.
     *
     * @since 1.0.0
     * @access public
     **/
    private function __construct() {

	    /** Initialize main variables. */
	    $this->initialization();

	    /** Define admin hooks. */
	    $this->admin_hooks();

	    /** Define hooks that runs on both the front-end as well as the dashboard. */
	    $this->both_hooks();

    }

	/**
	 * Define hooks that runs on both the front-end as well as the dashboard.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	private function both_hooks() {

		/** Load translation. */
		add_action( 'plugins_loaded', [$this, 'load_textdomain'] );

		/** Register custom widgets. */
		add_action( 'elementor/widgets/widgets_registered', [$this, 'register_widgets'] );

    }

	/**
	 * Register all of the hooks related to the admin area functionality.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	private function admin_hooks() {

		/** Add plugin settings page. */
		Settings::get_instance()->add_settings_page();

		/** Load JS and CSS for Backend Area. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

		/** Elementor widget Editor CSS. */
		add_action( 'elementor/editor/before_enqueue_styles', [$this, 'editor_styles'] );

		/** Remove all "third-party" notices from plugin settings page. */
		add_action( 'in_admin_header', [$this, 'remove_all_notices'], 1000 );

		/** Remove "Thank you for creating with WordPress" and WP version only from plugin settings page. */
		add_action( 'admin_enqueue_scripts', [$this, 'remove_wp_copyrights'] );

    }

	/**
	 * Add our css to Elementor admin editor.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function editor_styles() {
		wp_enqueue_style( 'mdp-masker-elementor-elementor-admin', MaskerElementor::$url . 'css/elementor-admin' . MaskerElementor::$suffix . '.css', [], MaskerElementor::$version );
	}

	/**
	 * Remove all other notices.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function remove_all_notices() {

		/** Work only on plugin settings page. */
		$screen = get_current_screen();

		if ( $screen->base != "settings_page_mdp_masker_elementor_settings" ) { return; }

		/** Remove other notices. */
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

	}

	/**
	 * Initialize main variables.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function initialization() {

		/** Plugin version. */
		if ( ! function_exists('get_plugin_data') ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$plugin_data = get_plugin_data( __FILE__ );
		self::$version = $plugin_data['Version'];

		/** Gets the plugin URL (with trailing slash). */
		self::$url = plugin_dir_url( __FILE__ );

		/** Gets the plugin PATH. */
		self::$path = plugin_dir_path( __FILE__ );

		/** Use minified libraries if SCRIPT_DEBUG is turned off. */
		self::$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		/** Set plugin basename. */
		self::$basename = plugin_basename( __FILE__ );

		/** Initialize plugin settings. */
		Settings::get_instance();

		/** Initialize PluginHelper. */
		PluginHelper::get_instance();

		/** Allow SVG files in the media library. */
		add_filter( 'upload_mimes', [ $this, 'allow_svg_uploads' ], 1, 1 );

		/** Plugin update mechanism enable only if plugin have Envato ID. */
		$plugin_id = EnvatoItem::get_instance()->get_id();
		if ( (int)$plugin_id > 0 ) {
			PluginUpdater::get_instance();
        }

	}

	/**
	 * Register new Elementor widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function register_widgets() {

		/** Check if Elementor installed and activated. */
		if ( ! did_action( 'elementor/loaded' ) ) { return; }

		/** Load and register Elementor widgets. */
		$path = MaskerElementor::$path . 'src/Merkulove/MaskerElementor/Elementor/widgets/';

		/**  Will store a list of chart file names. */
		$nameChart = array();

		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ) ) as $filename ) {
			if ( substr( $filename, -4 ) == '.php' ) {
				/** @noinspection PhpIncludeInspection */
				require_once $filename;

				/** Prepare class name from file. */
				$widget_class = $filename->getBasename( '.php' );
				$widget_class = str_replace( '.', '_', $widget_class );

				/** We write all the file names to the array. */
				$nameChart[] = $widget_class;
			}
		}

		/** We sort the array with the names of the widgets. */
		sort( $nameChart );

		/** Register chart widget . */
		foreach ( $nameChart as $chart){
			$chart = '\\' .substr( $chart, 3 );

			Plugin::instance()->widgets_manager->register_widget_type( new $chart() );
		}

	}

	/**
	 * Return plugin version.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 **/
	public function get_version() {
		return self::$version;
	}

	/**
	 * Remove "Thank you for creating with WordPress" and WP version only from plugin settings page.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return void
	 **/
	public function remove_wp_copyrights() {

		/** Remove "Thank you for creating with WordPress" and WP version from plugin settings page. */
		$screen = get_current_screen(); // Get current screen.

        /** Logger Settings Page. */
        $bases = [
            'elementor_page_mdp_masker_elementor_settings',
            'settings_page_mdp_masker_elementor_settings'
        ];

        /** Plugin Settings Page. */
        if ( in_array( $screen->base, $bases ) ) {
			add_filter( 'admin_footer_text', '__return_empty_string', 11 );
			add_filter( 'update_footer', '__return_empty_string', 11 );
		}

	}

    /**
     * Loads the Masker translated strings.
     *
     * @since 1.0.0
     * @access public
     **/
    public function load_textdomain() {

        load_plugin_textdomain( 'masker-elementor', false, self::$path . '/languages/' );

    }

    /**
     * Add CSS for admin area.
     *
     * @since 1.0.0
     * @return void
     **/
    public function admin_styles() {

        /** Add styles only on setting page */
        $screen = get_current_screen();

        /** Settings Page. */
        $bases = [
	        'elementor_page_mdp_masker_elementor_settings',
	        'settings_page_mdp_masker_elementor_settings'
        ];

        if ( in_array( $screen->base, $bases ) ) {

		    wp_enqueue_style( 'merkulov-ui', self::$url . 'css/merkulov-ui.min.css', [], self::$version );

	    } elseif ( 'plugin-install' == $screen->base ) {

		    /** Styles only for our plugin. */
		    if ( isset( $_GET['plugin'] ) AND $_GET['plugin'] === 'masker-elementor' ) {
			    wp_enqueue_style( 'mdp-masker-elementor-plugin-install', self::$url . 'css/plugin-install' . self::$suffix . '.css', [], self::$version );
		    }
	    }

    }

    /**
     * Add JS for admin area.
     *
     * @since 1.0.0
     * @return void
     **/
    public function admin_scripts() {

	    /** Add styles only on setting page */
	    $screen = get_current_screen();

	    /** Settings Page. */
        $bases = [
            'elementor_page_mdp_masker_elementor_settings',
            'settings_page_mdp_masker_elementor_settings'
        ];

        if ( in_array( $screen->base, $bases ) ) {
		    wp_enqueue_script( 'merkulov-ui', self::$url . 'js/merkulov-ui' . self::$suffix . '.js', [], self::$version, true );
	    }

    }

	/**
	 * Run when the plugin is activated.
	 *
	 * @static
	 * @since 1.0.0
	 **/
	public static function on_activation() {

		/** Security checks. */
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );

		/** Send install Action to our host. */
		Helper::get_instance()->send_action( 'install', 'masker-elementor', self::$version );

	}

	/**
	 * Allow SVG files in the media library.
	 *
	 * @param $mime_types - Current array of mime types.
	 *
	 * @return array - Updated array of mime types.
	 * @since 1.0.0
	 * @access public
	 */
	public function allow_svg_uploads( $mime_types ) {

		/** Adding .svg extension. */
		$mime_types['svg']  = 'image/svg+xml';
		$mime_types['svgz'] = 'image/svg+xml';

		return $mime_types;
	}

	/**
	 * Main Masker Instance.
	 *
	 * Insures that only one instance of Masker exists in memory at any one time.
	 *
	 * @static
	 * @return MaskerElementor
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof MaskerElementor ) ) {
			self::$instance = new MaskerElementor;
		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return void
	 **/
	public function __clone() {
		/** Cloning instances of the class is forbidden. */
		_doing_it_wrong( __FUNCTION__, esc_html__( 'The whole idea of the singleton design pattern is that there is a single object therefore, we don\'t want the object to be cloned.', 'masker-elementor' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be unserialized.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return void
	 **/
	public function __wakeup() {
		/** Unserializing instances of the class is forbidden. */
		_doing_it_wrong( __FUNCTION__, esc_html__( 'The whole idea of the singleton design pattern is that there is a single object therefore, we don\'t want the object to be unserialized.', 'masker-elementor' ), '1.0.0' );
	}

} // End Class Masker.

/** Run when the plugin is activated. */
register_activation_hook( __FILE__, [ 'Merkulove\MaskerElementor', 'on_activation'] );

/** Run Masker class. */
MaskerElementor::get_instance();
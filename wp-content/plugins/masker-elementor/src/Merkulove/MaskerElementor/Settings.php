<?php
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

namespace Merkulove\MaskerElementor;

use Merkulove\MaskerElementor;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement plugin settings.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class Settings {

	/**
	 * MaskerElementor Plugin settings.
	 *
	 * @var array()
	 * @since 1.0.0
	 **/
	public $options = [];

	/**
	 * The one true Settings.
	 *
	 * @var Settings
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Settings instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

	}

	/**
	 * Render Tabs Headers.
	 *
	 * @param string $current - Selected tab key.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function render_tabs( $current = 'general' ) {

		/** Tabs array. */
		$tabs = [];

		$tabs['status'] = [
			'icon' => 'info',
			'name' => esc_html__( 'Status', 'masker-elementor' )
		];

		/** Activation tab enable only if plugin have Envato ID. */
		$plugin_id = EnvatoItem::get_instance()->get_id();
		if ( (int)$plugin_id > 0 ) {
			$tabs['activation'] = [
				'icon' => 'vpn_key',
				'name' => esc_html__( 'Activation', 'masker-elementor' )
			];
        }

		$tabs['uninstall'] = [
			'icon' => 'delete_sweep',
			'name' => esc_html__( 'Uninstall', 'masker-elementor' )
		];

		/** Render Tabs. */
		?>
        <aside class="mdc-drawer">
            <div class="mdc-drawer__content">
                <nav class="mdc-list">

                    <div class="mdc-drawer__header mdc-plugin-fixed">
                        <!--suppress HtmlUnknownAnchorTarget -->
                        <a class="mdc-list-item mdp-plugin-title" href="#wpwrap">
                            <i class="mdc-list-item__graphic" aria-hidden="true">
                                <img src="<?php echo esc_attr( MaskerElementor::$url . 'images/logo-color.svg' ); ?>" alt="<?php echo esc_html__( 'MaskerElementor', 'masker-elementor' ) ?>">
                            </i>
                            <span class="mdc-list-item__text">
                                <?php echo esc_html__( 'Masker', 'masker-elementor' ) ?>
                            </span>
                            <span class="mdc-list-item__text">
                                <sup> <?php echo esc_html__( 'ver.', 'masker-elementor' ) . esc_html( MaskerElementor::$version ); ?></sup>
                            </span>
                        </a>
                        <button type="submit" name="submit" id="submit"
                                class="mdc-button mdc-button--dense mdc-button--raised">
                            <span class="mdc-button__label"><?php echo esc_html__( 'Save changes', 'masker-elementor' ) ?></span>
                        </button>
                    </div>

                    <hr class="mdc-plugin-menu">
                    <hr class="mdc-list-divider">
                    <h6 class="mdc-list-group__subheader"><?php echo esc_html__( 'Plugin settings', 'masker-elementor' ) ?></h6>

					<?php

					// Plugin settings tabs
					foreach ( $tabs as $tab => $value ) {
						$class = ( $tab == $current ) ? ' mdc-list-item--activated' : '';
						echo "<a class='mdc-list-item " . esc_attr( $class ) . "' href='?page=mdp_masker_elementor_settings&tab=" . esc_attr( $tab ) . "'><i class='material-icons mdc-list-item__graphic' aria-hidden='true'>" . esc_attr( $value['icon'] ) . "</i><span class='mdc-list-item__text'>" . esc_attr( $value['name'] ) . "</span></a>";
					}

					/** Helpful links. */
					$this->support_link();

					/** Activation Status. */
					PluginActivation::get_instance()->display_status();

					?>
                </nav>
            </div>
        </aside>
		<?php
	}

	/**
	 * Displays useful links for an activated and non-activated plugin.
	 *
	 * @since 1.0.0
     *
     * @return void
	 **/
	public function support_link() { ?>

        <hr class="mdc-list-divider">
        <h6 class="mdc-list-group__subheader"><?php echo esc_html__( 'Helpful links', 'masker-elementor' ) ?></h6>

        <a class="mdc-list-item" href="https://docs.merkulov.design/tag/masker/" target="_blank">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true">collections_bookmark</i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'Documentation', 'masker-elementor' ) ?></span>
        </a>

		<?php if ( PluginActivation::get_instance()->is_activated() ) : /** Activated. */ ?>
            <a class="mdc-list-item" href="https://1.envato.market/masker-elementor-support" target="_blank">
                <i class="material-icons mdc-list-item__graphic" aria-hidden="true">mail</i>
                <span class="mdc-list-item__text"><?php echo esc_html__( 'Get help', 'masker-elementor' ) ?></span>
            </a>
            <a class="mdc-list-item" href="https://1.envato.market/cc-downloads" target="_blank">
                <i class="material-icons mdc-list-item__graphic" aria-hidden="true">thumb_up</i>
                <span class="mdc-list-item__text"><?php echo esc_html__( 'Rate this plugin', 'masker-elementor' ) ?></span>
            </a>
		<?php endif; ?>

        <a class="mdc-list-item" href="https://1.envato.market/cc-merkulove" target="_blank">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true">store</i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'More plugins', 'masker-elementor' ) ?></span>
        </a>
		<?php

	}

	/**
	 * Add plugin settings page.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function add_settings_page() {

		add_action( 'admin_menu', [ $this, 'add_admin_menu' ], 1000 );
		add_action( 'admin_init', [ $this, 'settings_init' ] );

	}

	/**
	 * Generate Settings Page.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function settings_init() {

		/** Status Tab. */
		StatusTab::get_instance()->add_settings();

		/** Activation Tab. */
		PluginActivation::get_instance()->add_settings();

		/** Uninstall Tab. */
		UninstallTab::get_instance()->add_settings();

	}

	/**
	 * Add admin menu for plugin settings.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function add_admin_menu() {

        /** Check if Elementor installed and activated. */
        $parent = 'options-general.php';
        if ( did_action( 'elementor/loaded' ) ) {
            $parent = 'elementor';
        }

		add_submenu_page(
            $parent,
			esc_html__( 'Masker for Elementor Settings', 'masker-elementor' ),
			esc_html__( 'Masker for Elementor', 'masker-elementor' ),
			'manage_options',
			'mdp_masker_elementor_settings',
			[ $this, 'options_page' ]
		);

	}

	/**
	 * Plugin Settings Page.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function options_page() {

		/** User rights check. */
		if ( ! current_user_can( 'manage_options' ) ) { return; } ?>

        <!--suppress HtmlUnknownTarget -->
        <form action='options.php' method='post'>
            <div class="wrap">

				<?php
				$tab = 'status'; // Default tab
				if ( isset ( $_GET['tab'] ) ) { $tab = $_GET['tab']; }

				/** Render "MaskerElementor settings saved!" message. */
				$this->render_nags();

				/** Render Tabs Headers. */
				?><section class="mdp-aside"><?php $this->render_tabs( $tab ); ?></section><?php

				/** Render Tabs Body. */
				?><section class="mdp-tab-content"><?php

                    /** Activation Tab. */
                    if ( $tab == 'activation' ) {
						settings_fields( 'MaskerElementorActivationOptionsGroup' );
						do_settings_sections( 'MaskerElementorActivationOptionsGroup' );
						PluginActivation::get_instance()->render_pid();

                    /** Status tab. */
					} elseif ( $tab == 'status' ) {
						echo '<h3>' . esc_html__( 'System Requirements', 'masker-elementor' ) . '</h3>';
						StatusTab::get_instance()->render_form();

                    /** Uninstall Tab. */
					} elseif ( $tab == 'uninstall' ) {
						echo '<h3>' . esc_html__( 'Uninstall Settings', 'masker-elementor' ) . '</h3>';
						UninstallTab::get_instance()->render_form();
					}

					?>
                </section>
            </div>
        </form>

		<?php
	}

	/**
	 * Render "Settings Saved" nags.
	 *
	 * @since 1.0.0
     *
     * @return void
	 **/
	public function render_nags() {

		if ( ! isset( $_GET['settings-updated'] ) ) { return; }

		if ( strcmp( $_GET['settings-updated'], "true" ) == 0 ) {

			/** Render "Settings Saved" message. */
			UI::get_instance()->render_snackbar( esc_html__( 'Settings saved!', 'masker-elementor' ) );

		}

		if ( ! isset( $_GET['tab'] ) ) { return; }

		if ( strcmp( $_GET['tab'], "activation" ) == 0 ) {

			if ( PluginActivation::get_instance()->is_activated() ) {

				/** Render "Activation success" message. */
				UI::get_instance()->render_snackbar( esc_html__( 'Plugin activated successfully.', 'masker-elementor' ), 'success', 5500 );

			} else {

				/** Render "Activation failed" message. */
				UI::get_instance()->render_snackbar( esc_html__( 'Invalid purchase code.', 'masker-elementor' ), 'error', 5500 );

			}

		}

	}

	/**
	 * Main Settings Instance.
	 *
	 * Insures that only one instance of Settings exists in memory at any one time.
	 *
	 * @static
	 * @return Settings
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Settings ) ) {
			self::$instance = new Settings;
		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access protected
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
	 * @return void
	 * @since 1.0.0
	 * @access protected
	 **/
	public function __wakeup() {
		/** Unserializing instances of the class is forbidden. */
		_doing_it_wrong( __FUNCTION__, esc_html__( 'The whole idea of the singleton design pattern is that there is a single object therefore, we don\'t want the object to be unserialized.', 'masker-elementor' ), '1.0.0' );
	}

} // End Class Settings.

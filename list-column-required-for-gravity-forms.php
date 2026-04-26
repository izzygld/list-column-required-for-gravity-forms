<?php
/**
 * Plugin Name: List Column Required for Gravity Forms
 * Plugin URI: https://github.com/izzygld/list-column-required-for-gravity-forms
 * Description: Adds per-column required validation to Gravity Forms List fields with multiple columns. Mark individual columns as required right in the form editor.
 * Version: 1.1.0
 * Author: izzygld
 * Author URI: https://github.com/izzygld
 * Text Domain: list-column-required-for-gravity-forms
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// dont let ppl access this file directly thats bad
defined( 'ABSPATH' ) || exit;

// settin up all our constant values for the plugin
define( 'LCR_GF_VERSION', '1.1.0' );
define( 'LCR_GF_MIN_GF_VERSION', '2.5' );
define( 'LCR_GF_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'LCR_GF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * checkin if gravity forms is installed on the site
 * if its not there we gotta show an error and turn off the plugin
 */
function lcr_gf_check_gf_installed() {
    // lookin for gravity forms class to see if its active
    if ( ! class_exists( 'GFForms' ) ) {
        add_action( 'admin_notices', 'lcr_gf_show_missing_gf_error' );
        add_action( 'admin_init', 'lcr_gf_turn_off_plugin' );
        return false;
    }
    return true;
}

/**
 * showin the admin error when gravity forms aint there
 * this pops up at the top of the admin area
 */
function lcr_gf_show_missing_gf_error() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p>
            <strong><?php esc_html_e( 'List Column Required for Gravity Forms', 'list-column-required-for-gravity-forms' ); ?>:</strong>
            <?php
            printf(
                /* translators: %s: Gravity Forms plugin name */
                esc_html__( 'This plugin requires %s to be installed and activated. The plugin has been deactivated.', 'list-column-required-for-gravity-forms' ),
                '<strong>Gravity Forms</strong>'
            );
            ?>
        </p>
    </div>
    <?php
}

/**
 * turnin off the plugin if gravity forms isnt there
 * we dont want it runnin without its dependency ya know
 */
function lcr_gf_turn_off_plugin() {
    // only do this if were in admin and user can activate plugins
    if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );

        // get rid of the "Plugin activated" message cuz its misleading
        if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        }
    }
}

// runnin our dependency check early before gf loads
add_action( 'plugins_loaded', 'lcr_gf_check_gf_installed', 1 );

/**
 * this class handles loadin up the whole plugin
 * its like the main entry point that gets everything started
 * followin the gf addon framework patterns from their docs
 */
class LCR_GF_Bootstrap {

    /**
     * loads up da addon when gravity forms is ready to go
     * this is where all the magic starts happenin
     *
     * @return void
     */
    public static function fire_it_up() {
        // makin sure the gf addon framework stuff is available
        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }

        // bringin in the addon framework
        GFForms::include_addon_framework();

        // loadin up our helper classes
        require_once LCR_GF_PLUGIN_PATH . 'includes/class-list-column-validator.php';
        require_once LCR_GF_PLUGIN_PATH . 'includes/class-list-column-frontend.php';
        require_once LCR_GF_PLUGIN_PATH . 'class-lcr-gf-addon.php';

        // registerin the addon with gravity forms
        GFAddOn::register( 'LCR_GF_Addon' );
    }

    /**
     * grabbin da addon instance so we can use it elsewhere
     *
     * @return LCR_GF_Addon|null
     */
    public static function grab_da_instance() {
        return class_exists( 'LCR_GF_Addon' ) ? LCR_GF_Addon::get_instance() : null;
    }
}

// hookin into gravity forms load event to fire up our addon
add_action( 'gform_loaded', array( 'LCR_GF_Bootstrap', 'fire_it_up' ), 5 );

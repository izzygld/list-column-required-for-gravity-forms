<?php
/**
 * Main List Column Required for Gravity Forms Addon Class
 *
 * this is where all the addon magic happens
 * extends GFAddOn to hook into the form editor and validation
 *
 * @package LCR_GF
 */

// dont let anyone access this directly
defined( 'ABSPATH' ) || exit;

/**
 * LCR_GF_Addon class
 *
 * followin the GFAddOn pattern from the gf docs
 * this handles script enqueuing, validation hookup, and frontend rendering
 */
class LCR_GF_Addon extends GFAddOn {

    /**
     * holds an instance of this class, if we got one
     *
     * @var LCR_GF_Addon|null
     */
    private static $_instance = null;

    /**
     * addon version number
     *
     * @var string
     */
    protected $_version = LCR_GF_VERSION;

    /**
     * minimum gf version we need to work
     *
     * @var string
     */
    protected $_min_gravityforms_version = LCR_GF_MIN_GF_VERSION;

    /**
     * url-safe addon slug, gotta be max 33 chars
     *
     * @var string
     */
    protected $_slug = 'list-column-required-for-gravity-forms';

    /**
     * path to plugin from the plugins folder
     *
     * @var string
     */
    protected $_path = 'list-column-required-for-gravity-forms/list-column-required-for-gravity-forms.php';

    /**
     * full path to the main plugin file
     *
     * @var string
     */
    protected $_full_path = __FILE__;

    /**
     * the full title of our addon
     *
     * @var string
     */
    protected $_title = 'List Column Required for Gravity Forms';

    /**
     * shorter title for menus n stuff
     *
     * @var string
     */
    protected $_short_title = 'List Column Required';

    /**
     * our validator instance for checkin required columns
     *
     * @var LCR_GF_Validator
     */
    public $validator;

    /**
     * our frontend handler for renderin required indicators
     *
     * @var LCR_GF_Frontend
     */
    public $frontend;

    /**
     * gets the singleton instance of this class
     * creates it if it dont exist yet
     *
     * @return LCR_GF_Addon
     */
    public static function get_instance() {
        if ( null === self::$_instance ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * minimum requirements to run this addon
     * checkin php version and gf version
     *
     * @return array
     */
    public function minimum_requirements() {
        return array(
            'gravityforms' => array(
                'version' => $this->_min_gravityforms_version,
            ),
            'php'          => array(
                'version' => '7.4',
            ),
        );
    }

    /**
     * runs before wordpress init kicks off
     * settin up our handler instances early
     *
     * @return void
     */
    public function pre_init() {
        parent::pre_init();

        // spinnin up our handler classes
        $this->validator = new LCR_GF_Validator();
        $this->frontend  = new LCR_GF_Frontend();
    }

    /**
     * init method that runs on all pages
     * hookin up our validation filter here
     *
     * @return void
     */
    public function init() {
        parent::init();

        // hookin up the server-side validation for list column required checks
        $this->validator->hookup();

        // register render-time flags so the required legend + label asterisk show up
        // in both frontend and admin contexts (e.g. entry view)
        $this->frontend->hookup_render_flags();
    }

    /**
     * init frontend runs only on the public-facing site
     * hookin up the required indicators and attributes here
     *
     * @return void
     */
    public function init_frontend() {
        parent::init_frontend();

        // addin required indicators to column headers and inputs
        $this->frontend->hookup();
    }

    /**
     * scripts we need to load up
     * enqueuin our admin js for the form editor
     *
     * @return array
     */
    public function scripts() {
        $da_scripts = array(
            array(
                'handle'  => 'lcr_gf_admin',
                'src'     => $this->get_base_url() . '/assets/js/admin.js',
                'version' => $this->_version,
                'deps'    => array( 'jquery' ),
                'enqueue' => array(
                    array(
                        'admin_page' => array( 'form_editor' ),
                    ),
                ),
            ),
        );

        return array_merge( parent::scripts(), $da_scripts );
    }

    /**
     * styles we need to load up
     * enqueuin our admin css for the form editor
     *
     * @return array
     */
    public function styles() {
        $da_styles = array(
            array(
                'handle'  => 'lcr_gf_admin',
                'src'     => $this->get_base_url() . '/assets/css/admin.css',
                'version' => $this->_version,
                'enqueue' => array(
                    array(
                        'admin_page' => array( 'form_editor' ),
                    ),
                ),
            ),
        );

        return array_merge( parent::styles(), $da_styles );
    }
}

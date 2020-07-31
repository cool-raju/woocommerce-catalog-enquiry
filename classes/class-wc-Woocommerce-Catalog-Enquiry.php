<?php
class WC_Woocommerce_Catalog_Enquiry {

	public $plugin_url;

	public $plugin_path;

	public $version;

	public $token;
	
	public $text_domain;
	
	public $library;

	public $shortcode;

	public $admin;

	public $frontend;

	public $template;

	public $ajax;

	private $file;
	
	public $settings;
	
	public $dc_wp_fields;
	
	public $options;
	
	public $options_exclusion ;
	
	public $option_button;

	public function __construct($file) {

		$this->file = $file;
		$this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
		$this->plugin_path = trailingslashit(dirname($file));
		$this->token = WC_WOOCOMMERCE_CATALOG_ENQUIRY_PLUGIN_TOKEN;
		$this->text_domain = WC_WOOCOMMERCE_CATALOG_ENQUIRY_TEXT_DOMAIN;
		$this->version = WC_WOOCOMMERCE_CATALOG_ENQUIRY_PLUGIN_VERSION;
		// default gen setting
		$this->options = get_option('dc_wc_Woocommerce_Catalog_general');	
		// from_setting
		$this->option_gen = get_option('dc_wc_Woocommerce_Catalog_from_settings_name');
		// exclusion setting
		$this->options_exclusion = get_option('dc_wc_Woocommerce_Catalog_Enquiry_exclusion_settings_name');
		// button appearence
		$this->option_button = get_option('dc_wc_Woocommerce_Catalog_button_appear');
		add_action('init', array(&$this, 'init'), 0);
		// Catalog Email setup
		add_filter('woocommerce_email_classes', array(&$this, 'wc_catalog_enquiry_email_setup' ));
	}
	
	/**
	 * initilize plugin on WP init
	 */
	function init() {
		
		// Init Text Domain
		$this->load_plugin_textdomain();
		
		// Init library
		$this->load_class('library');
		$this->library = new WC_Woocommerce_Catalog_Enquiry_Library();

		// Init ajax
		if(defined('DOING_AJAX')) {
	      	$this->load_class('ajax');
	      	$this->ajax = new  WC_Woocommerce_Catalog_Enquiry_Ajax();
	    }

		if (is_admin()) {
			$this->load_class('admin');
			$this->admin = new WC_Woocommerce_Catalog_Enquiry_Admin();
		}

		if (!is_admin() || defined('DOING_AJAX')) {
			$this->load_class('frontend');
			$this->frontend = new WC_Woocommerce_Catalog_Enquiry_Frontend();
		}

		// DC Wp Fields
		$this->dc_wp_fields = $this->library->load_wp_fields();

	}
	
	/**
   * Load Localisation files.
   *
   * Note: the first-loaded translation file overrides any following ones if the same translation is present
   *
   * @access public
   * @return void
   */
  	public function load_plugin_textdomain() {
  		$locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'woocommerce-catalog-enquiry');
        load_textdomain('woocommerce-catalog-enquiry', WP_LANG_DIR . '/woocommerce-catalog-enquiry/woocommerce-catalog-enquiry-' . $locale . '.mo');
        load_plugin_textdomain('woocommerce-catalog-enquiry', false, plugin_basename(dirname(dirname(__FILE__))) . '/languages');
  	}

	public function load_class($class_name = '') {
		if ('' != $class_name && '' != $this->token) {
			require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}// End load_class()

	/**
	 * Add WC Catalog Email
	 *
	 * @param emails     default email classes
	 * @return modified email classes
	 */ 
	function wc_catalog_enquiry_email_setup( $emails ) {
		require_once( 'emails/class-wc-catalog-enquiry-email.php' );
		$emails['WC_Catalog_Enquiry_Email'] = new WC_Catalog_Enquiry_Email();
		
		return $emails;
	}
	
	/** Cache Helpers *********************************************************/

	/**
	 * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
	 *
	 * @access public
	 * @return void
	 */
	function nocache() {
		if (!defined('DONOTCACHEPAGE'))
			define("DONOTCACHEPAGE", "true");
		// WP Super Cache constant
	}

}

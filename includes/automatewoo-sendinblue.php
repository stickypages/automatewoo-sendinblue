<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AutomateWoo\Addon' ) ) {
	include WP_PLUGIN_DIR . '/automatewoo/includes/abstracts/addon.php';
}

class AW_SendInBlue_Addon extends AutomateWoo\Addon {

	/** @var AW_SendInBlue_Options */
	private $options;

	/** @var AW_SendInBlue_Admin */
	public $admin;

	/** @var AutomateWoo\SendInBlue\API */
	private $api;


	/**
	 * @param AW_Referrals_Plugin_Data $plugin_data
	 */
	public function __construct( $plugin_data ) {
		parent::__construct( $plugin_data );
	}


	/**
	 * Initiate
	 */
	public function init() {

		$this->includes();

		new AW_SendInBlue_Workflows();

		if ( is_admin() ) {
			$this->admin = new AW_SendInBlue_Admin();
		}

		do_action( 'automatewoo/SendInBlue/after_init' );
	}


	/**
	 * Includes
	 */
	public function includes() {
		
		include_once $this->path( '/includes/workflows.php' );

		if ( is_admin() ) {
			include_once $this->path( '/includes/admin.php' );
		}
	}



	/**
	 * @return AW_SendInBlue_Options
	 */
	public function options() {
		if ( ! isset( $this->options ) ) {
			include_once $this->path( '/includes/options.php' );
			$this->options = new AW_SendInBlue_Options();
		}

		return $this->options;
	}


	/**
	 * @return AutomateWoo\SendInBlue\API|false
	 */
	public function api() {
		if ( ! isset( $this->api ) ) {
			include_once $this->path( '/includes/api.php' );

			// $api_domain = AutomateWoo\Clean::string( $this->options()->api_domain );
			// $api_email = AutomateWoo\Clean::string( $this->options()->api_email );
			$api_key = AutomateWoo\Clean::string( $this->options()->api_key );

			// $api_domain = str_replace( [ 'http://', 'https://' ], '', $api_domain );

			if ( $api_key ) {
				$this->api = new AutomateWoo\SendInBlue\API( $api_key );
			}
			else {
				$this->api = false;
			}
		}

		return $this->api;
	}


	/** @var AW_SendInBlue_Addon */
	protected static $_instance;


}


/**
 * @return AW_SendInBlue_Addon
 */
function AW_SendInBlue() {
	return AW_SendInBlue_Addon::instance( new AW_SendInBlue_Plugin_Data() );
}
AW_SendInBlue();

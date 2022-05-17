<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AutomateWoo\Addon' ) ) {
	include WP_PLUGIN_DIR . '/automatewoo/includes/abstracts/addon.php';
}

class AW_StickyBlue_Addon extends AutomateWoo\Addon {

	/** @var AW_StickyBlue_Options */
	private $options;

	/** @var AW_StickyBlue_Admin */
	public $admin;

	/** @var StickyBlueAutomateWoo\API */
	private $api;

    public $api_key;


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

		new AW_StickyBlue_Workflows();

		if ( is_admin() ) {
			$this->admin = new AW_StickyBlue_Admin();
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
	 * @return AW_StickyBlue_Options
	 */
	public function options() {
		if ( ! isset( $this->options ) ) {
			include_once $this->path( '/includes/options.php' );
			$this->options = new AW_StickyBlue_Options();
		}

		return $this->options;
	}


	/**
	 * @return StickyBlueAutomateWoo\StickyBlue\API|false
	 */
	public function api() {
		if ( ! isset( $this->api ) ) {
			include_once $this->path( '/includes/api.php' );
            $this->api = false;

            $this->api_key = AutomateWoo\Clean::string( $this->options()->api_key );

			if ( $this->api_key ) {
				$this->api = new StickyBlueAutomateWoo\API( $this->api_key );
			}
		}

		return $this->api;
	}


	/** @var AW_StickyBlue_Addon */
	protected static $_instance;


}


/**
 * @return AW_StickyBlue_Addon
 */
function AW_SendInBlue() {
	return AW_StickyBlue_Addon::instance( new AW_StickyBlue_Plugin_Data() );
}
AW_SendInBlue();

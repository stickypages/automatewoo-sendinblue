<?php
/**
 * Plugin Name: AutomateWoo - SendInBlue Add-on
 * Description: SendInBlue Integration add-on for AutomateWoo / Elementor.
 * Version: 1.0.0
 * Author: StickyPages
 * Author URI: https://stickypages.ca
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
 * Text Domain: automatewoo-sendinblue
 *
 */


// Copyright (c) StickyPages. All rights reserved.
//
// Released under the GPLv3 license
// http://www.gnu.org/licenses/gpl-3.0
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************


if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * @class AW_SendInBlue_Plugin_Data
 */
class AW_SendInBlue_Plugin_Data {

	function __construct() {
		$this->id = 'automatewoo-sendinblue';
		$this->name = __( 'AutomateWoo - SendInBlue', 'automatewoo-sendinblue' );
		$this->version = '1.0.0';
		$this->file = __FILE__;
		$this->min_php_version = '5.4';
		$this->min_automatewoo_version = '4.3.0';
		$this->min_woocommerce_version = '3.0.0';
	}
}



/**
 * @class AW_SendInBlue_Loader
 */
class AW_SendInBlue_Loader {

	/** @var AW_SendInBlue_Plugin_Data */
	static $data;

	static $errors = array();


	/**
	 * @param AW_SendInBlue_Plugin_Data $data
	 */
	static function init( $data ) {
		self::$data = $data;

		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'load' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'load_textdomain' ) );
	}


	static function load() {
		self::check();
		if ( empty( self::$errors ) ) {
			include 'includes/automatewoo-sendinblue.php';
		}
	}


	static function check() {

		$inactive_text = '<strong>' . sprintf( __( '%s is inactive.', 'automatewoo-sendinblue' ), self::$data->name ) . '</strong>';

		if ( version_compare( phpversion(), self::$data->min_php_version, '<' ) ) {
			self::$errors[] = sprintf( __( '%s The plugin requires PHP version %s or newer.' , 'automatewoo-sendinblue' ), $inactive_text, self::$data->min_php_version );
		}

		if ( ! self::is_automatewoo_active() ) {
			self::$errors[] = sprintf( __( '%s The plugin requires AutomateWoo to be installed and activated.' , 'automatewoo-sendinblue' ), $inactive_text );
		}
		elseif ( ! self::is_automatewoo_version_ok() ) {
			self::$errors[] = sprintf(__( '%s The plugin requires AutomateWoo version %s or newer.', 'automatewoo-sendinblue' ), $inactive_text, self::$data->min_automatewoo_version );
		}
		elseif ( ! self::is_automatewoo_directory_name_ok() ) {
			self::$errors[] = sprintf(__( '%s AutomateWoo plugin directory name is not correct.', 'automatewoo-sendinblue' ), $inactive_text );
		}

		if ( ! self::is_woocommerce_version_ok() ) {
			self::$errors[] = sprintf(__( '%s The plugin requires WooCommerce version %s or newer.', 'automatewoo-sendinblue' ), $inactive_text, self::$data->min_woocommerce_version );
		}
	}


	static function load_textdomain() {
		load_plugin_textdomain( 'automatewoo-sendinblue', false, "automatewoo-sendinblue/languages" );
	}


	/**
	 * @return bool
	 */
	static function is_automatewoo_active() {
		return function_exists( 'AW' );
	}


	/**
	 * @return bool
	 */
	static function is_automatewoo_version_ok() {
		if ( ! function_exists( 'AW' ) ) return false;
		return version_compare( AW()->version, self::$data->min_automatewoo_version, '>=' );
	}


	/**
	 * @return bool
	 */
	static function is_woocommerce_version_ok() {
		if ( ! function_exists( 'WC' ) ) return false;
		if ( ! self::$data->min_woocommerce_version ) return true;
		return version_compare( WC()->version, self::$data->min_woocommerce_version, '>=' );
	}


	/**
	 * @return bool
	 */
	static function is_automatewoo_directory_name_ok() {
		$active_plugins = (array) get_option( 'active_plugins', [] );
		return in_array( 'automatewoo/automatewoo.php', $active_plugins ) || array_key_exists( 'automatewoo/automatewoo.php', $active_plugins );
	}


	static function admin_notices() {
		if ( empty( self::$errors ) ) return;
		echo '<div class="notice notice-error"><p>';
		echo implode( '<br>', self::$errors );
		echo '</p></div>';
	}


}

AW_SendInBlue_Loader::init( new AW_SendInBlue_Plugin_Data() );

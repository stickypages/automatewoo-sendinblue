<?php

namespace AutomateWoo\SendInBlue;

use AutomateWoo\Admin_Settings_Tab_Abstract;

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * @class AW_SendInBlue_Settings_Tab
 */
class Settings_Tab extends Admin_Settings_Tab_Abstract {

	/** @var bool */
	public $show_tab_title = false;

	/** @var string  */
	public $prefix = 'aw_SendInBlue_';


	public function __construct() {
		$this->id = 'sendinblue';
		$this->name = __( 'SendInBlue', 'automatewoo-sendinblue' );
	}


	public function load_settings() {

		if ( ! empty( $this->settings ) )
			return;

		$this->section_start( 'api', __( 'SendInBlue API Details', 'automatewoo-sendinblue' ) );

		$this->add_setting( 'api_key', [
			'title' => __( 'API Key', 'automatewoo-sendinblue' ),
			'type' => 'password',
			'tooltip' => __( 'Locate your SendInBlue API Key from Admin Settings -> SMTP & API -> V3 Key.', 'automatewoo-sendinblue' )
		]);

		$this->section_end( 'api' );
	}


	/**
	 * @return array
	 */
	public function get_settings() {
		$this->load_settings();
		return $this->settings;
	}


	/**
	 * @param $id
	 * @return mixed
	 */
	protected function get_default( $id ) {
		return isset( AW_SendInBlue()->options()->defaults[ $id ] ) ? AW_SendInBlue()->options()->defaults[ $id ] : false;
	}

}

return new Settings_Tab();

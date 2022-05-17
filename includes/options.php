<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class AW_StickyBlue_Options
 *
 * @property string $api_domain
 * @property string $api_email
 * @property string $api_key
 */
class AW_StickyBlue_Options extends AutomateWoo\Options_API {

	/** @var string */
	public $prefix = 'aw_StickyBlue_';


	public function __construct() {
		$this->defaults = [

		];
	}
}


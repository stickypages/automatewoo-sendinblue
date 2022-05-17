<?php

namespace StickyBlueAutomateWoo;

use AutomateWoo\Fields;
use AutomateWoo\Clean;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Action_StickyBlue_Add_Contact
 */
class Action_StickyBlue_Send_SMS extends Action_StickyBlue_Abstract {


	public function load_admin_details() {
		$this->title = __( 'Send SMS', 'automatewoo-sendinblue' );
		$this->description = __( 'This trigger can be used to send transactional SMS\'s with SendInBlue.', 'automatewoo-sendinblue' );
	}


	public function load_fields() {

        $sender = ( new Fields\Text() )
            ->set_name( 'sender' )
            ->set_title( __( 'Sender', 'automatewoo-sendinblue' ) )
            ->set_description( __('Limited to 11 characters', 'automatewoo-sendinblue') )
            ->set_variable_validation();

		$recipient = ( new Fields\Text() )
			->set_name( 'recipient' )
			->set_title( __( 'Recipient', 'automatewoo-sendinblue' ) )
            ->set_description( __('This must be international format, will automatically add +1 to the front.', 'automatewoo-sendinblue' ) )
			->set_variable_validation();

		$content = ( new Fields\Text() )
			->set_name( 'content' )
			->set_title( __( 'Content', 'automatewoo-sendinblue' ) )
            ->set_description( __('If more than 160 chars long, multiple messages will be sent.', 'automatewoo-sendinblue' ) )
			->set_variable_validation();

        $tag = ( new Fields\Text() )
            ->set_name( 'tag' )
            ->set_title( __( 'Tag', 'automatewoo-sendinblue' ) )
            ->set_variable_validation();

		$this->add_field( $sender );
		$this->add_field( $recipient );
		$this->add_field( $content );
        $this->add_field( $tag );
	}


	function run() {
        $sender = Clean::string( $this->get_option( 'sender', true ) );
        $sender = substr(preg_replace("/[^A-Za-z0-9]/", "", $sender), 0, 11);
        $recipient = Clean::string( $this->get_option( 'recipient', true ) );
        $content = Clean::string( $this->get_option( 'content', true ) );
        $tag = Clean::string( $this->get_option( 'tag', true ) );

		if ( empty( $sender ) || empty( $recipient ) || empty( $content ) || ! AW_SendInBlue()->api() ) {
			return;
		}
        $sms = [];
        $method = 'POST';
        $endpoint = '/transactionalSMS/sms';

		$sms['sender'] = $sender;
        $sms['recipient'] = AW_SendInBlue()->api()->parse_phone($recipient);
        $sms['content'] = $content;
        $sms['type'] = 'transactional';
        $sms['unicodeEnabled'] = false;
        if ( $tag ) $sms['tag'] = $tag;

		$response = AW_SendInBlue()->api()->request( $method, $endpoint, $sms );
        error_log(print_r($response, true));
	}
}

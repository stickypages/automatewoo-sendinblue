<?php

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Action_SendInBlue_Update_Contact_Field
 * @since 1.0
 */
class Action_SendInBlue_Update_Contact_Field extends Action_SendInBlue_Abstract {


	function load_admin_details() {
		$this->title = __( 'Update Contact Custom Field', 'automatewoo-sendinblue' );
	}


	function load_fields() {
		$field_name = ( new Fields\Text() )
			->set_name('field_name')
			->set_title( __( 'Custom Field Name', 'automatewoo-sendinblue' ) )
			->set_variable_validation()
			->set_required();

		$field_value = ( new Fields\Text() )
			->set_name('field_value')
			->set_title( __( 'Custom Field Value', 'automatewoo-sendinblue' ) )
			->set_variable_validation();

		$this->add_contact_email_field();
		$this->add_field( $field_name );
		$this->add_field( $field_value );
	}


	function run() {
		$email = Clean::email( $this->get_option( 'email', true ) );
		$field_name = Clean::string( $this->get_option( 'field_name' ) );
		$field_value = Clean::string( $this->get_option( 'field_value', true ) );

		if ( empty( $field_name ) || empty( $email ) || ! AW_SendInBlue()->api() ) {
			return;
		}

		$contact_id = AW_SendInBlue()->api()->get_contact_id_by_email( $email );

		if ( ! $contact_id ) {
			return;
		}

		AW_SendInBlue()->api()->request( 'PUT', '/contacts/edit-properties', [
			'id' => $contact_id,
			'properties' => [
				[
					'type' => 'CUSTOM',
					'name' => $field_name,
					'value' => $field_value
				]
			],
		]);
	}

}

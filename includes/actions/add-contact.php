<?php

namespace StickyBlueAutomateWoo;

use AutomateWoo\Fields;
use AutomateWoo\Clean;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Action_StickyBlue_Add_Contact
 */
class Action_StickyBlue_Add_Contact extends Action_StickyBlue_Abstract {


	public function load_admin_details() {
		$this->title = __( 'Create / Update Contact', 'automatewoo-sendinblue' );
		$this->description = __( 'This trigger can be used to create or update contacts in SendInBlue. If an existing contact is found by email then an update will occur otherwise a new contact will be created. When updating a contact any fields left blank will not be updated e.g. if you only want to update the address just select an address and enter an email, all other fields can be left blank.', 'automatewoo-sendinblue' );
	}


	public function load_fields() {

		$first_name = ( new Fields\Text() )
			->set_name( 'first_name' )
			->set_title( __( 'First Name', 'automatewoo-sendinblue' ) )
			->set_variable_validation();

		$last_name = ( new Fields\Text() )
			->set_name( 'last_name' )
			->set_title( __( 'Last Name', 'automatewoo-sendinblue' ) )
			->set_variable_validation();

		$phone = ( new Fields\Text() )
			->set_name( 'phone' )
			->set_title( __( 'Phone', 'automatewoo-sendinblue' ) )
            ->set_description( __('This must be international format, will automatically add +1 to the front.', 'automatewoo-sendinblue' ) )
			->set_variable_validation();

		$list_id = ( new Fields\Text() )
			->set_name( 'list_id' )
			->set_title( __( 'List Id', 'automatewoo-sendinblue' ) )
            ->set_description( __('my custom des', 'automatewoo-sendinblue' ) )
			->set_variable_validation();

        $unlinkListIds = ( new Fields\Text() )
            ->set_name( 'unlinkListIds' )
            ->set_title( __( 'Unlink List Id', 'automatewoo-sendinblue' ) )
            ->set_variable_validation();

		$this->add_contact_email_field();
		$this->add_field( $first_name );
		$this->add_field( $last_name );
		$this->add_field( $phone );
        $this->add_field( $list_id );
        $this->add_field( $unlinkListIds );
	}


	function run() {
		$email = Clean::email( $this->get_option( 'email', true ) );
		$first_name = Clean::string( $this->get_option( 'first_name', true ) );
		$last_name = Clean::string( $this->get_option( 'last_name', true ) );
		$phone = Clean::string( $this->get_option( 'phone', true ) );
        $list_id = Clean::string( $this->get_option( 'list_id', true ) );
        $unlinkListIds = Clean::string( $this->get_option( 'unlinkListIds', true ) );

		if ( empty( $email ) || ! AW_SendInBlue()->api() ) {
			return;
		}

		$contact = [];
		$contact_id = AW_SendInBlue()->api()->get_contact_id_by_email( $email );

		if ( $contact_id ) {
			// update a contact
			// $contact['id'] = $contact_id;
			$method = 'PUT';
			$endpoint = '/contacts/'.$contact_id;
		}
		else {
			$method = 'POST';
			$endpoint = '/contacts';
			AW_SendInBlue()->api()->clear_contact_id_cache( $email ); // clear cache because this contact is about to exist
		}

		$contact['email'] = AW_SendInBlue()->api()->parse_email( $email );
        $contact['listIds'] = [(int) $list_id];

		if ( $first_name ) $contact['attributes']['FIRSTNAME'] = $first_name;
        if ( $last_name ) $contact['attributes']['LASTNAME'] = $last_name;
        if ( $phone ) $contact['attributes']['SMS'] = AW_SendInBlue()->api()->parse_phone( $phone );

        if ( $unlinkListIds ) $contact['unlinkListIds'] = [(int) $unlinkListIds];

		$response = AW_SendInBlue()->api()->request( $method, $endpoint, $contact );
	}

}

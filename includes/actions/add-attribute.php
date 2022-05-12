<?php

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Action_SendInBlue_Add_Contact
 */
class Action_SendInBlue_Add_Attribute extends Action_SendInBlue_Abstract {


	public function load_admin_details() {
		$this->title = __( 'Add Contact Attribute', 'automatewoo-sendinblue' );
		$this->description = __( 'Add an attribute onto a contact.', 'automatewoo-sendinblue' );
	}


	public function load_fields() {

        $attr_label = ( new Fields\Text() )
            ->set_name( 'attr_label' )
            ->set_title( __( 'Label', 'automatewoo-sendinblue' ) )
            ->set_variable_validation();

        $attr_value = ( new Fields\Text() )
            ->set_name( 'attr_value' )
            ->set_title( __( 'Value', 'automatewoo-sendinblue' ) )
            ->set_variable_validation();

        $this->add_contact_email_field();
		$this->add_field( $attr_label );
		$this->add_field( $attr_value );
	}


	function run() {
        $email = Clean::email( $this->get_option( 'email', true ) );

        $attr_label = strtoupper(Clean::string( $this->get_option( 'attr_label', true ) ));
        $attr_value = Clean::string( $this->get_option( 'attr_value', true ) );

		if ( empty( $email ) || empty( $attr_label ) || empty( $attr_value ) || ! AW_SendInBlue()->api() ) {
			return;
		}

        // create attribute
        $attribute = [];
        $attribute['type'] = "text";
        $response = AW_SendInBlue()->api()->request( 'POST', '/contacts/attributes/normal/'.$attr_label, $attribute );


        // update a contact
        $contact = [];
        $contact_id = AW_SendInBlue()->api()->get_contact_id_by_email( $email );
        if ( $contact_id ) {
            $method = 'PUT';
            $endpoint = '/contacts/'.$contact_id;

            if ( $attr_label ) $contact['attributes'][$attr_label] = $attr_value;

            // Update contact with attribute
            $response = AW_SendInBlue()->api()->request( $method, $endpoint, $contact );
        }
	}

}

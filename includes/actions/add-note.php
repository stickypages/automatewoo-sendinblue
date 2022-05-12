<?php

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Action_SendInBlue_Add_Note
 */
class Action_SendInBlue_Add_Note extends Action_SendInBlue_Abstract {


	public function load_admin_details() {
		$this->title = __('Add Note To Contact', 'automatewoo-sendinblue');
	}


	public function load_fields() {

        $note = ( new Fields\Text_Area() )
			->set_name('note')
			->set_title( __( 'Note', 'automatewoo-sendinblue' ) )
			->set_rows( 3 )
			->set_variable_validation();

		$this->add_contact_email_field();
		$this->add_field( $note );
	}


	function run() {
		$email = Clean::email( $this->get_option( 'email', true ) );
		$note = Clean::textarea( $this->get_option( 'note', true ) );

		if ( empty( $email ) || ! AW_SendInBlue()->api() ) {
			return;
		}

		$contact_id = AW_SendInBlue()->api()->get_contact_id_by_email( $email );

		if ( $contact_id ) {
			// add tags
			$response = AW_SendInBlue()->api()->request( 'POST', '/crm/notes', [
				'text' => $note,
				'contactIds' => [ $contact_id ],
			]);
		}
	}

}

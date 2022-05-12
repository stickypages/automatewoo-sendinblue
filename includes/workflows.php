<?php
/**
 * Set up and custom triggers or actions
 *
 * @class AW_SendInBlue_Workflows
 */
class AW_SendInBlue_Workflows {


	function __construct() {
		add_filter( 'automatewoo/actions', [ $this, 'actions' ] );
	}


	/**
	 * @param array $actions
	 * @return array
	 */
	function actions( $actions ) {

		if ( ! AW_SendInBlue()->api() ) {
			return $actions; // api details not set
		}

		include_once AW_SendInBlue()->path( '/includes/actions/abstract.php' );
		include_once AW_SendInBlue()->path( '/includes/actions/add-contact.php' );
        include_once AW_SendInBlue()->path( '/includes/actions/send-sms.php' );
        include_once AW_SendInBlue()->path( '/includes/actions/add-attribute.php' );
		include_once AW_SendInBlue()->path( '/includes/actions/add-note.php' );
		// include_once AW_SendInBlue()->path( '/includes/actions/add-tags.php' );
        // include_once AW_SendInBlue()->path( '/includes/actions/remove-tags.php' );
        // include_once AW_SendInBlue()->path( '/includes/actions/add-task.php' );
        // include_once AW_SendInBlue()->path( '/includes/actions/create-deal.php' );
		// include_once AW_SendInBlue()->path( '/includes/actions/update-contact-field.php' );

		$actions['sendinblue_add_contact'] = 'AutomateWoo\Action_SendInBlue_Add_Contact';
		$actions['sendinblue_add_note'] = 'AutomateWoo\Action_SendInBlue_Add_Note';
        $actions['sendinblue_add_attribute'] = 'AutomateWoo\Action_SendInBlue_Add_Attribute';
        $actions['sendinblue_send_sms'] = 'AutomateWoo\Action_SendInBlue_Send_SMS';
		// $actions['sendinblue_update_contact_field'] = 'AutomateWoo\Action_SendInBlue_Update_Contact_Field';
		// $actions['sendinblue_add_tags'] = 'AutomateWoo\Action_SendInBlue_Add_Tags';
		// $actions['sendinblue_remove_tags'] = 'AutomateWoo\Action_SendInBlue_Remove_Tags';
		// $actions['sendinblue_add_task'] = 'AutomateWoo\Action_SendInBlue_Add_Task';
		// $actions['sendinblue_create_deal'] = 'AutomateWoo\Action_SendInBlue_Create_Deal';

		return $actions;
	}


}

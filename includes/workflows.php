<?php
/**
 * Set up and custom triggers or actions
 *
 * @class AW_StickyBlue_Workflows
 */
class AW_StickyBlue_Workflows {

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

		$actions['sendinblue_add_contact'] = 'StickyBlueAutomateWoo\Action_StickyBlue_Add_Contact';
		$actions['sendinblue_add_note'] = 'StickyBlueAutomateWoo\Action_StickyBlue_Add_Note';
        $actions['sendinblue_add_attribute'] = 'StickyBlueAutomateWoo\Action_StickyBlue_Add_Attribute';
        $actions['sendinblue_send_sms'] = 'StickyBlueAutomateWoo\Action_StickyBlue_Send_SMS';

		return $actions;
	}


}

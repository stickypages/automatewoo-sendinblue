<?php

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Action_SendInBlue_Add_Task
 */
class Action_SendInBlue_Add_Task extends Action_SendInBlue_Abstract {


	public function load_admin_details() {
		$this->title = __( 'Add Task To Contact', 'automatewoo-sendinblue' );
		$this->description = __( 'Please note you must first create the contact in SendInBlue before assigning any tasks to them.', 'automatewoo-sendinblue' );
	}


	public function load_fields() {

		$name = ( new Fields\Text() )
			->set_name('subject')
			->set_title( __( 'Task Name', 'automatewoo-sendinblue' ) )
			->set_required()
			->set_variable_validation();

		$owner = ( new Fields\Select( false ) )
			->set_name( 'owner' )
			->set_title( __( 'Task Owner', 'automatewoo-sendinblue' ) )
			->set_options( AW_SendInBlue()->api()->get_users() )
			->set_required();

		$type = ( new Fields\Select( false ) )
			->set_name('type')
			->set_title( __( 'Task Type', 'automatewoo-sendinblue' ) )
			->set_options([
				'CALL' => 'Call',
				'EMAIL' => 'Email',
				'FOLLOW_UP' => 'Follow Up',
				'MEETING' => 'Meeting',
				'MILESTONE' => 'Milestone',
				'SEND' => 'Send',
				'TWEET' => 'Tweet',
				'OTHER' => 'Other'
			])
			->set_required();

		$priority = ( new Fields\Select( false ) )
			->set_name('priority')
			->set_title( __( 'Priority', 'automatewoo-sendinblue' ) )
			->set_default( 'NORMAL' )
			->set_options([
				'HIGH' => 'High',
				'NORMAL' => 'Normal',
				'LOW' => 'Low'
			])
			->set_required();

		$due = ( new Fields\Text() )
			->set_name('due')
			->set_title( __( 'Due', 'automatewoo-sendinblue' ) )
			->set_placeholder('e.g. {{ shop.current_datetime | modify : +1 day }}')
			->set_variable_validation()
			->set_required();

		$description = ( new Fields\Text_Area() )
			->set_name('description')
			->set_title( __( 'Description', 'automatewoo-sendinblue' ) )
			->set_rows( 3 )
			->set_variable_validation();

		$this->add_contact_email_field();
		$this->add_field( $name );
		$this->add_field( $owner );
		$this->add_field( $type );
		$this->add_field( $priority );
		$this->add_field( $due );
		$this->add_field( $description );
	}


	public function run() {
		$email = Clean::email( $this->get_option( 'email', true ) );
		$subject = Clean::string( $this->get_option( 'subject', true ) );
		$owner = Clean::string( $this->get_option( 'owner' ) );
		$type = Clean::string( $this->get_option( 'type' ) );
		$priority = Clean::string( $this->get_option( 'priority' ) );
		$due = Clean::string( $this->get_option( 'due', true ) );
		$description = Clean::textarea( $this->get_option( 'description', true ) );

		if ( empty( $subject ) || empty( $email ) || ! AW_SendInBlue()->api() ) {
			return;
		}

		$contact_id = AW_SendInBlue()->api()->get_contact_id_by_email( $email );

		if ( ! $contact_id ) {
			return;
		}

		// convert to gmt timestamp
		if ( ! $due = strtotime( get_gmt_from_date( $due ) ) ) {
			$due = time();
		}

		$data = [
			'contacts' => [ $contact_id ],
			'subject' => $subject,
			'type' => $type,
			'priority' => $priority,
			'due' => $due,
			'taskDescription' => $description,
		];

		if ( $owner ) {
			$data['owner_id'] = $owner;
		}

		$response = AW_SendInBlue()->api()->request( 'POST', '/tasks', $data );
	}

}

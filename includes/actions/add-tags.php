<?php

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Action_SendInBlue_Add_Tags
 */
class Action_SendInBlue_Add_Tags extends Action_SendInBlue_Abstract {


	public function load_admin_details() {
		$this->title = __( 'Add Tags To Contact', 'automatewoo-sendinblue' );
	}


	public function load_fields() {

//		$create_user = ( new AW_Field_Checkbox() )
//			->set_title(__( "Create Contact If Missing", 'automatewoo-sendinblue' ) )
//			->set_name( 'create_missing_contact' );

		$this->add_contact_email_field();
		$this->add_tags_field();
//		$this->add_field($create_user);
	}


	/**
	 * @return void
	 */
	public function run() {

		$email = Clean::email( $this->get_option( 'email', true ) );
		$tags = $this->parse_tags_string( $this->get_option( 'tags', true ) );
//		$create_missing_contact = $this->get_option('create_missing_contact');

		if ( empty( $tags ) || empty( $email ) || ! AW_SendInBlue()->api() )
			return;

		$contact_id = AW_SendInBlue()->api()->get_contact_id_by_email( $email );

		if ( $contact_id ) {
			// add tags
			$response = AW_SendInBlue()->api()->request( 'PUT', '/contacts/edit/tags', [
				'id' => $contact_id,
				"tags" => $tags
			]);
		}
		else {

//			if ( $create_missing_contact )
//			{
//				// create contact from email
//				$contact = [
//					"tags" => $tags,
//					'properties' => [
//						[
//							"type" => "SYSTEM",
//							"name" => "email",
//							"subtype" => "work",
//							"value" => $email
//						]
//					]
//				];
//
//
//				// fill in any user data
//				$user = $this->workflow->get_data_item('user');
//
//
//				// first name is apparently required
//				$contact['properties'][] = [
//					"type" => "SYSTEM",
//					"name" => "first_name",
//					"value" => $user && $user->first_name ? $user->first_name : 'Guest'
//				];
//
//				if ( $user )
//				{
//					if ( $user->last_name )
//					{
//						$contact['properties'][] = [
//							"type" => "SYSTEM",
//							"name" => "last_name",
//							"value" => $user->last_name
//						];
//					}
//				}
//
//
//				$response = AW_SendInBlue()->api()->request( 'POST', '/contacts', $contact );
//
//				if ( $response->get_response_code() == 200 )
//				{
//					$response_body = $response->get_body();
//
//					if ( $response_body['id'] )
//					{
//						AW_SendInBlue()->api()->set_contact_id_cache( $email, $response_body['id'] );
//					}
//				}
//			}
		}

	}

}

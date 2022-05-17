<?php


/**
 * Class StickyBlue_Action_After_Submit
 * @see https://developers.elementor.com/custom-form-action/
 * Custom elementor form action after submit to add a subscriber to
 * SendInBlue via API
 */
class StickyBlue_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {

    private $api_key;

    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name() {
        return 'sendinblue';
    }

    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label() {
        return __( 'SendInBlue', 'elementor-sendinblue' );
    }

    /**
     * Run
     *
     * Runs the action after submit
     *
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run( $record, $ajax_handler ) {
        $settings = $record->get( 'form_settings' );

        //  Make sure that there is a sendinblue list ID
        if ( empty( $settings['sendinblue_list'] ) ) {
            return;
        }

        // Make sure that there is Send In Blue Email field ID
        if ( empty( $settings['sendinblue_email_field'] ) ) {
            return;
        }

        // Get submitted Form data
        $raw_fields = $record->get( 'fields' );

        // Normalize the Form Data
        $fields = [];
        foreach ( $raw_fields as $id => $field ) {
            $fields[ $id ] = $field['value'];
        }

        // If we got this far we can start building our request data
        $sendinblue_data = [
            'email' => $fields[$settings['sendinblue_email_field']],
            'listIds' => [(int) $settings['sendinblue_list']]
        ];
        // add name if field is mapped
        if ( !empty( $fields[$settings['sendinblue_name_field']] ) ) {
            $sendinblue_data['attributes']['FIRSTNAME'] = $fields[$settings['sendinblue_name_field']];
        }

        // add phone number
        if ( !empty( $fields[$settings['sendinblue_phone_field']] ) ) {
            $sendinblue_data['attributes']['SMS'] = AW_SendInBlue()->api()->parse_phone( $fields[$settings['sendinblue_phone_field']] );
        }
        // Send the request
        error_log(print_r($sendinblue_data, true));

        if (empty($sendinblue_data['email'])) {
            error_log('AW/SENDINBLUE > No Email');
            return;
        }

        // create attribute
        if (!empty($settings['sendinblue_attribute_label_field'])) {
            $attribute = [];
            $attribute['type'] = "text";
            $endpoint = '/contacts/attributes/normal/' . $settings['sendinblue_attribute_label_field'];

            $this->request("POST", $endpoint, $sendinblue_data, $attribute);

            if ( !empty( $settings['sendinblue_attribute_value_field'] ) ) {
                $sendinblue_data['attributes'][$settings['sendinblue_attribute_label_field']] = $settings['sendinblue_attribute_value_field'];
            }
        }

        $method = 'POST';
        $endpoint = '/contacts';
        $contact_id = AW_SendInBlue()->api()->get_contact_id_by_email( $sendinblue_data['email'] );
        if ( $contact_id ) {
            $method = 'PUT';
            $endpoint = '/contacts/'.$contact_id;
        }
        $this->request($method, $endpoint, $sendinblue_data);
    }

    public function request($method, $endpoint, $data) {
        $api_key = get_option('aw_StickyBlue_api_key');
        error_log('CUSTOM API KEY', $api_key);
        $res = wp_remote_post( 'https://api.sendinblue.com/v3'.$endpoint, [
            'body' => json_encode($data),
            'method' => $method,
            'headers' => [
                'api-key' => $api_key,
                'Content-Type' => 'application/json'
            ]
        ] );
        error_log(print_r($res, true));
    }

    public function get_contact_lists() {
        error_log('GET CONTACT LISTS');
        error_log('API_KEY', AW_SendInBlue()->api()->api_key);
        $this->api_key = AW_SendInBlue()->api()->api_key;
        $listsResponse = AW_SendInBlue()->api()->request( 'GET', '/contacts/lists');
        $sendinblue_lists = array();
        if ( $listsResponse->get_response_code() == 200 ) {
            $response_body = $listsResponse->get_body();
            if ($response_body['lists']) {
                foreach($response_body['lists'] as $list)
                    if ($list['name'] != 'identified_contacts') {
                        $sendinblue_lists += [
                            $list['id'] => $list['name']
                        ];
                    }
            }
        }
        return $sendinblue_lists;
    }

    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section( $widget ) {

        $widget->start_controls_section(
            'section_sendinblue',
            [
                'label' => __( 'SendInBlue', 'elementor-sendinblue' ),
                'condition' => [
                    'submit_actions' => $this->get_name(),
                ],
            ]
        );

        $widget->add_control(
            'sendinblue_list',
            [
                'label' => __( 'SendInBlue List', 'elementor-sendinblue' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_contact_lists(),
                'separator' => 'before',
                'description' => __( 'The list you want to subscribe a user to.', 'elementor-sendinblue' ),
            ]
        );

        $widget->add_control(
            'sendinblue_email_field',
            [
                'label' => __( 'Email Field ID', 'elementor-sendinblue' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $widget->add_control(
            'sendinblue_name_field',
            [
                'label' => __( 'Name Field ID', 'elementor-sendinblue' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $widget->add_control(
            'sendinblue_phone_field',
            [
                'label' => __( 'Phone Field ID', 'elementor-sendinblue' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $widget->add_control(
            'sendinblue_attribute_label_field',
            [
                'label' => __( 'Contact Attribute Label', 'elementor-sendinblue' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );
        $widget->add_control(
            'sendinblue_attribute_value_field',
            [
                'label' => __( 'Contact Attribute Value', 'elementor-sendinblue' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $widget->end_controls_section();

    }

    /**
     * On Export
     *
     * Clears form settings on export
     * @access Public
     * @param array $element
     */
    public function on_export( $element ) {
        unset(
            $element['sendinblue_list'],
            $element['sendinblue_name_field'],
            $element['sendinblue_email_field'],
            $element['sendinblue_phone_field'],
            $element['sendinblue_attribute_label_field'],
            $element['sendinblue_attribute_value_field']
        );
    }
}


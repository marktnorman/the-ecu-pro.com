<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Zoho_Flow_Formidable_Forms extends Zoho_Flow_Service
{
    public function get_forms( $request ) {
        $forms = FrmForm::getAll();
        $data = array();
        foreach ($forms as $form) {
            array_push($data, array(
                'id' => $form->id,
                'title' => $form->name
            ));
        }
        return rest_ensure_response( $data );
    }
 
    public function get_form_schema() {
        $schema = array(
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            'title'                => 'form',
            'type'                 => 'form',
            'properties'           => array(
                'id' => array(
                    'description'  => esc_html__( 'ID of the Formidable form.', 'zoho-flow' ),
                    'type'         => 'integer',
                    'context'      => array( 'view', 'edit'),
                    'readonly'     => true,
                ),
                'title' => array(
                    'description'  => esc_html__( 'The title of the Formidable form.', 'zoho-flow' ),
                    'type'         => 'string',
                    'context'      => array( 'view', 'edit'),
                ),
            ),
        );
 
        return $schema;
    }

 
    public function get_fields( $request ) {
        $form_id = $request['form_id'];
        if(!ctype_digit($form_id)){
            return new WP_Error( 'rest_bad_request', esc_html__( 'The form ID is invalid.', 'zoho-flow' ), array( 'status' => 400 ) );
        }
        $form = FrmForm::getOne( $form_id );

        if(!$form){
            return new WP_Error( 'rest_not_found', esc_html__( 'The form is not found.', 'zoho-flow' ), array( 'status' => 404 ) );
        }
        
        $all_fields = FrmField::get_all_for_form($form_id);

        $fields = array();
        foreach( $all_fields as $field ){
            $data = array(
                'id' => $field->id,
                'name' => $field->name,
                'field-key'=> $field->field_key,
                'type'=> $field->type,
                'required' => $field->required,
            );
            array_push($fields, $data);
        }
        return rest_ensure_response( $fields );
    }
 
    public function get_field_schema() {
        $schema = array(
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            'title'                => 'field',
            'type'                 => 'field',
            'properties'           => array(
                'name' => array(
                    'description'  => esc_html__( 'Unique name of the field.', 'zoho-flow' ),
                    'type'         => 'string',
                    'context'      => array( 'view', 'edit'),
                    'readonly'     => true,
                ),
                'label' => array(
                    'description'  => esc_html__( 'Label of the field.', 'zoho-flow' ),
                    'type'         => 'string',
                    'context'      => array( 'view', 'edit')
                ),
                'type' => array(
                    'description'  => esc_html__( 'Type of the field.', 'zoho-flow' ),
                    'type'         => 'string',
                    'context'      => array( 'view', 'edit')
                ),
                'options' => array(
                    'description'  => esc_html__( 'Options of a dropdown/multiselect/checkbox/radio field.', 'zoho-flow' ),
                    'type'         => 'array',
                    'context'      => array( 'view', 'edit')
                ),
                'is_required' => array(
                    'description'  => esc_html__( 'Whether the field is mandatory.', 'zoho-flow' ),
                    'type'         => 'boolean',
                    'context'      => array( 'view', 'edit')
                ),
            ),
        );
 
        return $schema;
    }


 
    public function get_webhooks( $request ) {
        $form_id = $request['form_id'];
        if(!ctype_digit($form_id)){
            return new WP_Error( 'rest_bad_request', esc_html__( 'The form ID is invalid.', 'zoho-flow' ), array( 'status' => 400 ) );
        }
        $form = FrmForm::getOne( $form_id );

        if(!$form){
            return new WP_Error( 'rest_not_found', esc_html__( 'The form is not found.', 'zoho-flow' ), array( 'status' => 404 ) );
        }

        $args = array(
            'form_id' => $form->id
        );

        $webhooks = $this->get_webhook_posts($args);

 
        if ( empty( $webhooks ) ) {
            return rest_ensure_response( $webhooks );
        }


        $data = array();

        foreach ( $webhooks as $webhook ) {
            $webhook = array(
                'plugin_service' => $this->get_service_name(),
                'id' => $webhook->ID,
                'form_id' => $form->id,
                'url' => $webhook->url
            );
            array_push($data, $webhook);
        }
        return rest_ensure_response( $data );
    }

    public function create_webhook( $request ) {
        $form_id = $request['form_id'];
        $url = esc_url_raw($request['url']);
        if(!ctype_digit($form_id)){
            return new WP_Error( 'rest_bad_request', esc_html__( 'The form ID is invalid.', 'zoho-flow' ), array( 'status' => 400 ) );
        }
        $form = FrmForm::getOne( $form_id );

        if(!$form){
            return new WP_Error( 'rest_not_found', esc_html__( 'The form is not found.', 'zoho-flow' ), array( 'status' => 404 ) );
        }

        $form_title = $form->name;

        $post_id = $this->create_webhook_post($form_title, array(
            'form_id' => $form->id,
            'url' => $url
        ));
 
        return rest_ensure_response( array(
            'plugin_service' => $this->get_service_name(),
            'id' => $post_id,
            'form_id' => $form->id,
            'url' => $url
        ) );
    }

    public function delete_webhook( $request ) {
        $form_id = $request['form_id'];
        if(!ctype_digit($form_id)){
            return new WP_Error( 'rest_bad_request', esc_html__( 'The form ID is invalid.', 'zoho-flow' ), array( 'status' => 400 ) );
        }
        $form = FrmForm::getOne( $form_id );

        if(!$form){
            return new WP_Error( 'not_found', esc_html__( 'The form is not found.', 'zoho-flow' ), array( 'status' => 404 ) );
        }

        $webhook_id = $request['webhook_id'];
        if(!ctype_digit($webhook_id)){
            return new WP_Error( 'rest_bad_request', esc_html__( 'The webhook ID is invalid.', 'zoho-flow' ), array( 'status' => 400 ) );
        }
        $result = $this->delete_webhook_post($webhook_id);
        if(is_wp_error($result)){
            return $result;
        }
        return rest_ensure_response(array(
            'plugin_service' => $this->get_service_name(),
            'id' => $result->ID
        ));
        return rest_ensure_response($result);
    }
 
    public function get_form_webhook_schema() {
        $schema = array(
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            'title'                => 'webhook',
            'type'                 => 'webhook',
            'properties'           => array(
                'id' => array(
                    'description'  => esc_html__( 'Unique id of the webhook.', 'zoho-flow' ),
                    'type'         => 'integer',
                    'context'      => array( 'view', 'edit'),
                    'readonly'     => true,
                ),
                'form_id' => array(
                    'description'  => esc_html__( 'Unique id of the form.', 'zoho-flow' ),
                    'type'         => 'integer',
                    'context'      => array( 'view', 'edit'),
                    'readonly'     => true,
                ),                
                'url' => array(
                    'description'  => esc_html__( 'The webhook URL.', 'zoho-flow' ),
                    'type'         => 'string',
                    'context'      => array( 'view', 'edit')
                ),
            ),
        );
 
        return $schema;
    }  

    public function process_form_submission($entry_id, $form_id){
        $entries = FrmEntryMeta::get_entry_meta_info($entry_id);
        $args = array(
            'form_id' => $form_id
        );
        $webhooks = $this->get_webhook_posts($args);
        $data = array();
        if ( !empty( $webhooks ) ) {
            foreach ($entries as $entry){
                $field_id = $entry->field_id;
                $field_info = FrmField::getOne($field_id);
                $name = $field_info->field_key;
                $value = $entry->meta_value;
                $type= $field_info->type;
                switch ($type){
                    case 'checkbox' :
                        $matches = array();
                        preg_match_all('/".*?"/', $value, $matches);
                        foreach ($matches as $match){
                            $data[$name] = $match;
                        }
                        break;
                }
                if($type!=='checkbox'){
                    $data[$name] = $value;
                }
            }

            $files = array();
            foreach ( $webhooks as $webhook ) {
                $url = $webhook->url;
                zoho_flow_execute_webhook($url, $data, $files);
            }
        }

	}  	

    private function convert_field_name($name)
    {
        $name = preg_replace('/[^a-zA-Z0-9_]+/', ' ', $name);
        $name = trim($name);
        $name = str_replace(" ", "_", $name);
        $name = strtolower($name);

        return $name;
    }

}

<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

$zoho_flow_services_config = array (
  array (
    'name' => esc_html__('WordPress.org'),
    'gallery_app_link' => 'wordpress_org',
    'description' => esc_html__('WordPress.org is a free, open-source content management system that enables you to create and publish webpages. It provides a web template system with themes and also allows you to customize your websites using plugins.', 'zoho-flow'),
    'icon_file' => 'wordpress.png',
    'class_test' => 'WP_Comment',
    'version' => 'v1',
    'rest_apis' => array (
      array (
        'type' => 'list',
        'path' => '/users',
        'method' => 'get_users',
        'capability' => 'read',
        'schema_method' => 'get_user_schema',
      ),
      array (
        'type' => 'list',
        'path' => '/posts',
        'method' => 'get_posts',
        'capability' => 'read',
        'schema_method' => 'get_post_schema',
      ),
      array(
        'type' => 'list',
        'path' => '/comments',
        'method' => 'get_comments',
        'capability' => 'read',
        'schema_method' => 'get_comment_schema'
      ),
      array (
        'type' => 'list',
        'path' => '/posts/(?\'post_id\'[\\d]+)/comments/webhooks',
        'method' => 'get_webhooks',
        'capability' => 'read',
        'schema_method' => 'get_form_webhook_schema',
      ),
      array(
          'type' => 'create',
          'path' => '/posts/(?\'post_id\'[\\d]+)/comments/webhooks',
          'method' => 'create_post_comments_webhook',
          'capability' => 'edit_posts',
          'schema_method' => '',
      ),
      array(
          'type' => 'delete',
          'path' => '/posts/(?\'post_id\'[\\d]+)/comments/webhooks/(?\'webhook_id\'[\\d]+)',
          'method' => 'delete_webhook',
          'capability' => 'delete_posts',
          'schema_method' => '',
      ),
      array(
          'type' => 'list',
          'path' => '/(?\'post_type\'[a-zA-Z_]+)/webhooks',
          'method' => 'get_webhooks_for_post',
          'capability' => 'read',
          'schema_method' => '',
      ),
      array(
          'type' => 'create',
          'path' => '/(?\'post_type\'[a-zA-Z_]+)/webhooks',
          'method' => 'create_webhook_for_post',
          'capability' => 'edit_posts',
          'schema_method' => '',
      ),
      array(
          'type' => 'delete',
          'path' => '/(?\'post_type\'[a-zA-Z_]+)/webhooks/(?\'webhook_id\'[\\d]+)',
          'method' => 'delete_webhook',
          'capability' => 'delete_posts',
          'schema_method' => '',
      ),
      array(
          'type' => 'list',
          'path' => '/comments/webhooks',
          'method' => 'get_comments_webhooks',
          'capability' => 'read',
          'schema_method' => ''
      ),
      array(
          'type' => 'create',
          'path' => '/comments/webhooks',
          'method' => 'create_comments_webhooks',
          'capability' => 'edit_posts',
          'schema_method' => ''
      ),
      array(
          'type' => 'delete',
          'path' => '/comments/webhooks/(?\'webhook_id\'[\\d]+)',
          'method' => 'delete_webhook',
          'capability' => 'delete_posts',
          'schema_method' => '',
      ),
      array(
          'type' => 'create',
          'path' => '/users',
          'method' => 'create_user',
          'capability' => 'create_users',
          'schema_method' => '',
      ),
      array(
          'type' => 'update',
          'path' => '/users/(?\'user_id\'[\\d]+)',
          'method' => 'update_user',
          'capability' => 'edit_users',
          'schema_method' => '',
      ),
      array(
          'type' => 'create',
          'path' => '/posts',
          'method' => 'create_post',
          'capability' => 'edit_posts',
          'schema_method' => '',
      ),
      array(
          'type' => 'update',
          'path' => '/posts',
          'method' => 'update_post',
          'capability' => 'edit_posts',
          'schema_method' => '',
      ),
      array(
        'type' => 'list',
        'path' => '/getuser/(?P<user_id>\d+)',
        'method' => 'get_user_by',
        'capability' => 'read',
        'schema_method' => '',
      ),
      array(
        'type' => 'list',
        'path' => '/getuser/(?P<login>\S+)',
        'method' => 'get_user_by',
        'capability' => 'read',
        'schema_method' => '',
      ),
    ),
    'hooks' => array (
      array (
        'action' => 'comment_post',
        'method' => 'process_comment_post',
        'args_count' => 3,
      ),
      array (
        'action' => 'spammed_comment',
        'method' => 'process_spammed_comment',
        'args_count' => 2,
      ),
      array (
        'action' => 'edit_comment',
        'method' => 'process_edit_comment',
        'args_count' => 2,
      ),
      array (
        'action' => 'wp_set_comment_status',
        'method' => 'process_set_comment_status',
        'args_count' => 2,
      ),
      array (
        'action' => 'user_register',
        'method' => 'process_user_register',
        'args_count' => 1,
      ),
      array(
          'action' => 'profile_update',
          'method' => 'process_profile_update',
          'args_count' => 2
      ),
      array(
          'action' => 'save_post',
          'method' => 'process_save_post',
          'args_count' => 3
      ),
    ),
  ),
  array (
    'name' => esc_html__('Contact Form 7'),
    'gallery_app_link' => 'contact-form-7',
    'description' => esc_html__('Contact Form 7 is a popular WordPress plugin for creating lead generating forms. It supports Ajax-powered submitting, CAPTCHA, Akismet spam filtering, and more.', 'zoho-flow'),
    'icon_file' => 'contact-form-7.png',
    'class_test' => 'WPCF7_ContactForm',
    'version' => 'v1',
    'rest_apis' => array (
      array (
        'type' => 'list',
        'path' => '/forms',
        'method' => 'get_forms',
        'capability' => 'wpcf7_read_contact_forms',
        'schema_method' => 'get_form_schema',
      ),
      array (
        'type' => 'list',
        'path' => '/forms/(?\'form_id\'[\\d]+)/fields',
        'method' => 'get_fields',
        'capability' => 'wpcf7_read_contact_forms',
        'schema_method' => 'get_field_schema',
      ),
      array (
        'type' => 'list',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
        'method' => 'get_webhooks',
        'capability' => 'wpcf7_read_contact_forms',
        'schema_method' => 'get_form_webhook_schema',
      ),
      array (
        'type' => 'create',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
        'method' => 'create_webhook',
        'capability' => 'wpcf7_edit_contact_form',
        'schema_method' => '',
      ),
      array (
        'type' => 'delete',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks/(?\'webhook_id\'[\\d]+)',
        'method' => 'delete_webhook',
        'capability' => 'wpcf7_delete_contact_form',
        'schema_method' => '',
      ),
      array (
        'type' => 'get',
        'path' => '/files/(?\'filename\'.+)',
        'method' => 'get_file',
        'capability' => 'wpcf7_edit_contact_form',
        'schema_method' => '',
      ),
    ),
    'hooks' => array (
      array (
        'action' => 'wpcf7_before_send_mail',
        'method' => 'process_form_submission',
        'args_count' => 1,
      ),
    ),
  ),
  array (
    'name' => esc_html__('WPForms'),
    'gallery_app_link' => 'wpforms',
    'description' => esc_html__('WP Forms is a drag and drop WordPress form builder that’s easy and powerful. It provides you with numerous customizable form templates for subscription forms, payment forms, and more.', 'zoho-flow'),
    'icon_file' => 'wpforms.png',
    'class_test' => 'WPForms',
    'version' => 'v1',
    'rest_apis' => array (
      array (
        'type' => 'list',
        'path' => '/forms',
        'method' => 'get_forms',
        'capability' => 'manage_options',
        'schema_method' => 'get_form_schema',
      ),
      array (
        'type' => 'list',
        'path' => '/forms/(?\'form_id\'[\\d]+)/fields',
        'method' => 'get_fields',
        'capability' => 'manage_options',
        'schema_method' => 'get_field_schema',
      ),
      array (
        'type' => 'list',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
        'method' => 'get_webhooks',
        'capability' => 'manage_options',
        'schema_method' => 'get_form_webhook_schema',
      ),
      array (
        'type' => 'create',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
        'method' => 'create_webhook',
        'capability' => 'manage_options',
        'schema_method' => '',
      ),
      array (
        'type' => 'delete',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks/(?\'webhook_id\'[\\d]+)',
        'method' => 'delete_webhook',
        'capability' => 'manage_options',
        'schema_method' => '',
      ),
      array (
        'type' => 'get',
        'path' => '/files/(?\'filename\'.+)',
        'method' => 'get_file',
        'capability' => 'manage_options',
        'schema_method' => '',
      ),
    ),
    'hooks' => array (
      array (
        'action' => 'wpforms_process_complete',
        'method' => 'process_form_submission',
        'args_count' => 4,
      ),
    ),
  ),
  array (
    'name' => esc_html__('Ninja Forms'),
    'gallery_app_link' => 'ninja-forms',
    'description' => esc_html__('Ninja Forms is a beginner friendly form builder plugin that lets you build professional forms in minutes. It provides conditional logic, multi-step forms, file uploads, and more.', 'zoho-flow'),
    'icon_file' => 'ninja-forms.png',
    'class_test' => 'Ninja_Forms',
    'version' => 'v1',
    'rest_apis' => array (
      array (
        'type' => 'list',
        'path' => '/forms',
        'method' => 'get_forms',
        'capability' => 'manage_options',
        'schema_method' => 'get_form_schema',
      ),
      array (
        'type' => 'list',
        'path' => '/forms/(?\'form_id\'[\\d]+)/fields',
        'method' => 'get_fields',
        'capability' => 'manage_options',
        'schema_method' => 'get_field_schema',
      ),
      array (
        'type' => 'list',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
        'method' => 'get_webhooks',
        'capability' => 'manage_options',
        'schema_method' => 'get_form_webhook_schema',
      ),
      array (
        'type' => 'create',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
        'method' => 'create_webhook',
        'capability' => 'manage_options',
        'schema_method' => '',
      ),
      array (
        'type' => 'delete',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks/(?\'webhook_id\'[\\d]+)',
        'method' => 'delete_webhook',
        'capability' => 'manage_options',
        'schema_method' => '',
      ),
    ),
    'hooks' => array (
      array (
        'action' => 'ninja_forms_after_submission',
        'method' => 'process_form_submission',
        'args_count' => 4,
      ),
    ),
  ),
  array (
    'name' => esc_html__('Formidable Forms'),
    'gallery_app_link' => 'formidable-forms',
    'description' => esc_html__('Formidable Forms is an advanced WordPress form plugin with a drag and drop form builder. Build a single contact form or complex multi-page forms with conditional logic, calculations, file uploads, and more.', 'zoho-flow'),
    'icon_file' => 'formidable-forms.png',
    'class_test' => 'FrmSettings',
    'version' => 'v1',
    'rest_apis' => array (
      array (
        'type' => 'list',
        'path' => '/forms',
        'method' => 'get_forms',
        'capability' => 'frm_view_forms',
        'schema_method' => 'get_form_schema',
      ),
      array (
        'type' => 'list',
        'path' => '/forms/(?\'form_id\'[\\d]+)/fields',
        'method' => 'get_fields',
        'capability' => 'frm_view_forms',
        'schema_method' => 'get_field_schema',
      ),
      array (
        'type' => 'list',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
        'method' => 'get_webhooks',
        'capability' => 'frm_view_forms',
        'schema_method' => 'get_form_webhook_schema',
      ),
      array (
        'type' => 'create',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
        'method' => 'create_webhook',
        'capability' => 'frm_edit_forms',
        'schema_method' => '',
      ),
      array (
        'type' => 'delete',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks/(?\'webhook_id\'[\\d]+)',
        'method' => 'delete_webhook',
        'capability' => 'frm_delete_forms',
        'schema_method' => '',
      ),
    ),
    'hooks' => array (
      array (
        'action' => 'frm_after_create_entry',
        'method' => 'process_form_submission',
        'args_count' => 3,
      ),
    ),
  ),
  array (
    'name' => esc_html__('Everest Forms'),
    'gallery_app_link' => 'everest-forms',
    'description' => esc_html__('Everest Forms is a drag and drop form builder plugin that lets you create beautiful forms, including contact forms, within minutes. The plugin is lightweight, fast, and mobile responsive.', 'zoho-flow'),
    'icon_file' => 'everest-forms.png',
    'class_test' => 'EverestForms',
    'version' => 'v1',
    'rest_apis' => array (
      array (
        'type' => 'list',
        'path' => '/forms',
        'method' => 'get_forms',
        'capability' => 'manage_everest_forms',
        'schema_method' => 'get_form_schema',
      ),
      array (
        'type' => 'list',
        'path' => '/forms/(?\'form_id\'[\\d]+)/fields',
        'method' => 'get_fields',
        'capability' => 'manage_everest_forms',
        'schema_method' => 'get_field_schema',
      ),
      array (
        'type' => 'list',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
        'method' => 'get_webhooks',
        'capability' => 'manage_everest_forms',
        'schema_method' => 'get_form_webhook_schema',
      ),
      array (
        'type' => 'create',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
        'method' => 'create_webhook',
        'capability' => 'edit_everest_forms',
        'schema_method' => '',
      ),
      array (
        'type' => 'delete',
        'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks/(?\'webhook_id\'[\\d]+)',
        'method' => 'delete_webhook',
        'capability' => 'manage_everest_forms',
        'schema_method' => '',
      ),
    ),
    'hooks' => array (
      array (
        'action' => 'everest_forms_process_complete',
        'method' => 'process_form_submission',
        'args_count' => 4,
      ),
    ),
  ),
  array(
        'name' => esc_html__('Elementor'),
        'gallery_app_link' => 'elementor',
        'description' => esc_html__('Elementor is a visual website builder plugin for WordPress that provides an intuitive drag-and-drop editor, 100+ pre-designed templates and blocks, advanced design features, and more.', 'zoho-flow'),
        'icon_file' => 'elementor.png',
        'class_test' => 'Elementor\Api',
        'version' => 'v1',
        'rest_apis' => array (
            array (
                'type' => 'list',
                'path' => '/forms',
                'method' => 'get_forms',
                'capability' => 'manage_options',
                'schema_method' => 'get_form_schema',
            ),
            array (
                'type' => 'list',
                'path' => '/forms/(?\'form_id\'[a-zA-Z0-9_]+)/fields',
                'method' => 'get_fields',
                'capability' => 'manage_options',
                'schema_method' => 'get_field_schema',
            ),
            array (
                'type' => 'list',
                'path' => '/forms/(?\'form_id\'[a-zA-Z0-9_]+)/webhooks',
                'method' => 'get_webhooks',
                'capability' => 'manage_options',
                'schema_method' => 'get_form_webhook_schema',
            ),
            array (
                'type' => 'create',
                'path' => '/forms/(?\'form_id\'[a-zA-Z0-9_]+)/webhooks',
                'method' => 'create_webhook',
                'capability' => 'manage_options',
                'schema_method' => '',
            ),
            array (
                'type' => 'delete',
                'path' => '/forms/(?\'form_id\'[a-zA-Z0-9_]+)/webhooks/(?\'webhook_id\'[\\d]+)',
                'method' => 'delete_webhook',
                'capability' => 'manage_options',
                'schema_method' => '',
            ),
        ),
        'hooks' => array (
            array (
                'action' => 'elementor_pro/forms/new_record',
                'method' => 'process_form_submission',
                'args_count' => 2,
            ),
        ),
    ),
    array(
        'name' => esc_html__('Ultimate Member'),
        'gallery_app_link' => 'ultimate-member',
        'description' => esc_html__('Ultimate Member is a free user profile WordPress plugin that makes it easy to create powerful online communities and membership sites with WordPress.', 'zoho-flow'),
        'icon_file' => 'ultimate-member.png',
        'class_test' => 'UM',
        'version' => 'v1',
        'rest_apis' => array (
            array (
                'type' => 'list',
                'path' => '/forms',
                'method' => 'get_forms',
                'capability' => 'manage_options',
                'schema_method' => 'get_form_schema',
            ),
            array (
                'type' => 'list',
                'path' => '/forms/(?\'form_id\'[\\d]+)/fields',
                'method' => 'get_fields',
                'capability' => 'manage_options',
                'schema_method' => 'get_field_schema',
            ),
            array (
                'type' => 'list',
                'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
                'method' => 'get_webhooks',
                'capability' => 'manage_options',
                'schema_method' => 'get_form_webhook_schema',
            ),
            array (
                'type' => 'create',
                'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks',
                'method' => 'create_webhook',
                'capability' => 'manage_options',
                'schema_method' => '',
            ),
            array (
                'type' => 'delete',
                'path' => '/forms/(?\'form_id\'[\\d]+)/webhooks/(?\'webhook_id\'[\\d]+)',
                'method' => 'delete_webhook',
                'capability' => 'manage_options',
                'schema_method' => '',
            ),
        ),
        'hooks' => array (
            array (
                'action' => 'um_after_save_registration_details',
                'method' => 'process_form_submission',
                'args_count' => 2,
            ),
        ),
    ),
    array(
        'name' => esc_html__('DigiMember'),
        'gallery_app_link' => 'digi-member',
        'description' => esc_html__('DigiMember is an easy-to-use membership plugin for WordPress, that lets you build your own automated membership site. It provides features such as individual content placement, personal addressing, exams and certifications, and more.', 'zoho-flow'),
        'icon_file' => 'digi-member.png',
        'class_test' => 'ncore_Class',
        'version' => 'v1',
        'rest_apis' => array (
            array (
                'type' => 'list',
                'path' => '/products',
                'method' => 'get_all_products',
                'capability' => 'manage_options',
                'schema_method' => '',
            ),
            array (
                'type' => 'list',
                'path' => '/users/(?\'user_id\'[\\d]+)/products',
                'method' => 'get_products_of_user',
                'capability' => 'manage_options',
                'schema_method' => '',
            ),
            array (
                'type' => 'list',
                'path' => '/orders/(?\'user_id\'[\\d]+)',
                'method' => 'get_user_orders',
                'capability' => 'manage_options',
                'schema_method' => '',
            ),
            array (
                'type' => 'create',
                'path' => '/orders',
                'method' => 'create_orders',
                'capability' => 'manage_options',
                'schema_method' => '',
            ),
            array (
                'type' => 'list',
                'path' => '/(?\'post_type\'[a-zA-Z_]+)/webhooks',
                'method' => 'get_webhook_for_order',
                'capability' => 'manage_options',
                'schema_method' => '',
            ),
            array (
                'type' => 'create',
                'path' => '/(?\'post_type\'[a-zA-Z_]+)/webhooks',
                'method' => 'create_webhook_for_order',
                'capability' => 'manage_options',
                'schema_method' => '',
            ),
            array (
                'type' => 'delete',
                'path' => '/(?\'post_type\'[a-zA-Z_]+)/webhooks/(?\'webhook_id\'[\\d]+)',
                'method' => 'delete_webhook',
                'capability' => 'manage_options',
                'schema_method' => '',
            ),
        ),
        'hooks' => array (
            array (
                'action' => 'digimember_purchase',
                'method' => 'digi_purchase',
                'args_count' => 4,
            ),
        ),
    )
);
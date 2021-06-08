<?php

add_action('wp_ajax_user_auth_checkout', 'user_auth_checkout');
add_action('wp_ajax_nopriv_user_auth_checkout', 'user_auth_checkout');

/**
 * user_auth_checkout function
 */
function user_auth_checkout()
{
    $data = '';

    if (isset($_POST['form_data'])) {

        $user = get_user_by( 'email', $_POST['form_data'] );

        if( $user ) {
            wp_set_current_user( $user->ID, $user->user_login );
            wp_set_auth_cookie( $user->ID ,false, is_ssl());

            echo 'User logged in successfully!';
            die();
        } else {
            echo 'User does not exist';
            die();
        }
    }

}
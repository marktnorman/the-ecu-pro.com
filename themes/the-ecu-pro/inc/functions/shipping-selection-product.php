<?php

// Enable customer WC_Session
//add_action( 'init', 'wc_session_enabler' );
function wc_session_enabler() {
    if ( is_user_logged_in() || is_admin() )
        return;

    if ( isset(WC()->session) && ! WC()->session->has_session() ) {
        WC()->session->set_customer_session_cookie( true );
    }
}
<?php

/**
 * Function to check if string starting
 * with given substring
 *
 * @param $string string The string to search within
 * @param $startString string The string to search for
 *
 * @return bool
 */
function starts_with ($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

/**
 * Function to check the string if it ends
 * with given substring or not
 *
 * @param $string string The string to search within
 * @param $endString string The string to search for
 *
 * @return bool
 */
function ends_with($string, $endString)
{
    $len = strlen($endString);
    if ($len == 0) {
        return true;
    }
    return (substr($string, -$len) === $endString);
}

/**
 * Checks the $_POST request for existing valid nonce
 *
 * @return bool
 */
function verify_ajax_nonce()
{
    return wp_verify_nonce( $_POST['nonce'], 'w3ex-advbedit-nonce' );
}

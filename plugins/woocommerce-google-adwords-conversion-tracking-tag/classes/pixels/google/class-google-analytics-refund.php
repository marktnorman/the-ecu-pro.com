<?php

namespace WGACT\Classes\Pixels\Google;

use  WC_Order ;
use  WC_Order_Refund ;
use  WC_Product ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

class Google_Analytics_Refund extends Google_Analytics
{
    use  Trait_Google ;
    public function __construct( $options )
    {
        parent::__construct( $options );
    }

}
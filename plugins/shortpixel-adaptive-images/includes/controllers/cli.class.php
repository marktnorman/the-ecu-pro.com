<?php
/**
 * User: simon
 * Date: 17.11.2020
 */

/**
 * Just a few sample commands to learn how WP-CLI works
 */
class ShortPixelCLI extends WP_CLI_Command {
    /**
     * Clear the CSS cache.
     */
    function clear_css() {
        if(!!ShortPixelAI::clear_css_cache()) {
            WP_CLI::line( 'CSS cache cleared successfully.' );
        } else {
            WP_CLI::line( 'Could not clear CSS cache.' );
        }
    }


    /**
     * Clear the LQIPs
     */
    function clear_lqips() {
        \ShortPixel\AI\LQIP::clearCache();
        WP_CLI::line( 'LQIP images cleared.' );
    }
}

WP_CLI::add_command( 'shortpixel', 'ShortPixelCLI' );
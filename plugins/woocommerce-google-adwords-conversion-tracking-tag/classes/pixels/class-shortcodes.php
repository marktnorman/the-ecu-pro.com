<?php

namespace WGACT\Classes\Pixels;

use WGACT\Classes\Pixels\Google\Trait_Google;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Shortcodes extends Pixel
{
    use Trait_Google;
    use Trait_Product;

    public function __construct($options)
    {
        parent::__construct($options);

        add_shortcode('view-item', [$this, 'wooptpm_view_item']);
        add_shortcode('conversion-pixel', [$this, 'wooptpm_conversion_pixel']);
    }

    public function wooptpm_view_item($attributes)
    {
        $shortcode_attributes = shortcode_atts([
            'product-id' => null,
        ], $attributes);

        if ($shortcode_attributes['product-id']) {

            $product = wc_get_product($shortcode_attributes['product-id']);

            if (!is_object($product)) {
                wc_get_logger()->debug('get_product_data_layer_script received an invalid product', ['source' => 'wooptpm']);
                return;
            }

            echo $this->get_product_data_layer_script($product, false, false);

            ?>

            <script>
                jQuery(window).on('load', function () {
                    jQuery(document).trigger('wooptpmViewItem', wooptpm.getProductDetailsFormattedForEvent(<?php echo $shortcode_attributes['product-id'] ?>));
                });
            </script>
            <?php
        }
    }

    public function wooptpm_conversion_pixel($attributes)
    {
        $shortcode_attributes = shortcode_atts([
            'pixel'                 => 'all',
            'gads-conversion-id'    => $this->options_obj->google->ads->conversion_id,
            'gads-conversion-label' => '',
            'fbc-event'             => 'Lead',
            'twc-event'             => 'CompleteRegistration',
            'pinc-event'            => 'lead',
            'pinc-lead-type'        => '',
            'ms-ads-event'          => 'submit',
            'ms-ads-event-category' => '',
            'ms-ads-event-label'    => 'lead',
            'ms-ads-event-value'    => 0,
            'snap-event'            => 'SIGN_UP',
            'tiktok-event'          => 'SubmitForm',
        ], $attributes);

        if ($shortcode_attributes['pixel'] == 'google-ads') {
            if ($this->is_google_ads_active()) $this->conversion_html_google_ads($shortcode_attributes);
        } elseif ($shortcode_attributes['pixel'] == 'facebook') {
            if ($this->options_obj->facebook->pixel_id) $this->conversion_html_facebook($shortcode_attributes);
        } elseif ($shortcode_attributes['pixel'] == 'twitter') {
            if ($this->options_obj->twitter->pixel_id) $this->conversion_html_twitter($shortcode_attributes);
        } elseif ($shortcode_attributes['pixel'] == 'pinterest') {
            if ($this->options_obj->pinterest->pixel_id) $this->conversion_html_pinterest($shortcode_attributes);
        } elseif ($shortcode_attributes['pixel'] == 'ms-ads') {
            if ($this->options_obj->bing->uet_tag_id) $this->conversion_html_microsoft_ads($shortcode_attributes);
        } elseif ($shortcode_attributes['pixel'] == 'snapchat') {
            if ($this->options_obj->snapchat->pixel_id) $this->conversion_html_snapchat($shortcode_attributes);
        } elseif ($shortcode_attributes['pixel'] == 'tiktok') {
            if ($this->options_obj->tiktok->pixel_id) $this->conversion_html_tiktok($shortcode_attributes);
        } elseif ($shortcode_attributes['pixel'] == 'all') {
            if ($this->is_google_ads_active()) $this->conversion_html_google_ads($shortcode_attributes);
            if ($this->options_obj->facebook->pixel_id) $this->conversion_html_facebook($shortcode_attributes);
            if ($this->options_obj->twitter->pixel_id) $this->conversion_html_twitter($shortcode_attributes);
            if ($this->options_obj->pinterest->pixel_id) $this->conversion_html_pinterest($shortcode_attributes);
            if ($this->options_obj->bing->uet_tag_id) $this->conversion_html_microsoft_ads($shortcode_attributes);
            if ($this->options_obj->snapchat->pixel_id) $this->conversion_html_snapchat($shortcode_attributes);
            if ($this->options_obj->tiktok->pixel_id) $this->conversion_html_tiktok($shortcode_attributes);
        }
    }

    private function conversion_html_snapchat($shortcode_attributes)
    {
        ?>

        <script>
            snaptr('track', '<?php echo $shortcode_attributes['snap-event'] ?>');
        </script>
        <?php
    }

    private function conversion_html_tiktok($shortcode_attributes)
    {
        ?>

        <script>
            ttq.track('<?php echo $shortcode_attributes['tiktok-event'] ?>');
        </script>
        <?php
    }

    private function conversion_html_google_ads($shortcode_attributes)
    {
        ?>

        <script>
            gtag('event', 'conversion', {'send_to': 'AW-<?php echo $shortcode_attributes['gads-conversion-id'] ?>/<?php echo $shortcode_attributes['gads-conversion-label'] ?>'});
        </script>
        <?php
    }

    // https://developers.facebook.com/docs/analytics/send_data/events/
    private function conversion_html_facebook($shortcode_attributes)
    {
        if ($this->options_obj->facebook->capi->token) {
            ?>

            <script>
                jQuery(window).on('load', function () {

                    let eventId = wooptpm.getRandomEventId();

                    fbq('track', '<?php echo $shortcode_attributes['fbc-event'] ?>', {}, {
                        eventID: eventId,
                    });

                    jQuery(document).trigger('wooptpmFbCapiEvent', {
                        event_name      : "<?php echo $shortcode_attributes['fbc-event'] ?>",
                        event_id        : eventId,
                        user_data       : wooptpm.getFbUserData(),
                        event_source_url: window.location.href
                    });
                });

            </script>
            <?php
        } else {
            ?>

            <script>
                fbq('track', '<?php echo $shortcode_attributes['fbc-event'] ?>');
            </script>
            <?php
        }
    }

    // https://business.twitter.com/en/help/campaign-measurement-and-analytics/conversion-tracking-for-websites.html
    private function conversion_html_twitter($shortcode_attributes)
    {
        ?>

        <script>
            twq('track', '<?php echo $shortcode_attributes['twc-event'] ?>');
        </script>
        <?php
    }

    // https://help.pinterest.com/en/business/article/track-conversions-with-pinterest-tag
    // https://help.pinterest.com/en/business/article/add-event-codes
    private function conversion_html_pinterest($shortcode_attributes)
    {
        if ($shortcode_attributes['pinc-lead-type'] == '') {
            ?>

            <script>
                pintrk('track', '<?php echo $shortcode_attributes['pinc-event'] ?>');
            </script>
            <?php
        } else {
            ?>

            <script>
                pintrk('track', '<?php echo $shortcode_attributes['pinc-event'] ?>', {
                    lead_type: '<?php echo $shortcode_attributes['pinc-lead-type'] ?>'
                });
            </script>
            <?php
        }
    }

    // https://bingadsuet.azurewebsites.net/UETDirectOnSite_ReportCustomEvents.html
    private function conversion_html_microsoft_ads($shortcode_attributes)
    {
        ?>

        <script>
            window.uetq = window.uetq || [];
            window.uetq.push('event', '<?php echo $shortcode_attributes['ms-ads-event'] ?>', {
                'event_category': '<?php echo $shortcode_attributes['ms-ads-event-category'] ?>',
                'event_label'   : '<?php echo $shortcode_attributes['ms-ads-event-label'] ?>',
                'event_value'   : '<?php echo $shortcode_attributes['ms-ads-event-value'] ?>'
            });
        </script>
        <?php
    }
}
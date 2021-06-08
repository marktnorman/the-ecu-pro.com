<?php
/**
 * User: simon
 * Date: 10.06.2019
 */

use ShortPixel\AI\ActiveIntegrations;

class ShortPixelRegexParser {
    protected $ctrl;
    private $logger;
    private $cssParser;

    private $scripts;
    private $styles;
    private $CDATAs;
    private $noscripts;
	private $attributes;

    private $classFilter = false;
    private $attrFilter = false;
    private $attrValFilter = false;
    private $attrToIntegrateAndRemove = false;

    private $isEager = false;

    public function __construct(ShortPixelAI $ctrl)
    {
        $this->ctrl = $ctrl;
        $this->logger = ShortPixelAILogger::instance();
        $this->cssParser = new ShortPixelCssParser($ctrl);
    }

    public function parse($content) {
        $this->logger->log("******** REGEX PARSER BB ********* " . (SHORTPIXEL_AI_DEBUG & ShortPixelAILogger::DEBUG_INCLUDE_CONTENT ? "\n\nCONTENT:\n" . $content : ''));

        //add the preconnect header for faster loading of the images
        $apiUrl = parse_url($this->ctrl->settings->behaviour->api_url);
        $content = str_replace('</head>', '<link href="' . (isset($apiUrl['scheme'])?$apiUrl['scheme']: 'https') . '://' . $apiUrl['host'] . '" rel="preconnect" crossorigin></head>',
            $content);
        // EXTRACT all CDATA and inline script to be reinserted after the replaces
        // -----------------------------------------------------------------------

        $this->CDATAs = array();
        // this ungreedy regex fails with catastrophic backtracking if the CDATA is very long so will do it the manual way...
        /*$content = preg_replace_callback(
        //     this part matches the scripts of the page, we don't replace inside JS
            '/\<\!\[CDATA\[(.*)\]\]\>/sU', // U flag - UNGREEDY
            array($this, 'replace_cdatas'),
            $content
        );*/
        //$content = $this->replace_cdatas($content);
        $content = $this->extract_blocks($content, '__sp_cdata', '<![CDATA[', ']]>', $this->CDATAs, true);

        //<noscript> blocks will have URLs replaced but eagerly
        $this->noscripts = array();
        $content = $this->extract_blocks($content, '__sp_noscript', '<noscript.', '</noscript>', $this->noscripts);

        $this->scripts = array();
        $content = $this->extract_blocks($content, '__sp_script', '<script.', '</script>', $this->scripts);
        //$content = preg_replace_callback(
        ////     this part matches the scripts of the page, we don't replace inside JS
        //    '/\<script(.*)\<\/script\>/sU', // U flag - UNGREEDY
        //    array($this, 'replace_scripts'),
        //    $content
        //);
        $this->styles = array();
        $content = $this->extract_blocks($content, '__sp_style', '<style.', '</style>', $this->styles);
/*        $content = preg_replace_callback(
        //     this part matches the styles of the page, we replace inside CSS afterwards.
            '/\<style(.*)\<\/style\>/sU', // U flag - UNGREEDY
            array($this, 'replace_styles'),
            $content
        );
*/
	    /**
	     * Isolate the attributes which contain the ">" sign
	     * to avoid the parse conflicts and corrupt the DOM
	     * if "Generate Noscript" options is enabled
	     */
	    $this->attributes = [];
	    $content = $this->isolate_attributes( $content );
	    /*
	     * Old implementation using RegEx
	    $content = preg_replace_callback( '/=(["\'`])(?!\1)((?:(?![^\\\\]\1).)*?(?:>|>.*?(?:[^\\\\])))\1/s', [ $this, 'isolate_attributes' ], $content );
	    */

	    SHORTPIXEL_AI_DEBUG && $this->logger->log( "CHECK 1: " . strlen( $content ) );

        // Replace different cases of image URL usages
        // -------------------------------------------

        /* $content = preg_replace_callback(
        //     this part matches URLs without quotes
            '/\<img[^\<\>]*?\ssrc\=([^\s\'"][^\s>]*)(?:.+?)\>/s',
            array( $this, 'replace_images_no_quotes' ),
            $content
        ); */

        $regexMaster = '/\<({{TAG}})(?:\s|\s[^\<\>]*?\s)({{ATTR}})\s*\=\s*(?:(\"|\')([^\>\'\"]+)(?:\'|\")|([^\>\'\"\s]+))(?:.*?)\>/s';
        $regexMasterSrcset = '/\<({{TAG}})(?:\s|\s[^\<\>]*?\s)({{ATTR}})\s*\=\s*(?:(\")([^\"\>]+?)\"|(\')([^\'\>]+?)\')(?:.*?)\>/s';
        $regexItems = $this->ctrl->getTagRules();

        foreach ($regexItems as $regexItem) {
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array($regexItem[0], $regexItem[1]), $regexMaster);
            //$this->logger->log("REGEX: $regex");
            $this->classFilter = (isset($regexItem[2])) ? $regexItem[2] : false;
            $this->attrFilter = (isset($regexItem[3])) ? $regexItem[3] : false;
            $this->attrValFilter = (isset($regexItem[5])) ? $regexItem[5] : false;
            $this->attrToIntegrateAndRemove = (isset($regexItem[4])) ? $regexItem[4] : false;
            $this->isEager = (isset($regexItem[6])) ? $regexItem[6] : false;
	    $this->extMeta = (isset($regexItem[7])) ? $regexItem[7] : false;
	    SHORTPIXEL_AI_DEBUG && $this->logger->log("******** REGEX IS : " . $regex
            . ($this->classFilter ? " CLASS FILTER: " . $this->classFilter : '')
            . ($this->attrFilter ? " ATTR FILTER: " . $this->attrFilter . ($this->attrValFilter ? '=' . $this->attrValFilter : '') : ''));
            $content = preg_replace_callback($regex,
                array($this, 'replace_images'),
                $content
            );
            $this->classFilter = false;
            $this->attrFilter = false;
        }

        $this->logger->log("******** REGEX PARSER replace_wc_gallery_thumbs *********");

        $content = preg_replace_callback(
            '/\<(div|figure|li)[^\<\>]*?\sdata-thumb(?:nail|)\=(?:\"|\')(.+?)(?:\"|\')(?:.+?)\>/s',
            array($this, 'replace_wc_gallery_thumbs'),
            $content
        );

        $integrations = ActiveIntegrations::_();
        $theme = $integrations->get('theme');

        $this->logger->log("******** REGEX PARSER replace_background_image_from_tag *********");

        $bgTagsList = 'div|span|body|article|section|i|p|h[1-6]|form|img|figure|a|header|li';
        if($integrations->has('slider-revolution')) {
            $bgTagsList .= '|rs-bg-elem';
        }
        //we need to specify the list of tags because on some situations PHP regex implementation throws catastrophic backtracking if we use [\w]+ instead...
        $bkImgRegex = '/\<(' . $bgTagsList . ')([^\<\>]*?)\b(background-image|background)(\s*:(?:[^;]*?[,\s]|\s*))url\((?:\'|")?([^\'"\)]*)(\'|")?\s*\)(.*?)\>/s';
        $contentReplaced = preg_replace_callback( $bkImgRegex, array($this->cssParser, 'replace_background_image_from_tag'), $content);
        if(strlen($contentReplaced)) { // better safe than sorry
            $content = $contentReplaced;
        } else {
            //this can happen if there are HUGE tags like for example a <form with data-product_variations having a large number of variations (HS# 59186)
            SHORTPIXEL_AI_DEBUG && $this->logger->log("******** REGEX PARSER replace_background_image_from_tag FAILED using regex: " . $bkImgRegex);
        }

        if($theme == 'CROWD 2' || $theme == 'Lovely 2') {
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array('div|a|span', 'style'), $regexMasterSrcset);
            $this->logger->log("CROWD 2 theme - regex $regex");
            $content = preg_replace_callback($regex,
                array($this, 'replace_crowd2_img_styles'),
                $content
            );
        }

        if($integrations->get('wp-bakery')) {
            $content = preg_replace_callback(
                '/\<([\w]+)(?:[^\<\>]*?)\b(data-ultimate-bg)(=(?:"|\'|)[^"\']*?)url\((?:\'|")?([^\'"\)]*)(\'|")?\s*\)/s',
                array($this->cssParser, 'replace_wp_bakery_data_ultimate_bg'),
                $content
            );
        }

        if($this->ctrl->settings->behaviour->replace_method !== 'src') { //srcset has to be checked too, in some cases the srcset wp hook isn't called...
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array('img', 'srcset'), $regexMasterSrcset);
            $this->logger->log("SRCSET REGEX: $regex");
            $content = preg_replace_callback($regex,
                array($this, 'replace_custom_srcset'),
                $content
            );
        }

        if ($integrations->get('envira')) {
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array('img', 'data-envira-srcset'), $regexMasterSrcset);
            $this->logger->log("ENVIRA REGEX: $regex");
            $content = preg_replace_callback($regex,
                array($this, 'replace_custom_srcset'),
                $content
            );
        }

        if($theme == 'Jupiter') {
            //Jupiter has the mk slider in it, which uses srcsets encoded as JSON in data-mk-image-src-set atributes. How cool is that.
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array('img', 'data-mk-image-src-set'), $regexMasterSrcset);
            $this->logger->log("Jupiter data-mk: $regex");
            $content = preg_replace_callback($regex,
                array($this, 'replace_custom_json_attr'),
                $content
            );
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array('div', 'data-mk-img-set'), $regexMasterSrcset);
            $content = preg_replace_callback($regex,
                array($this, 'replace_custom_json_attr'),
                $content
            );
        } elseif(strpos($theme, 'Divi') === 0) {
            $this->ctrl->affectedTags->add('div', 2);
            //Divi - Elegant Themes' custom attribute
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array('img', 'data-et-multi-view'), $regexMasterSrcset);
            $this->logger->log("Divi data-et-multi-view: $regex");
            $content = preg_replace_callback($regex,
                array($this, 'replace_custom_json_attr'),
                $content
            );
        } elseif($theme == 'Stack') {
            // Stack moves srcs from images to background-images of divs...
            $this->ctrl->affectedTags->add('div', 2);
        }

        if($integrations->get('acf')) {
            //ACF moves src's from hidden images to FIGURE backgrounds
            $this->ctrl->affectedTags->add('figure', 2);
        }

        if($integrations->get('elementor')) {
            //Elementor can pass image URLs in sections' or divs' data-settings
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array('header|section|div', 'data-settings|data-options'), $regexMasterSrcset);
            $content = preg_replace_callback($regex,
                array($this, 'replace_custom_json_attr'),
                $content
            );
        }

        if($integrations->get('real3d-flipbook')) {
            //Elementor can pass image URLs in sections' or divs' data-settings
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array('div', 'data-flipbook-options'), $regexMasterSrcset);
            SHORTPIXEL_AI_DEBUG && $this->logger->log("REAL 3D FLIPBOOK regex: " . $regex . " Content len: " . strlen($content));
            $content = preg_replace_callback($regex,
                array($this, 'replace_custom_json_attr'),
                $content
            );
        }

        if($integrations->get('woocommerce') && preg_match('/\<form\b[^>]+\bdata-product_variations=/is', $content)) {
            //there are product variations
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array('form', 'data-product_variations'), $regexMasterSrcset);
            SHORTPIXEL_AI_DEBUG && $this->logger->log("WOO REPLACE VARIATIONS regex: " . $regex);
            $content = preg_replace_callback($regex,
                array($this, 'replace_product_variations'),
                $content
            );
        }

        /*if($this->integrations->get('modula')) {
            $regex = str_replace(array('{{TAG}}','{{ATTR}}'), array('img', 'data-envira-srcset'), $regexMasterSrcset);
            $this->logger->log("REGEX: $regex");
            $content = preg_replace_callback( $regex,
                array( $this, 'replace_custom_srcset' ),
                $content
            );
        }*/

        //NextGen uses a data-src and data-thumbnail inside the <a> tag for the image popup
        /*        $content = preg_replace_callback(
                    '/\<(?:a|div)[^\<\>]*?\sdata-src\=(\"|\'?)(.+?)(?:\"|\')(?:.+?)\>/s',
                    array( $this, 'replace_images_data_src' ),
                    $content
                );
                $content = preg_replace_callback(
                    '/\<a[^\<\>]*?\sdata-thumbnail\=(\"|\'?)(.+?)(?:\"|\')(?:.+?)\>/s',
                    array( $this, 'replace_link_data_thumbnail' ),
                    $content
                );
        */
        //TODO this is not working because NextGen is not handling the inline data: images properly
        //TODO check with them
        if ($integrations->get('nextgen')) {
            $content = preg_replace_callback(
                '/\<a[^\<\>]*?\shref\=(\"|\'?)(.+?)(?:\"|\')(?:.+?)\>/s',
                array($this, 'replace_link_href'),
                $content
            );
        }

        //$content = preg_replace_callback(
        //	'/\<div.+?data-src\=(?:\"|\')(.+?)(?:\"|\')(?:.+?)\>\<\/div\>/s',
        //	array( $this, 'replace_wc_gallery_thumbs' ),
        //	$content
        //);

        SHORTPIXEL_AI_DEBUG && $this->logger->log("******** REGEX PARSER put back the at styles, scripts and CDATAs. Content len: " . strlen($content));

        // put an isolated attributes back
        $content = $this->revert_attributes( $content );

        //put back the styles, scripts and CDATAs.
        for ($i = 0; $i < count($this->styles); $i++) {
            $style = $this->styles[$i];
            //$this->logger->log("STYLE $i: $style");
            //replace all the background-image's
            $style = $this->cssParser->replace_inline_style_backgrounds($style);
            //$this->logger->log("STYLE REPLACED: $style");

            $content = str_replace("<style>__sp_style_plAc3h0ldR_$i</style>", $style, $content);
        }

        //handle the noscripts in a simpler (only <img>) and eager way.
        for ($i = 0; $i < count($this->noscripts); $i++) {
            $noscript = $this->noscripts[$i];
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array('img', 'src'), $regexMaster);
            $this->logger->log("NOSCRIPT: $noscript");
            $this->classFilter = false;
            $this->attrFilter = false;
            $this->attrValFilter = false;
            $this->attrToIntegrateAndRemove = false;
            $this->isEager = true;
            $this->extMeta = false;
            $noscript = preg_replace_callback($regex,
                array($this, 'replace_images'),
                $noscript
            );
            $this->logger->log("NOSCRIPT SRC: $noscript");
            $regex = str_replace(array('{{TAG}}', '{{ATTR}}'), array('img', 'srcset'), $regexMasterSrcset);
            $noscript = preg_replace_callback($regex,
                array($this, 'replace_custom_srcset'),
                $noscript
            );
            $this->logger->log("NOSCRIPT SRCSET: $noscript");
            $content = str_replace("<noscript>__sp_noscript_plAc3h0ldR_$i</noscript>", $noscript, $content);
        }

        for ($i = 0; $i < count($this->scripts); $i++) {
            $this->logger->log("SCRIPT $i");
            $script = $this->scripts[$i];
            if($this->ctrl->settings->areas->parse_json) {
                if(preg_match('/^(\<script[^>]*(?:\btype=(?:"|\')application\/(?:ld\+|)json(?:"|\'))[^>]*\>)/sU', $script)) {
                    $script = preg_replace_callback('/^(\<script[^>]*(?:\btype=(?:"|\')application\/(?:ld\+|)json(?:"|\'))[^>]*\>)(.*)\<\/script\>/sU',
                        array($this, 'replace_application_json_script'),
                        $script
                    );
                }

            }

			if ( $this->ctrl->settings->areas->parse_js ) {
				if ( strpos( $script, '__sp_cdata_plAc3h0ldR_' ) ) {
					$script = preg_replace_callback( '/__sp_cdata_plAc3h0ldR_([0-9]+)/s',
						array( $this, 'replace_cdata_js' ),
						$script
					);
				}
				else {
                    $script = trim($script);
				    $openingTagEnd = strpos($script, '>') + 1;
				    $openingTag = substr($script, 0, $openingTagEnd);
				    if(preg_match('/\btype=["\'](application|text)\/javascript/', $openingTag) || !preg_match('/\btype=/', $openingTag))
				    { // either it's type text/javascript or type not defined. Outstanding example that should not be parsed:  type=text/template (HS#51163)
                        $jsParser = new ShortPixelJsParser( $this->ctrl );

                        $parsed_script = $jsParser->parse( $script );

                        $script = empty( $parsed_script ) ? $script : $parsed_script;
                    }
                    else if(preg_match('/\btype=["\']text\/template/', $openingTag)) {
                        $endTagStart = strrpos($script, "</script");
                        if($endTagStart > $openingTagEnd) {
                            $tpl = trim(substr($script, $openingTagEnd, $endTagStart - $openingTagEnd));
                            $tpl = json_decode($tpl);
                            $parser = new ShortPixelRegexParser($this->ctrl);
                            $parsed_script = $parser->parse($tpl);
                            (SHORTPIXEL_AI_DEBUG & ShortPixelAILogger::DEBUG_AREA_JSON) && $this->logger->log("TEXT TEMPLATE BY REGEX: " . $parsed_script);
                            $script = empty( $parsed_script ) ? $script : $openingTag . json_encode($parsed_script) . '</script>';
                        }
                    }
				}
			}

            $content = str_replace("<script>__sp_script_plAc3h0ldR_$i</script>", $script, $content);
        }

	    for ( $i = 0; $i < count( $this->CDATAs ); $i++ ) {
		    $content = str_replace( "<![CDATA[\n__sp_cdata_plAc3h0ldR_$i\n]]>", $this->CDATAs[ $i ], $content );
	    }
      
	    // $content = str_replace('{{SPAI-AFFECTED-TAGS}}', implode(',', array_keys($this->ctrl->affectedTags)), $content);

	    $this->ctrl->affectedTags->remove( 'img' );

	    if ( strpos( $content, '{{SPAI-AFFECTED-TAGS}}' ) ) {
		    $this->logger->log( "AFFECTED TAGS PLACEHOLDER FOUND: ", $this->ctrl->affectedTags->getAll() );
		    $content = str_replace( '{{SPAI-AFFECTED-TAGS}}', addslashes( json_encode( $this->ctrl->affectedTags->getAll() ) ), $content );
	    }
	    else {
		    $this->logger->log( "AFFECTED TAGS PLACEHOLDER NOT FOUND: ", $this->ctrl->affectedTags->getAll() );
		    $content = str_replace( '</body>', '<script>var spai_affectedTags = "' . addslashes( json_encode( $this->ctrl->affectedTags->getAll() ) ) . '";</script></body>', $content );
	    }

        SHORTPIXEL_AI_DEBUG && $this->logger->log("******** REGEX PARSER RETURN ********* " . strlen($content) . (SHORTPIXEL_AI_DEBUG & ShortPixelAILogger::DEBUG_INCLUDE_CONTENT ? $content : ''));

        return $content;
    }

	protected function replace_cdata_js( $matches ) {
		$jsParser = new ShortPixelJsParser( $this->ctrl );
		$index    = (int) $matches[ 1 ];
		$script   = $this->CDATAs[ $index ];

		if ( strpos( $script, 'var spai_settings' ) === false ) { //don't parse our own JS block...
			$parsed_script = $jsParser->parse( $script );

			$this->CDATAs[ $index ] = empty( $parsed_script ) ? $script : $parsed_script;
		}

		return $matches[ 0 ];
	}

//    protected function replace_cdata_js($matches) {
//        $jsParser = new ShortPixelJsParser($this->ctrl);
//        $key = '__sp_cdata_plAc3h0ldR_' . $matches[1];
//        $script = $this->CDATAs[$key];
//        if(strpos($script, 'var spai_settings') === false) { //don't parse our own JS block...
//            $this->CDATAs[$key] = $jsParser->parseJsonBlocks($script, 'cdata');
//        }
//        return $matches[0];
//    }

    public function replace_crowd2_img_styles($matches) {
        $text = $matches[0];
        if(isset($matches[5]) && $matches[5] == '\'') {
            $matches[3] = $matches[5];
            $matches[4] = $matches[6];
        }
        $style = $matches[4];
        if(strpos($style, '--img-') === false) return $text;
        $qm = strlen($matches[3]) ? $matches[3] : '"';

        $pattern = $matches[2] . '=' . $matches[3] . $matches[4] . $matches[3];
        $pos = strpos($text, $pattern);
        if($pos === false) return $text;

        $replacedStyle = $this->cssParser->replace_crowd2_img_styles($matches[4]);
        $replacement = ' ' . $matches[2] . '=' . $qm . $replacedStyle . $qm;

        $this->logger->log("CROWD2 - style found, string $pattern is replaced by $replacement");

        $str = substr($text, 0, $pos) . $replacement . substr($text, $pos + strlen($pattern));
        return $str;
    }

    /**
     * extract the specific block into the store array. Preferred over the out-of-the box preg_replace_callback because as the content of the blocks must be un-greedy, in many cases when the blocks are large
     * a "catastrophic backtrace" exception is thrown by the PHP function.
     * @param $content
     * @param $id - code for the block - will be put in the replacement
     * @param $startMarker eg. <![CDATA[ or <noscript>. If the last character is '.' then the string without it will be searched but replaced with the string ending in '>' instead of '.' ( search for <script and replace with <script>)
     * @param $endMarker eg ]]> or </noscript>
     * @param $store - by ref. the array in which the extracted blocks will be put
     * @param bool $newLine if true it adds a new line after/before the start/end markers, needed for CDATA
     * @return string - the changed $content
     */
	public function extract_blocks( $content, $id, $startMarker, $endMarker, &$store, $newLine = false ) {
        $matches = array();
        $startMarkerRepl = $startMarker;
        if(substr($startMarker, -1) == '.') {
            $startMarker = substr($startMarker, 0, -1);
            $startMarkerRepl = $startMarker . '>';
        }
        $startMarkerLen = strlen($startMarker);
        $endMarkerLen = strlen($endMarker);
        for($idx = 0, $match = false, $len = strlen($content); $idx < $len - $endMarkerLen + 1; $idx++) {
            if($match) {
                if(substr( $content, $idx, $endMarkerLen) == $endMarker) {
                    //end of CDATA block
                    $matches[] = (object)array('start' => $match ? $match : 0, 'end' => $idx + $endMarkerLen - 1);
                    $idx += $endMarkerLen - 1;
                    $match = false;
                }
            } else {
                if(substr($content, $idx, $startMarkerLen) == $startMarker) {
                    $match = $idx;
                    $idx += $startMarkerLen - 1;
                }
            }
        }
        $this->logger->log(" MATCHED $startMarker BLOCKS: " . json_encode($matches));
        $replacedContent = '';
        $nl = ($newLine ? "\n" : '');
        for($idx = 0; $idx < count($matches); $idx++) {
            $start = isset($matches[$idx - 1]) ? $matches[$idx - 1]->end + 1 : 0;
            $replacedContent .= substr($content, $start, $matches[$idx]->start - $start) . $startMarkerRepl . $nl . $id . '_plAc3h0ldR_' .$idx . $nl . $endMarker;
            $cdata = substr($content, $matches[$idx]->start, $matches[$idx]->end - $matches[$idx]->start + 1);
            SHORTPIXEL_AI_DEBUG && $this->logger->log(" MATCHED AND EXTRACTED $idx: " . $cdata);
            $store[] = $cdata;
        }
        $replacedContent .= substr($content, isset($matches[$idx - 1]) ? $matches[$idx - 1]->end + 1 : 0);
        return $replacedContent;
    }

	/*
	 * Old implementation
	 *
	private function isolate_attributes( $matches ) {
		$index = count( $this->attributes );

		$this->logger->log( 'ISOLATED ATTRIBUTE BODY:', $matches[ 2 ] ); // second group is a body of the affected attribute

		$this->attributes[] = $matches[ 2 ]; // second group is a body of the affected attribute

		return str_replace( $matches[ 2 ], '__sp_attribute_plAc3h0ldR_' . $index, $matches[ 0 ] );
	}
	*/

	/**
	 * Method parses & isolates attributes which could affect the DOM for future parsing, notably attributes containing <tags>
	 *
	 * @param string $content HTML content
	 *
	 * @return string content with placeholders instead of the extracted attributes
	 */
	private function isolate_attributes( $content ) {
        //first mark down each attribute that contains >
        for ($attributes = [], $attributesContent = [],
             $inTag = false, $attrStartMarker = 0, $inAttr = false, $toBeExtracted = false, $attrDelim = false,
             $len = strlen($content),
             $index = 0; $index < $len; $index++) {
            if(!$inTag ) {
                if( $content[$index] == '<') {
                    //start of a tag
                    $inTag = true;
                } // else we just increment no matter what
            }
            else {
                if(!$inAttr ) {
                    if($content[$index] == '>') {
                        //end of a tag
                        $inTag = false;
                    }
                    elseif(($content[$index] == '"' | $content[$index] == '\'') && $content[$index-1] == '=' ) {
                        $attrStartMarker = $index;
                        $attrDelim = $content[$index];
                        $inAttr = true;
                    }
                }
                else {
                    if($content[$index] == '>') {
                        //that's our attribute, this one will need to be replaced
                        $toBeExtracted = true;
                    } elseif($content[$index] == $attrDelim && $content[$index - 1] != '\\') {
                        //the attribute just ended
                        $inAttr = false;
                        if($toBeExtracted) {
                            $toBeExtracted = false;
                            $attributes[] = (object)['start' => $attrStartMarker + 1, 'end' => $index];
                            $attributesContent[] =  substr($content, $attrStartMarker + 1, $index - $attrStartMarker - 1);
                        }
                    }

                }
            }
        }
        //now replace them
        if(count($attributes)) {
            for ($parsedContent = '', $index = 0, $prev = 0; $index < count($attributes); $index++) {
                $parsedContent .= substr($content, $prev, $attributes[$index]->start - $prev) . '__sp_attribute_plAc3h0ldR_' . $index . '_';
                $prev = $attributes[$index]->end;
            }
            $parsedContent .= substr($content, $prev);
            $this->attributes = $attributesContent;
            return $parsedContent;
        }
        
		return $content;
	}

	/**
	 * Method reverts isolated attributes which could affect the DOM
	 *
	 * @param string $content HTML content which has been parsed
	 *
	 * @return string
	 */
	private function revert_attributes( $content ) {
		if ( !empty( $this->attributes ) ) {
			foreach ( $this->attributes as $index => $attribute ) {
				SHORTPIXEL_AI_DEBUG && $this->logger->log( 'REVERTED ATTRIBUTE VALUE: ' . $attribute );

				$content = str_replace( '__sp_attribute_plAc3h0ldR_' . $index . '_', $attribute, $content );
			}
		}

		return $content;
	}

    public function replace_scripts($matches)
    {
        $index = count($this->scripts);
        $this->scripts[] = $matches[0];
        return "<script>__sp_script_plAc3h0ldR_$index</script>";
    }

    public function replace_styles($matches)
    {
        //$this->logger->log("STYLE: " . $matches[0]);
        $index = count($this->styles);
        $this->styles[] = $matches[0];
        return "<style>__sp_style_plAc3h0ldR_$index</style>";
    }

    public function replace_images($matches)
    {
        $text = $matches[0];
        $tag = $matches[ 1 ];
        $attr = $matches[ 2 ];
        $url = isset( $matches[ 5 ] ) ? $matches[ 5 ] : $matches[ 4 ];
        $q = $matches[ 3 ]; //$matches[3] will be either " or '

        if (count($matches) < 5 ||  preg_match('/\s' . $attr . '=["\']{0,1}data\:image\/svg\+xml;/s', $text)) {
            //avoid duplicated replaces due to filters interference
            return $text;
        }
        if ($this->classFilter && !preg_match('/\bclass=(\"|\').*?\b' . $this->classFilter . '\b.*?(\"|\')/s', $text)) {
            return $text;
        }

	    if ( $this->attrFilter ) {
		    if ( $this->attrValFilter ) {
			    if ( !preg_match( '/\b' . $this->attrFilter . '=((\"|\'|\`)[^\"|\'|\`]*\b|)' . preg_quote( $this->attrValFilter, '/' ) . '/s', $text ) ) {
				    return $text;
			    }
		    }
		    else {
			    $stripped = preg_replace( '/(\"|\'|\`).*?(\"|\'|\`)/s', ' ', $text ); //keep only the attribute's names
			    if ( !preg_match( '/\b' . $this->attrFilter . '(?=\s|=|\/?>)/s', $stripped ) ) {
				    return $text;
			    }
		    }
	    }

	    $noresize = true; //it's a return param actually, if not modified, don't add the noscript tag
	    $processed_tag = $this->_replace_images( $tag, $attr, $text, $url, $q, $noresize );

	    if (   $tag == 'img' //noscript makes sense only for img tags
	        && $this->ctrl->options->settings_behaviour_replaceMethod === 'src'
            && !!$this->ctrl->options->settings_behaviour_generateNoscript
            && !$noresize) { // don't add noscript if the image is eager. (HS#41939)
	        $eager = $this->isEager;
            $this->isEager = true;
            SHORTPIXEL_AI_DEBUG && $this->logger->log("NOSCRIPT TAG FOR " . $url);
            //replace the URLs with eager URLs inside noscript.
		    $processed_tag .= '<noscript data-spai="1">' . $this->_replace_images($tag, $attr, $text, $url, $q) . '</noscript>';
		    $this->isEager = $eager;
	    }

	    return $processed_tag;
    }

    protected function _replace_images($tag, $attr, $text, $url, $q = '', &$noresize = false) {
        SHORTPIXEL_AI_DEBUG && $this->logger->log("******** REPLACE IMAGE IN $tag ATTRIBUTE $attr: '" . $url . "'" . ($this->extMeta ? " EXT" : " INT"));

	    if (
		    !$this->ctrl->lazyNoticeThrown && substr( $url, 0, 10 ) == 'data:image'
		    && ( strpos( $text, 'data-lazy-src=' ) !== false
		         || strpos( $text, 'data-layzr=' ) !== false
		         || strpos( $text, 'data-src=' ) !== false
		         || ( strpos( $text, 'data-orig-src=' ) !== false && strpos( $text, 'lazyload' ) ) //found for Avada theme with Fusion Builder
		    )
	    ) {
		    set_transient( "shortpixelai_thrown_notice", [ 'when' => 'lazy', 'extra' => false, 'causer' => 'regex parser', 'text' => $text ], 86400 );
		    $this->ctrl->lazyNoticeThrown = true;
	    }
	    //if($this->ctrl->lazyNoticeThrown) {
        //    $this->logger->log("Lazy notice thrown");
        //    return $text;
        //}

        $extMeta = $this->extMeta; //TODO REMOVE OBSOLETE
        if($this->ctrl->urlIsApi($url)) {$this->logger->log('IS API');return $text;}
        if(!ShortPixelUrlTools::isValid($url)) {$this->logger->log('NOT VALID');return $text;}
        if($this->ctrl->urlIsExcluded($url)) {$this->logger->log('EXCLUDED');return $text;}

        //custom exclusion for SliderRevolution TODO unhack
        if(ActiveIntegrations::_()->get('slider-revolution') && preg_match('/plugins\/revslider\/.*\/dummy.png$/', $url )) {
            return $text;
        }

        $pristineUrl = $url;
        //WP is encoding some characters, like & ( to &#038; ), and oh, some URLs end in spaces...
        $url = trim(html_entity_decode($url));

        //early check for the excluded selectors - only the basic cases when the selector is img.class
        if($this->ctrl->tagIs('excluded', $text)) {
            $this->logger->log("Excluding: " . $text);
            return $text;
        }
        //prevent cases when html code including data-spai attributes gets copied into new articles
        if(strpos($text, 'data-spai') > 0) {
            //if(strpos($text, 'data:image/svg+xml;' . ($extMeta ? 'base64' : 'u=')) > 0) {
            //  this way it works if we have two rules for the same tag, eg: img:src and img:data-src etc. - fix for
            if(preg_match('/\s' . $attr . '=["\']{0,1}data\:image\/svg\+xml;/s', $text)) {
                //for cases when the src is pseudo
                //Seems that Thrive Architect is doing something like this under the hood? (see https://secure.helpscout.net/conversation/862862953/16430/)
                $this->logger->log("Ignoring $tag - $attr because it's already data:img: " . $url);
                return $text;
            }
            //for cases when it's normal URL, just get rid of data-spai's
            $text = preg_replace('/data-spai(-upd|)=["\'][0-9]*["\']/s', '', $text);
        }

        $noresize = $this->isEager || $this->ctrl->tagIs('noresize', $text) || $this->ctrl->tagIs('eager', $text);

        SHORTPIXEL_AI_DEBUG && $this->logger->log("Including: " . $url . ($noresize ? ' EAGER' : ' LAZY'));

	    //some particular cases are hardcoded here...
	    // 1. Revolution Slider (rev-slidebg) Glow Pro's Swiper slider (attachment-glow-featured-slider-img) and Optimizer PRO's frontpage slider (stat_bg_img)
	    if ( strpos( $text, 'attachment-glow-featured-slider-img' ) ) {
		    $this->ctrl->affectedTags->add( 'figure', 1 ); //the Glow Pro moves the image in <figure> tags
	    }
	    //    both discard the data-spai-src-meta :( so we will put the original URL inside the src
	    if ( $extMeta && $tag == 'img' && preg_match( '/\bclass=(\"|\').*?\b(rev-slidebg|stat_bg_img|attachment-glow-featured-slider-img)\b.*?(\"|\')/s', $text ) ) {
		    $extMeta = false;
	    }

        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        //Get current image size
        if($ext === 'css') { //no need to calculate the image size if it's CSS
            $sizes = [1, 1, 'thumb' => false];
            $AR = 1;
        } else {
            $sizes = ShortPixelUrlTools::get_image_size($url);
            $AR = (isset($sizes[0]) && $sizes[0] && $sizes[1]) ? $sizes[0] / $sizes[1] : 1; //it appears that in some circumstances sizes[0] could be 0 and sizes[1] > 0... HS#42421
            $this->logger->log('Got Sizes: ', $sizes);
        }
        $qex = strlen($q) ? '' : '"';
        $qm = strlen($q) ? $q : '"';
        $atirReplacement = '';

        if((isset($sizes['thumb']) || $noresize) && $this->attrToIntegrateAndRemove) {
            $this->logger->log('Integrate and remove: ' . $this->attrToIntegrateAndRemove . '. Sizes: ', $sizes);
            $matches = false;
            $this->logger->log('TEXT: ' . $text);
            if(preg_match('/\b' . $this->attrToIntegrateAndRemove . '=(\"|\')(.*?)(?:\"|\')/s', $text, $matches) && isset($matches[2]) && strlen(trim($matches[2]))){
                $this->logger->log('Matched:', $matches);
                $items = explode(',', $matches[2]);
                foreach($items as $item) {
                    $parts = explode(' ', trim($item));

                    if($noresize) {
                        $atirReplacement .= (strlen($atirReplacement) ? ', ': ' ' . $this->attrToIntegrateAndRemove . '="')
                            . ($this->ctrl->urlIsApi($parts[0]) ? $parts[0] : $this->ctrl->get_api_url(false, false) . '/' . ShortPixelUrlTools::absoluteUrl($url))
                            . (isset($parts[1]) ? ' ' . $parts[1] : '');
                    }
                    elseif(isset($sizes['thumb'])) {
                        $partSizes = ShortPixelUrlTools::get_image_size($parts[0]);
                        if(isset($partSizes[0]) && isset($partSizes[1]) && $partSizes [0] > 0 && $partSizes [1] > 0) {
                            $partAR = $partSizes[0] / $partSizes[1];
                            if($partSizes[0] > $sizes[0]
                                && abs(($partAR - $AR) / $AR) < 0.03) {
                                $sizes = $partSizes;
                                $url = $parts[0];
                            }
                        }
                    }
                }
                if(strlen($atirReplacement)) {
                    $atirReplacement .= '"';
                }
            }
        }

        $spaiMeta = ''; $spaiMarker = ' data-spai=' . $qm . '1' . $qm; $dataLityTarget = '';
        if($noresize) {
            $inlinePlaceholder = $this->ctrl->get_api_url(false, false, $this->ctrl->get_extension( $url )) . ($ext == 'css' ? '+v_' . $this->ctrl->cssCacheVer : '') . '/' . ShortPixelUrlTools::absoluteUrl($url);
            $spaiMarker = ' data-spai-egr=' . $qm . '1' . $qm;
        } else {
            //lazy
            if ( $tag == 'img' && $attr == 'src' && !!$this->ctrl->settings->areas->lity && preg_match('/\bdata-lity\b/', $text) ) {
                $dataLityTarget = ' data-lity-target="' . $this->ctrl->get_api_url(false, false, $this->ctrl->get_extension( $url )) . '/' . ShortPixelUrlTools::absoluteUrl($url) . '"';
                SHORTPIXEL_AI_DEBUG && $this->logger->log("LITY TARGET " . $dataLityTarget);
            }

            if($extMeta){
                $url = apply_filters('shortpixel/ai/originalUrl', $url); //# 37750
                $data = isset($sizes[0]) ? ShortPixelUrlTools::generate_placeholder_svg_pair($sizes[0], $sizes[1], /*$this->absoluteUrl(*/$url) : ShortPixelUrlTools::generate_placeholder_svg_pair(false, false, $url);
                $inlinePlaceholder = $data->image;
                $spaiMeta = $data->meta ? ' data-spai-' . $attr . '-meta="' . $data->meta . '"' : '';
            } else {
                $url = apply_filters('shortpixel/ai/originalUrl', $url); //# 37750
                $inlinePlaceholder = isset($sizes[0]) ? ShortPixelUrlTools::generate_placeholder_svg($sizes[0], $sizes[1], /*$this->absoluteUrl(*/$url) : ShortPixelUrlTools::generate_placeholder_svg(false, false, $url);
            }
        }
        $pattern = '/\s' . $attr . '\s*=\s*' . preg_quote($q . $pristineUrl . $q, '/') . '/';
        $replacement = ' '. $attr . '=' . $qm . $inlinePlaceholder . $qm . $spaiMarker . $spaiMeta . $dataLityTarget;

        SHORTPIXEL_AI_DEBUG && $this->logger->log("REPLACING: Pattern " . $pattern . " with " . $replacement . " in " . $text);

        $str = preg_replace($pattern, $replacement, $text);

        if($this->attrToIntegrateAndRemove) {
            $str = preg_replace('/' . $this->attrToIntegrateAndRemove . '=(\"|\').*?(\"|\')/s', $atirReplacement, $str);
            if($this->attrToIntegrateAndRemove == 'srcset' && $atirReplacement == '') {
                //also make sure we remove the sizes if srcset was removed (HS# 59394)
                $str = preg_replace('/\bsizes=(\"|\').*?(\"|\')/s', '', $str);
            }
        }
        $this->ctrl->affectedTags->add($tag, 1);
        SHORTPIXEL_AI_DEBUG && $this->logger->log("Replaced pattern: $pattern with $replacement. RESULTED TAG: $str\n\n");
        return $str;// . "<!-- original url: $url -->";
    }

    public  function replace_wc_gallery_thumbs( $matches ) {
        $tag = $matches[1];
        if(strpos($matches[2], '%3C') === 0) { //it's HTML urlencoded, HS #38440
            $parser = new ShortPixelRegexParser($this->ctrl);
            return str_replace($matches[2], rawurlencode($parser->parse(urldecode($matches[2]))), $matches[0]);
        }
        if(!ShortPixelUrlTools::isValid($matches[2])) {
            return $matches[0];
        }
        $url = ShortPixelUrlTools::absoluteUrl($matches[2]);
        $str = str_replace($matches[2], $this->ctrl->get_api_url(false, false, $this->ctrl->get_extension( $url )) . '/' . $url, $matches[0]);
        if($str != $matches[0]) {
            $this->ctrl->affectedTags->add($tag, 1);
        }
        return $str;
    }

    /**
     * for data-envira-srcset currently
     * @param $matches
     * @return null|string|string[]
     */
    public function replace_custom_srcset($matches)
    {
        $this->logger->log("REPLACE CUSTOM SRCSET ", $matches);
        if(isset($matches[5]) && $matches[5] == '\'') {
            $matches[3] = $matches[5];
            $matches[4] = $matches[6];
        }
        $qm = strlen($matches[3]) ? $matches[3] : '"';
        $text = $matches[0];
        $pattern = $matches[2] . '=' . $matches[3] . $matches[4] . $matches[3];
        $replacement = ' ' . $matches[2] . '=' . $qm . $this->replace_srcset($matches[4]) . $qm;
        if($this->ctrl->settings->behaviour->replace_method === 'src' && strpos($text, 'loading=') === false) {
            $replacement .= ' loading="lazy"';
        }
        $pos = strpos($text, $pattern);
        if($pos === false) return $text;
        $str = substr($text, 0, $pos) . $replacement . substr($text, $pos + strlen($pattern));
        return $str;// . "<!-- original url: $url -->";
    }

    /**
     * parses a <script type="application/json">
     * @param $matches
     */
    public function replace_application_json_script($matches) {
        $lazy = strpos($matches[1], 'application/ld+json') === false ? $this->ctrl->settings->areas->parse_json_lazy : 0; //ld+json is structured data, don't lazy-load (0 marks not lazy-load
        $jsonParser = new ShortPixelJsonParser($this->ctrl, $lazy);
        $dataObj = json_decode(str_replace('&quot;', '"', $matches[2]));
        if(json_last_error() === JSON_ERROR_SYNTAX) {
            $this->logger->log("APPLICATION JSON syntax error", $matches);
            return $matches[0];
        }
        $this->logger->log("APPLICATION / JSON", $dataObj);
        return $matches[1] . json_encode($jsonParser->parse($dataObj)) . '</script>';
    }

    /**
     * for data-envira-srcset currently
     * @param $matches
     * @return null|string|string[]
     */
    public function replace_custom_json_attr($matches)
    {
        $qm = strlen($matches[3]) ? $matches[3] : '"';
        $text = $matches[0];
        $tag = $matches[1];
        $attr = $matches[2];
        if(isset($matches[5]) && $matches[5] == '\'') {
            $matches[3] = $matches[5];
            $matches[4] = $matches[6];
        }
        $jsonParser = new ShortPixelJsonParser($this->ctrl);
        $parsed = json_decode(str_replace('&quot;', '"', $matches[4]));
        if(json_last_error() === JSON_ERROR_SYNTAX) return $text;
        $replaced = str_replace('"', '&quot;',json_encode($jsonParser->parse($parsed), JSON_UNESCAPED_SLASHES));
        if($matches[4] == $replaced) return $text;
        $pattern = $attr . '=' . $matches[3] . $matches[4] . $matches[3];
        $replacement = ' ' . $attr . '=' . $qm . $replaced . $qm . ' data-spai-egr='. $qm . '1' . $qm;
        $pos = strpos($text, $pattern);
        if($pos === false) return $text;
        $str = substr($text, 0, $pos) . $replacement . substr($text, $pos + strlen($pattern));
        if(strtolower($tag) == 'section') {
            $this->ctrl->affectedTags->add('div', 3);
        }
        return $str;// . "<!-- original url: $url -->";
    }

    /**
     * for data-product_variations
     * @param $matches
     * @return null|string|string[]
     */
    public function replace_product_variations($matches)
    {
        SHORTPIXEL_AI_DEBUG && $this->logger->log("PRODUCT VARIATION", $matches);
        if(isset($matches[5]) && $matches[5] == '\'') {
            $matches[3] = $matches[5];
            $matches[4] = $matches[6];
        }
        $qm = strlen($matches[3]) ? $matches[3] : '"';
        SHORTPIXEL_AI_DEBUG && $this->logger->log("PRODUCT VARIATION QM ", $qm);
        $parsed = json_decode(str_replace('&quot;', '"', $matches[4]));
        SHORTPIXEL_AI_DEBUG && $this->logger->log("PRODUCT VARIATION PARSED: ", $parsed);
        $text = $matches[0];
        SHORTPIXEL_AI_DEBUG && $this->logger->log("PRODUCT VARIATION JSON LAST ERR: ", function_exists('json_last_error') ? json_last_error() : 'function missing');
        if(($err = json_last_error()) === JSON_ERROR_SYNTAX) {
            SHORTPIXEL_AI_DEBUG && $this->logger->log("PRODUCT VARIATIONS - JSON PARSE ERROR " . $err);
            return $text;
        }
        if(!is_array($parsed)) {
            SHORTPIXEL_AI_DEBUG && $this->logger->log("PRODUCT VARIATIONS - NOT AN ARRAY " . get_class($parsed));
            return $text;
        }
        $this->logger->log('PRODUCT VARIATIONS - replacing');
        $aiUrl = $this->ctrl->get_api_url(false);
        for($i = 0; $i < count($parsed); $i++) {
            if(!empty($parsed[$i]->image->src)) {
                SHORTPIXEL_AI_DEBUG && $this->logger->log("VARIATIONS - src " . $parsed[$i]->image->src);
                $parsed[$i]->image->src = $aiUrl . '/' . ShortPixelUrlTools::absoluteUrl(trim($parsed[$i]->image->src));
            }
            if(!empty($parsed[$i]->image->full_src)) {
                SHORTPIXEL_AI_DEBUG && $this->logger->log("VARIATIONS - full_src " . $parsed[$i]->image->full_src);
                $parsed[$i]->image->full_src = $aiUrl . '/' . ShortPixelUrlTools::absoluteUrl(trim($parsed[$i]->image->full_src));
            }
            if(!empty($parsed[$i]->image->thumb_src)) {
                SHORTPIXEL_AI_DEBUG && $this->logger->log("VARIATIONS - thumb_src " . $parsed[$i]->image->src);
                $parsed[$i]->image->thumb_src = $aiUrl . '/' . ShortPixelUrlTools::absoluteUrl(trim($parsed[$i]->image->thumb_src));
            }
            if(!empty($parsed[$i]->image->gallery_thumbnail_src)) {
                SHORTPIXEL_AI_DEBUG && $this->logger->log("VARIATIONS - gallery_thumbnail_src " . $parsed[$i]->image->src);
                $parsed[$i]->image->gallery_thumbnail_src = $aiUrl . '/' . ShortPixelUrlTools::absoluteUrl(trim($parsed[$i]->image->gallery_thumbnail_src));
            }
            if(!empty($parsed[$i]->image->url)) {
                SHORTPIXEL_AI_DEBUG && $this->logger->log("VARIATIONS - url " . $parsed[$i]->image->src);
                $parsed[$i]->image->url = $aiUrl . '/' . ShortPixelUrlTools::absoluteUrl(trim($parsed[$i]->image->url));
            }
            if(!empty($parsed[$i]->image->srcset)) {
                SHORTPIXEL_AI_DEBUG && $this->logger->log("VARIATIONS - srcset " . $parsed[$i]->image->srcset);
                // $parsed[$i]->image->srcset = preg_replace_callback('/data:image\/svg\+xml;u=[^\s]*/s', // old
                $parsed[$i]->image->srcset = preg_replace_callback('/data:image\/svg\+xml;base64[^\s]*/s', // new
                    array($this, 'pseudo_url_to_api_url'),
                    $parsed[$i]->image->srcset
                );
                SHORTPIXEL_AI_DEBUG && $this->logger->log("VARIATIONS - srcset REPLACED placeholders? " . $parsed[$i]->image->srcset);
                $parsed[$i]->image->srcset = $this->replace_srcset( $parsed[$i]->image->srcset );
            }
            if(isset($parsed[$i]->variation_gallery_images) && is_array($parsed[$i]->variation_gallery_images)) {
                foreach ($parsed[$i]->variation_gallery_images as $variation_gallery_image) {
                    if(isset($variation_gallery_image->src)) {
                        $variation_gallery_image->src = $aiUrl . '/' . ShortPixelUrlTools::absoluteUrl(trim($variation_gallery_image->src));
                    }
                    if(isset($variation_gallery_image->archive_src)) {
                        $variation_gallery_image->archive_src = $aiUrl . '/' . ShortPixelUrlTools::absoluteUrl(trim($variation_gallery_image->archive_src));
                    }
                    if(isset($variation_gallery_image->gallery_thumbnail_src)) {
                        $variation_gallery_image->gallery_thumbnail_src = $aiUrl . '/' . ShortPixelUrlTools::absoluteUrl(trim($variation_gallery_image->gallery_thumbnail_src));
                    }
                    if(isset($variation_gallery_image->srcset)) {
                        $variation_gallery_image->srcset = $this->replace_srcset($variation_gallery_image->srcset);
                    }
                }
            }
        }
        $replaced = str_replace('"', '&quot;',json_encode($parsed, JSON_UNESCAPED_SLASHES));
        if($matches[4] == $replaced) return $text;
        $pattern = $matches[2] . '=' . $matches[3] . $matches[4] . $matches[3];
        $replacement = $matches[2] . '=' . $qm . $replaced . $qm . ' data-spai-egr=' . $qm . '1' . $qm;
        $pos = strpos($text, $pattern);
        if($pos === false) return $text;
        $str = substr($text, 0, $pos) . $replacement . substr($text, $pos + strlen($pattern));
        return $str;// . "<!-- original url: $url -->";
    }

    public function pseudo_url_to_api_url($match){
        $this->logger->log("VARIATIONS SRCSET ITEM", $match);
        $url = ShortPixelUrlTools::url_from_placeholder_svg($match[0]);
        $this->logger->log("VARIATIONS SRCSET URL", $url);
        return $this->ctrl->get_api_url(false, false, $this->ctrl->get_extension( $url )) . '/' . ShortPixelUrlTools::absoluteUrl($url);
    }

    public function replace_srcset($srcset) {
        $aiSrcsetItems = array();
        $aiUrl = $this->ctrl->get_api_url(false);
        $aiUrlBase = $this->ctrl->settings->behaviour->api_url;
        $srcsetItems = explode(',', $srcset);
        foreach($srcsetItems as $srcsetItem) {
            $srcsetItem = trim($srcsetItem);
            $srcsetItemParts = explode(' ', $srcsetItem);
            if($this->ctrl->urlIsExcluded($srcsetItemParts[0])) {
                //if any of the items are excluded, don't replace
                SHORTPIXEL_AI_DEBUG && $this->logger->log("REPLACE SRCSET abort - excluded: " . $srcsetItem);
                return $srcset;
            }
            if(strpos($srcsetItem, $aiUrlBase) !== false || strpos($srcsetItem, 'data:image/') === 0) {
                SHORTPIXEL_AI_DEBUG && $this->logger->log("REPLACE SRCSET abort - AI url: " . $srcsetItem);
                return $srcset; //already parsed by the hook.
            }
            $prefix = strpos($aiUrl, 'http') === 0 ? '' : 'http:';
            $aiSrcsetItems[] = $prefix . $aiUrl . '/' . ShortPixelUrlTools::absoluteUrl(trim($srcsetItem));
        }
        return implode(', ', $aiSrcsetItems);
    }

    //NextGen specific
    //TODO make gallery specific
    public function replace_link_href($matches)
    {
//        if (count($matches) < 3 || strpos($matches[0], 'href=' . $matches[1] . 'data:image/svg+xml;u=') // old implementation
        if (count($matches) < 3 || strpos($matches[0], 'href=' . $matches[1] . 'data:image/svg+xml;') // I think "u=" can be removed too
            || strpos($matches[0], 'ngg-fancybox') === false) { //this is to limit replacing the href to NextGen's fancybox links
            //avoid duplicated replaces due to filters interference
            return $matches[0];
        }
        //$matches[1] will be either " or '
        return $this->_replace_images('a', 'href', $matches[0], $matches[2], $matches[1]);
    }

    public static function parseInlineStyle() {

    }
}

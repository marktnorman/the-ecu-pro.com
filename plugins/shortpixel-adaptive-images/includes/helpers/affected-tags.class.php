<?php
/**
 * Helps with holding the affected tags list and their classes, in order to limit the JS load.
 * User: simon
 * Date: 31.07.2020
 */

namespace ShortPixel\AI;

class AffectedTags {
    private $affectedTags;
    private $classMap;

    public function __construct() {
        $this->affectedTags = $this->getRecorded();
        $this->classMap = [];
    }

    public function get() {
        return $this->affectedTags;
    }

    public function getSelectors() {
        return $this->group($this->classMap);
    }

    public function add($tag, $type, $classes = false) {
        if(ctype_alnum($tag)) {
            $this->affectedTags[$tag] = $type | (isset($this->affectedTags[$tag]) ? $this->affectedTags[$tag] : 0);
        }
    }

    public function remove($tag) {
        unset($this->affectedTags[$tag]);
    }

    public function record() {
        $affectedTags = $this->getRecorded();
        foreach($this->affectedTags as $tag => $val) {
            if(!isset($affectedTags[$tag]) || ($affectedTags[$tag] !== $val)) {
                //this makes sure that we don't run the update if all the affected tags are already recorded.
                update_option('spai_settings_lazy_ajax_tags', $this->mergeTags($this->affectedTags, $affectedTags));
                return;
            }
        }
    }

    public function getRecorded() {
        return get_option('spai_settings_lazy_ajax_tags', array());
    }

    public function getAll() {
        return $this->mergeTags($this->affectedTags, get_option('spai_settings_lazy_ajax_tags', array()));
    }

    protected function mergeTags($tags, $moreTags) {
        foreach($tags as $key => $val) {
            $moreTags[$key] = (isset($moreTags[$key]) ? $moreTags[$key] : 0) | $val;
        }
        return $moreTags;
    }


}
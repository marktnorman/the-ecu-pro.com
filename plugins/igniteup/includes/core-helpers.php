<?php

/*
 * 
 * igniteup_head global function
 * 
 */

function igniteup_head()
{
    CSComingSoonCreator::perfomIgniteUpHead();
}

/*
 * 
 * igniteup_footer global function
 * 
 */

function igniteup_footer()
{
    CSComingSoonCreator::perfomIgniteUpFooter();
}

/*
 * 
 * igniteup_get_option global function
 * 
 */

function igniteup_get_option($key, $default = NULL)
{
    $value = CSComingSoonCreator::igniteUpGetOption($key, $default);
    return $value;
}

function render_dynamic_css($data)
{
    $string = '';
    foreach ($data as $css) {
        $string .= $css['el'] . "{";
        foreach ($css['styles'] as $prop => $value) {
            if (empty($value))
                continue;
            $property = preg_split('/(?=[A-Z])/', $prop);

            if ($prop == 'backgroundImage')
                $value = 'url(' . $value . ')';

            $string .= strtolower(implode("-", $property)) . ':' . $value . ';';
        }
        $string .= '}';
    }
    echo $string;
}

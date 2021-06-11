<form action="options.php" method="post" id="igniteup-template-options">
    <?php
    settings_fields('cscs_temp_options');
    do_settings_sections('cscs_temp_options');
    global $cscs_templates;
    $template_options = $cscs_templates[CSCS_DEFAULT_TEMPLATE];
    $sections = array('def_section' => 'General');
    if (isset($template_options['section'])) {
        $sections = $template_options['section'];
    }
    ?> 
    <div class="main-row">
        <div class="igniteup-options">
            <div class="igniteup-accordian ">
                <?php
                if (!isset($template_options)) {
                    _e('Something is wrong with your template', CSCS_TEXT_DOMAIN);
                    exit();
                }

                $section_count = 1;
                $section_array = array();
                if (isset($template_options['options']) && count($template_options['options'])) {
                    $temp_options = $template_options['options'];

                    foreach ($sections as $section_key => $section) {
                        $section_options = array();
                        foreach ($temp_options as $opkey => $opt) {
                            if (isset($opt['section']) && $section_key == $opt['section']) {
                                $section_options[$opkey] = $opt;
                            } elseif ((!isset($opt['section']) || empty($opt['section'])) && $section_count == 1) {
                                $section_options[$opkey] = $opt;
                            }
                        }
                        $section_array[$section_key] = $section_options;
                        $section_count++;
                    }

                    foreach ($section_array as $key => $section) {

                        echo '<h2>' . $sections[$key] . '</h2>';

                        echo '<div><table class="form-table">';

                        foreach ($section as $key => $field) {
                            $option_key = CSCS_TEMPLATEOPTION_PREFIX . CSCS_DEFAULT_TEMPLATE . '_' . $key;
                            $option_id = CSCS_DEFAULT_TEMPLATE . '_' . $key;
                            $def_val = isset($field['def']) ? $field['def'] : '';
                            $saved_value = get_option($option_key, $def_val);
                            ?>
                            <tr>
                                <th>
                                    <label for="<?php echo $option_id; ?>"><?php echo isset($field['label']) ? $field['label'] : __('Undefined', CSCS_TEXT_DOMAIN); ?></label>
                                </th>
                                <td>

                                    <?php
                                    switch ($field['type']) {
                                        case 'text':
                                            echo "<input type='text' class='regular-text reset-supported' id='$option_id' value='" . htmlspecialchars($saved_value, ENT_QUOTES) . "' name='$option_key' placeholder='" . (isset($field['placeholder']) ? $field['placeholder'] : '' ) . "' data-defval='" . htmlentities($def_val,ENT_QUOTES) . "'>";
                                            break;
                                        case 'email':
                                            echo "<input type='email' class='regular-text reset-supported' id='$option_id' value='$saved_value' name='$option_key' placeholder='" . (isset($field['placeholder']) ? $field['placeholder'] : '' ) . "' data-defval='" . $def_val . "'>";
                                            break;
                                        case 'color-picker':
                                            echo "<input type='text' class='cs-color-picker reset-supported' id='$option_id' value='$saved_value' name='$option_key' placeholder='" . (isset($field['placeholder']) ? $field['placeholder'] : '' ) . "' data-defval='" . $def_val . "'>";
                                            break;
                                        case 'date':
                                            echo "<input type='text' class='cs-date-picker regular-text reset-supported' id='$option_id' value='$saved_value' name='$option_key' placeholder='" . (isset($field['placeholder']) ? $field['placeholder'] : '' ) . "' data-defval='" . $def_val . "'>";
                                            break;
                                        case 'time':
                                            echo "<input type='text' class='cs-time-picker regular-text reset-supported' id='$option_id' value='$saved_value' name='$option_key' placeholder='" . (isset($field['placeholder']) ? $field['placeholder'] : '' ) . "' data-defval='" . $def_val . "'>";
                                            break;
                                        case 'checkbox':
                                            echo "<label><input type='checkbox' class='igniteup-checkbox-switch' data-size='mini' data-on-color='info' data-animate='true' class='reset-supported' id='$option_id' value='1' name='$option_key' data-defval='" . $def_val . "' " . ($saved_value == 1 ? 'checked="checked"' : '' ) . ">  " . (isset($field['label']) ? $field['label'] : 'Undefined') . "</label>";
                                            break;
                                        case 'textarea':
                                            echo "<textarea  rows='5' cols='46'  class='regular-text reset-supported' id='$option_id' name='$option_key' placeholder='" . (isset($field['placeholder']) ? $field['placeholder'] : '' ) . "' data-defval='" . htmlentities($def_val,ENT_QUOTES) . "'>$saved_value</textarea>";
                                            break;
                                        case 'image':
                                            ?>
                                            <div class="uploader">
                                                <input id="<?php echo $option_id; ?>" class="regular-text reset-supported" name="<?php echo $option_key; ?>" type="text" value="<?php echo $saved_value; ?>" data-defval='<?php echo $def_val; ?>' />
                                                <input id="<?php echo $option_id; ?>_button" class="button cscs_uploadbutton" data-input="<?php echo $option_id; ?>" type="submit" value="<?php _e('Upload', CSCS_TEXT_DOMAIN); ?>" />
                                            </div>
                                            <?php
                                            break;

                                        case 'select':
                                            ?>
                                            <select name="<?php echo $option_key; ?>" class="regular-text min-width-200 reset-supported" id="<?php echo $option_id; ?>" data-defval='<?php echo $def_val; ?>' >
                                                <?php
                                                $required_field = $field['required'];
                                                if (isset($required_field) && !$required_field)
                                                    echo "<option value='0' " . (empty($saved_value) ? 'selected="selected"' : '') . ">" . __('None', CSCS_TEXT_DOMAIN) . "</option>";
                                                foreach ($field['values'] as $key => $value) {
                                                    echo "<option " . (!empty($saved_value) && $saved_value == $key ? 'selected="selected"' : '') . " value='$key'  " . (is_array($value) && $value['disabled'] ? 'disabled="disabled"' : '') . " >" . (is_array($value) ? $value['text'] : $value) . "</option>";
                                                }
                                                ?>
                                            </select>
                                            <?php
                                            break;


                                        default:
                                    }
                                    if (isset($field['description'])) {
                                        echo "<p class='description'>" . $field['description'] . "</p>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        echo '</table></div>';
                    }
                    ?>

                </div>
                <?php
            } else {
                echo '<div class="updated"><p>';
                _e('No options defined for the active template!', CSCS_TEXT_DOMAIN);
                echo '</p></div>';
            }
            ?>

            <p>
                <a class="reset-igniteup add-new-h2" href="#"><?php _e('Reset to Defaults' ,CSCS_TEXT_DOMAIN); ?></a>        
            </p>
            <p class="submit">
                <input type="submit" name="save" class="button button-primary submit" value="<?php _e('Save Changes' ,CSCS_TEXT_DOMAIN); ?>">
                <a href="#" class="button preview-igniteup" data-forward="<?php echo esc_url(home_url("/?igniteup=force")); ?>"><?php _e('Preview', CSCS_TEXT_DOMAIN); ?></a>        
                <span id="saveResult" data-text="<?php echo htmlentities(__('Settings Saved Successfully', 'wp'), ENT_QUOTES); ?>"></span>       
            </p>    
        </div>
        <?php include 'temp-siderbar-ad.php' ?>
    </div>
</form>

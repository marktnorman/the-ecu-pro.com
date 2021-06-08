<?php
$ftp_server = '';
$ftp_user = '';
$ftp_password = '';
$ftp_port = '';
$use_ftps = '';
$use_pasv = '';
$enable_ftp_ie = '';
$ftp_server_path = '';
$pro_use_sftp = '';
$pro_use_implicit_ftp = '';
$import_from_url = '';
if (!empty($ftp_settings)) {
    $ftp_server = !empty($ftp_settings['pro_ftp_server']) ? $ftp_settings['pro_ftp_server'] : '';
    $ftp_user = !empty($ftp_settings['pro_ftp_user']) ? $ftp_settings['pro_ftp_user'] : '';
    $ftp_password = !empty($ftp_settings['pro_ftp_password']) ? $ftp_settings['pro_ftp_password'] : '';
    $ftp_port = !empty($ftp_settings['pro_ftp_port']) ? $ftp_settings['pro_ftp_port'] : 21;
    $use_ftps = !empty($ftp_settings['pro_use_ftps']) ? $ftp_settings['pro_use_ftps'] : '';
    $use_pasv = !empty($ftp_settings['pro_use_pasv']) ? $ftp_settings['pro_use_pasv'] : '';
    $pro_use_sftp = !empty($ftp_settings['pro_use_sftp']) ? $ftp_settings['pro_use_sftp'] : '';
    $enable_ftp_ie = !empty($ftp_settings['pro_enable_ftp_ie']) ? $ftp_settings['pro_enable_ftp_ie'] : '';
    $ftp_server_path = !empty($ftp_settings['pro_ftp_server_path']) ? $ftp_settings['pro_ftp_server_path'] : '';
    $pro_use_implicit_ftp = !empty($ftp_settings['pro_use_implicit_ftp']) ? $ftp_settings['pro_use_implicit_ftp'] : '';
}
wp_enqueue_script('woocommerce-prod-piep-test-ftp', plugins_url(basename(plugin_dir_path(WF_ProdImpExpCsv_FILE)) . '/js/piep_test_ftp_connection.js', basename(__FILE__)),array(),WT_PROD_IMP_EXP_VER);
$xa_prod_piep_ftp = array('admin_ajax_url' => admin_url('admin-ajax.php'),'wt_nonce'=> wp_create_nonce(WF_PROD_IMP_EXP_ID));
wp_localize_script('woocommerce-prod-piep-test-ftp', 'xa_prod_piep_test_ftp', $xa_prod_piep_ftp);
/*if (class_exists('class_wf_sftp_import_export') && $pro_use_sftp == 1) {
    $ftp_text= 'sFTP';
} else {
    $ftp_text= 'FTP';
}*/
$ftp_text= 'FTP';

?>

<div class="woocommerce">
    
    <?php 
    $tab = 'import';
    include_once WT_PIPE_BASE_PATH.'includes/views/html-wt-common-header.php'; ?>
    <ul class="subsubsub" style="margin-left: 15px;">
        <li><a href="<?php echo admin_url('admin.php?page=wf_woocommerce_csv_im_ex') ?>" class=""><?php _e('Export', 'wf_csv_import_export'); ?></a> | </li>
        <li><a href="<?php echo admin_url('admin.php?import=wt_woocommerce_product_csv') ?>" class="current"><?php _e('Import', 'wf_csv_import_export'); ?></a> </li>
    </ul>
    <br/>    
    <div class="wf-import-greeting tool-box bg-white p-20p">
        <h3 class="title aw-title"><?php _e('Import Products in CSV/XML Format', 'wf_csv_import_export'); ?></h3>
        <p><?php _e('Import products in CSV/XML format ( works for simple, grouped, external and variable products)  from different sources (  from your computer OR from another server via FTP. )', 'wf_csv_import_export'); ?></p>
        <p><?php //_e('You can import products (in CSV format) in to the shop using any of below methods.', 'wf_csv_import_export');  ?></p>

        <?php if (!empty($upload_dir['error'])) : ?>
            <div class="error"><p><?php _e('Before you can upload your import file, you will need to fix the following error:', 'wf_csv_import_export'); ?></p>
                <p><strong><?php echo $upload_dir['error']; ?></strong></p>
            </div>
        <?php else : ?>
            <div  class="">
                <form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr(wp_nonce_url($action, 'import-upload')); ?>">
                    <table class="form-table">
                        <tbody>
                            <tr style="">
                                <th style="">
                                    <label for="upload"><?php _e('Method 1: Select a file from your computer', 'wf_csv_import_export'); ?></label>
                                </th>
                                <td style="">
                                    <input type="file" id="upload" name="import" size="25" />
                                    <input type="hidden" name="action" value="save" />
                                    <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
                                    <small><?php printf(__('Maximum size: %s'), $size); ?></small>
                                </td>
                            </tr>

                            <tr  style="">
                                <th style="">
                                    <label for="ftp"><?php echo sprintf(__('Method 2: Provide %s details', 'wf_csv_import_export'),$ftp_text ); ?></label>
                                </th>
                                <th>
                                    <input type="checkbox" name="pro_enable_ftp_ie" id="pro_enable_ftp_ie" class="checkbox" <?php checked($enable_ftp_ie, 1); ?> />
                                    <p style="font-size: 12px"><?php echo sprintf(__('Check to enable %s import', 'wf_csv_import_export'),$ftp_text ); ?></p> 
                                </th>
                            </tr>  
                            <tr id="pro_export_section_all">
                                <td></td>
                                <td>
       
                                    <table class="form-table">                                                                              
                                        <tr>
                                            <th>
                                                <label for="pro_ftp_server"><?php echo sprintf(__('%s Server Host/IP', 'wf_csv_import_export'),$ftp_text ); ?></label>
                                            </th>
                                            <td>
                                                <input type="text" name="pro_ftp_server" id="pro_ftp_server" placeholder="<?php _e('XXX.XXX.XXX.XXX', 'wf_csv_import_export'); ?>" value="<?php echo $ftp_server; ?>" class="input-text" />
                                                <p style="font-size: 12px"><?php echo sprintf(__('Enter your %s server hostname', 'wf_csv_import_export'),$ftp_text ); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="pro_ftp_user"><?php echo sprintf(__('%s User Name', 'wf_csv_import_export'),$ftp_text ); ?></label>
                                            </th>
                                            <td>
                                                <input type="text" name="pro_ftp_user" id="pro_ftp_user" placeholder="" value="<?php echo $ftp_user; ?>" class="input-text" />
                                                <p style="font-size: 12px"><?php echo sprintf(__('Enter your %s username', 'wf_csv_import_export'),$ftp_text ); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="pro_ftp_password"><?php echo sprintf(__('%s Password', 'wf_csv_import_export'),$ftp_text ); ?></label>
                                            </th>
                                            <td>
                                                <input type="password" name="pro_ftp_password" id="pro_ftp_password" placeholder="" value="<?php echo $ftp_password; ?>" class="input-text" />
                                                <p style="font-size: 12px"><?php echo sprintf(__('Enter your %s password', 'wf_csv_import_export'),$ftp_text ); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="pro_ftp_port"><?php echo sprintf(__('%s Port', 'wf_csv_import_export'),$ftp_text ); ?></label>
                                            </th>
                                            <td>
                                                <input type="text" name="pro_ftp_port" id="pro_ftp_port" placeholder="21 (default) " value="<?php echo $ftp_port; ?>" class="input-text" />
                                                <p style="font-size: 12px"><?php _e('Enter your port number', 'wf_csv_import_export'); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="pro_ftp_server_path"><?php echo sprintf(__('%s Server Path', 'wf_csv_import_export'),$ftp_text ); ?></label>
                                            </th>
                                            <td>
                                                <input type="text" name="pro_ftp_server_path" id="pro_ftp_server_path" placeholder="" value="<?php echo $ftp_server_path; ?>" class="input-text" />
                                                <p style="font-size: 12px"><?php echo sprintf(__('Enter the %s server path', 'wf_csv_import_export'),$ftp_text ); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="use_ftps"><?php _e('Use FTPS', 'wf_csv_import_export'); ?></label>
                                            </th>
                                            <td>
                                                <input type="checkbox" name="pro_use_ftps" id="pro_use_ftps" class="checkbox" <?php checked($use_ftps, 1); ?> />
                                                <p style="font-size: 12px"><?php _e('Enable this to send data over a network with SSL encryption', 'wf_csv_import_export'); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="pro_use_pasv"><?php _e('Enable Passive mode', 'wf_csv_import_export'); ?></label>
                                            </th>
                                            <td>
                                                <input type="checkbox" name="pro_use_pasv" id="pro_use_pasv" class="checkbox" <?php checked($use_pasv, 1); ?> />
                                                <p style="font-size: 12px"><?php _e('Enable this to turns passive mode on or off', 'wf_csv_import_export'); ?></p>

                                            </td> 
                                        </tr>
                                            <tr>
                                                <th>
                                                    <label for="pro_use_sftp"><?php _e('Is sFTP ?', 'wf_csv_import_export'); ?></label>
                                                </th>
                                                <td>
                                                    <input type="checkbox" name="pro_use_sftp" id="pro_use_sftp" class="checkbox" <?php checked($pro_use_sftp, 1); ?> />
                                                    <!--<p style="font-size: 12px"><?php _e('Enable this to turns passive mode on or off', 'wf_csv_import_export'); ?></p>-->
                                                </td>
                                            </tr>
                                             <tr>
                                                <th>
                                                    <label for="pro_use_implicit_ftp"><?php _e('implicit ftp over tls ?', 'wf_csv_import_export'); ?></label>
                                                </th>
                                                <td>
                                                    <input type="checkbox" name="pro_use_implicit_ftp" id="pro_use_implicit_ftp" class="checkbox" <?php checked($pro_use_implicit_ftp, 1); ?> />
  
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                            <th>
                                                <input type="button" id="prod_test_ftp_connection" class="button button-primary" value="<?php echo sprintf(__('Test %s', 'wf_csv_import_export'),$ftp_text ); ?>" />
                                                <span class ="spinner " ></span>
                                            </th>
                                            <td id="prod_ftp_test_notice"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr  style="">
                                <th style="">
                                    <label for="import_from_url"><?php _e('Method 3: Enter the URL of the file', 'wf_csv_import_export'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="import_from_url" name="import_from_url" placeholder="" value="<?php echo $import_from_url; ?>" class="input-text" style="width: 50%" />
                                    <p style="font-size: 12px"><?php _e('Provide the URL of the CSV where it can be downloaded from', 'wf_csv_import_export'); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="">
                        <h3 class="title aw-title"><?php _e('Available Options', 'wf_csv_import_export'); ?></h3>
                        <table class="form-table">
                            <tbody>
                                <?php
                                $mapping_from_db = get_option('wf_prod_csv_imp_exp_mapping');
                                if (!empty($mapping_from_db)) {
                                    ?>
                                    <tr>
                                        <th>
                                            <label for="profile"><?php _e('Mapping Profile', 'wf_csv_import_export'); ?></label>
                                        </th>
                                        <td>
                                            <select name="profile">
                                                <option value="">--Select--</option>
                                                <?php foreach ($mapping_from_db as $key => $value) { ?>
                                                    <option value="<?php echo $key; ?>"><?php echo $key; ?></option>

                                                <?php } ?>
                                            </select>
                                            <p style="font-size: 13px; font-style: italic;"><?php _e('Select the previously saved mapping profile', 'wf_csv_import_export'); ?></p>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <tr>
                                    <th><label><?php _e('Update products if exists', 'wf_csv_import_export'); ?></label><br/></th>
                                    <td>
                                        <input type="checkbox" name="merge" id="merge">
                                        <span class="description"><?php _e('Existing products are identified by their SKUs/IDs. If this option is not selected and if a product with same ID/SKU is found in the CSV, that product will not be imported.', 'wf_csv_import_export'); ?></span>
                                    </td>

                                </tr>
                                <tr>
                                    <th><label><?php _e('Skip new products', 'wf_csv_import_export'); ?></label><br/></th>
                                    <td>
                                        <input type="checkbox" name="skip_new" id="skip_new">
                                        <span class="description"><?php _e('While updating existing products, enable this to skip products which are not already present in the store', 'wf_csv_import_export'); ?></span>
                                    </td>
                            
                                </tr>

                                <tr>
                                    <th><label><?php _e('Delimiter', 'wf_csv_import_export'); ?></label><br/></th>
                                     <td> 
                                        <input type="radio" id="wt_prod_delimiter_comma" name="delimiter" value="," checked >
                                        <label for="wt_prod_delimiter_comma">Comma</label>&nbsp;&nbsp;&nbsp;
                                        <input type="radio" id="wt_prod_delimiter_tab" name="delimiter" value="tab" >
                                        <label for="wt_prod_delimiter_tab">Tab</label>&nbsp;&nbsp;&nbsp;
                                        <input type="radio" id="wt_prod_delimiter_semi" name="delimiter" value=";" >
                                        <label for="wt_prod_delimiter_semi">Semicolon</label>&nbsp;&nbsp;&nbsp;
                                        <input type="radio" id="wt_prod_delimiter_space" name="delimiter" value="space" >
                                        <label for="wt_prod_delimiter_space">Space</label>&nbsp;&nbsp;&nbsp;
                                        <input type="radio" id="wt_prod_delimiter_other" name="delimiter" value="" >
                                        <label for="wt_prod_delimiter_other">Other</label>&nbsp;&nbsp;&nbsp;
                                         <input type="text" id="wt_prod_delimiter_other_txt" placeholder="," name="delimitertxt"  maxlength="1" size="2" />
                                       <span class="description"><?php _e('<br />Select a delimiter to separate the field values with. ‘,’ is the default delimiter', 'wf_csv_import_export'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label><?php _e('Merge empty cells', 'wf_csv_import_export'); ?></label><br/></th>
                                    <td>
                                        <input type="checkbox" name="merge_empty_cells" />
                                        <span class="description"><?php _e('Check to merge the empty cells in CSV, otherwise empty cells will be ignored', 'wf_csv_import_export'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label><?php _e('Delete existing products', 'wf_csv_import_export'); ?></label>
                                        <br/>
                                    </th>
                                    <td>
                                        <input type="checkbox" name="delete_products" />
                                        <span class="description"><?php _e('Check to <b style="color:red"> delete existing products </b>  that are not present in the CSV', 'wf_csv_import_export') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label><?php _e('Use SKU to link up-sells and cross-sells', 'wf_csv_import_export'); ?></label><br/>
                                    </th>
                                    <td>
                                        <input type="checkbox" name="use_sku_upsell_crosssell" id="use_sku_upsell_crosssell" />
                                        <span class="description"><?php _e('Check to import up-sells and cross-sells using the product SKU', 'wf_csv_import_export'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label><?php _e('Disable thumbnail generation', 'wf_csv_import_export'); ?></label><br/>
                                    </th>
                                    <td>
                                        <input type="checkbox" name="pro_stop_thumbnail_regen" id="pro_stop_thumbnail_regen" />
                                        <span class="description"><?php _e('Check this box to avoid the generation of thumbnails on import', 'wf_csv_import_export'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label><?php _e('New product status', 'wf_csv_import_export'); ?></label><br/>
                                    </th>
                                    <td>
                                        <input type="text" name="new_prod_status" id="new_prod_status" placeholder="draft / pending / publish "/>
                                        <span class="description"><?php _e('<br /> Change the product status (draft/pending/published) of all the new products imported overriding the existing status', 'wf_csv_import_export'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label><?php _e('Use SKU to link grouped products', 'wf_csv_import_export'); ?></label><br/>
                                    </th>
                                    <td>
                                        <input type="checkbox" name="prod_use_chidren_sku" id="prod_use_chidren_sku" />
                                        <span class="description"><?php _e(' To import grouped products based on SKU, enable this option. By default they are linked using product ID', 'wf_csv_import_export'); ?></span>
                                    </td>
                                </tr>


                            </tbody>
                        </table>
                    </div>
                    <p class="submit">
                        <input type="submit" class="button button-primary" id ="wt_import_greet" value="<?php _e('Upload file and import', 'wf_csv_import_export'); ?>" />
                    </p>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    jQuery("#wt_prod_delimiter_other").click(function() {
        jQuery("#wt_prod_delimiter_other_txt").focus();
     });

    jQuery("#wt_prod_delimiter_other_txt").focus(function() {
        jQuery("#wt_prod_delimiter_other").prop("checked", true);
        
    });
    
    jQuery("#wt_import_greet").click(function () {
        var delimiter = jQuery("#wt_prod_delimiter_other_txt").val();
        jQuery('#wt_prod_delimiter_other').val(delimiter);
        });
    
</script>
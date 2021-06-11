<?php

function createTemplateThumb($key, $template, $active = FALSE) {
    $screenshot = @plugin_dir_url(CSCS_FILE) . "includes/templates/" . $template['folder_name'] . "/screenshot.jpg";
    if (isset($template['plugin_file']) && !empty($template['plugin_file']))
        $screenshot = plugin_dir_url($template['plugin_file']) . "template/screenshot.jpg";
    ?>
    <div class="template-box">
        <div class="template-title"><?php echo $template['name'], $active ? ' (' . __('Active', CSCS_TEXT_DOMAIN) . ')' : ''; ?></div>
        <img src="<?php echo $screenshot; ?>">
        <div class="template-footer">

            <?php if (CSCS_DEFAULT_TEMPLATE == $key): ?>
                <a href="<?php echo admin_url("admin.php?page=cscs_options&section=appearance"); ?>">
                    <button class="button button-primary button-customize"><?php _e('Customize', CSCS_TEXT_DOMAIN); ?></button>
                </a>
            <?php else: ?>
                <form method="post">
                    <input type="hidden" name="activate_template" value="<?php echo $key; ?>">
                    <button class="button button-activate"><?php _e('Activate', CSCS_TEXT_DOMAIN); ?></button>
                </form>
            <?php endif; ?>

            <?php if (CSCS_DEFAULT_TEMPLATE != $key && !in_array($key, CSComingSoonCreator::getDefaultTemplateList())): ?>
                <a href="<?php echo admin_url('plugins.php'); ?>#igniteup" class="button button-delete"><?php _e('Uninstall', CSCS_TEXT_DOMAIN) ?></a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>

<div class="wrap">
    <h1><?php _e('Templates', CSCS_TEXT_DOMAIN); ?> <a href="https://getigniteup.com/templates/?utm_source=templates_page&utm_medium=temp_add_btn&utm_campaign=plugin" target="_blank" class="page-title-action"><?php _e('Add new', CSCS_TEXT_DOMAIN); ?></a></h1>
    <?php if (isset($_REQUEST['activated']) && $_REQUEST['activated'] == 'yes'): ?>
        <div class="updated notice is-dismissible below-h2"><p><?php _e('A new template activated!', CSCS_TEXT_DOMAIN); ?> <a href="<?php echo admin_url("admin.php?page=cscs_options&section=appearance"); ?>"><?php _e('Customize it', CSCS_TEXT_DOMAIN); ?></a></p></div>
    <?php endif; ?>
    <div id="cscs-templates">
        <?php
        $templates = CSAdminOptions::getTemplates();
        createTemplateThumb(CSCS_DEFAULT_TEMPLATE, $templates[CSCS_DEFAULT_TEMPLATE], TRUE);
        foreach ($templates as $key => $template) {
            if ($key !== CSCS_DEFAULT_TEMPLATE)
                createTemplateThumb($key, $template);
        }
        ?>
        <div class="template-box get-template-box">
            <a href="https://getigniteup.com/templates/?utm_source=templates_page&utm_medium=temp_box&utm_campaign=plugin" target="_blank">
                <img src="<?php echo plugin_dir_url(CSCS_FILE) . 'includes/images/def-screenshot.png'; ?>"> 
                <div id="hover_wrapper"><img src="<?php echo plugin_dir_url(CSCS_FILE) . "includes/images/def-screenshot-hover.png"; ?>"></div>
            </a>
            <div class="get-more"><?php _e('Get More Templates', CSCS_TEXT_DOMAIN); ?></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

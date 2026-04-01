<?php
add_action('admin_menu', 'amflp_add_settings_page');
add_action('admin_init', 'amflp_register_settings');

function amflp_add_settings_page() {
    add_options_page(
        'AMFLP Plugin Settings',
        'AMFLP Settings',
        'manage_options',
        'amflp-settings',
        'amflp_render_settings_page'
    );
}

function amflp_render_settings_page() {
    $universal_content = get_option('amf_universal_content');
    ?>
    <div class="wrap">
        <h1>AMFLP Plugin Settings</h1>

        <form method="post" action="options.php">
            <?php settings_fields('amfence-options'); ?>
            
            <label for="amflp-universal-content">Universal Content</label>
            <?php wp_editor($universal_content, 'amf_universal_content', array('textarea_rows' => 5)); ?>

            
            <?php submit_button('Save Content'); ?>
        </form>

        <h2>CSV Import</h2>
        <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="amflp_import_csv">
            <input type="file" name="amflp_csv_file" accept=".csv">
            <?php wp_nonce_field('amflp_import_csv', 'amflp_import_csv_nonce'); ?>
            <?php submit_button('Import CSV'); ?>
        </form>
    </div>
    <?php
}

function amflp_register_settings() {
    register_setting('amfence-options', 'amf_universal_content');
    
    add_settings_section('amfence-universal-content-section', 'Universal Content', 'amflp_render_universal_content_section', 'amflp-settings');
    add_settings_field('amflp-universal-content', 'Content', 'amflp_render_content_field', 'amflp-settings', 'amfence-universal-content-section');
}

function amflp_render_universal_content_section() {
    echo '<h2>Universal Content</h2>';
}

function amflp_render_content_field() {
    $option = get_option('amflp_universal_content');
    wp_editor($option, 'amflp_universal_content', array('textarea_rows' => 5));
}
?>

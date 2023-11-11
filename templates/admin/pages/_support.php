<?php

if (!defined('ABSPATH')) {
    exit;
}

// global $wpdb;

// $sqlQuery = "SELECT pm.*, p.post_type
//     FROM {$wpdb->postmeta} pm
//     INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
//     WHERE 1=1
//     AND pm.meta_key = '_wp_old_slug'
//     AND p.post_type= 'attachment'
//     AND (p.post_mime_type = 'image/jpeg' OR p.post_mime_type = 'image/gif' OR p.post_mime_type = 'image/jpg' OR p.post_mime_type = 'image/png')
//     ";

$backupAttachmentUrl = wp_nonce_url(add_query_arg(
    [
        'action' => 'imageseo_backup_attachment',
    ],
    admin_url('admin-post.php')
), 'imageseo_backup_attachment');

$data = imageseo_get_service('Htaccess')->generate();

?>

<div id="wrap-imageseo">
    <div class="wrap">
        <h1 class="imageseo__page-title"><?php esc_html_e('Image SEO Support', 'imageseo'); ?></h1>
        <p>This page is intended for SEO image media.</p>
        <p>It is advised to use it only with a member of the ImageSEO support team. If needed: <a href="mailto:support@imageseo.io">
        support@imageseo.io</a></p>

        <h2>Backup an attachment ID</h2>
        <form method="post" id="mainform" action="<?php echo esc_url($backupAttachmentUrl); ?>">
            <input type="text" name="attachmentId" value="">
            <?php submit_button(); ?>
        </form>
        <h2>View Redirect Image SEO </h2>
        <pre>
            <?php echo wp_kses_post( $data ); ?>
        </pre>
        <h2>Reset Htaccess ImageSEO</h2>
        <form method="post" id="mainform" action="<?php echo esc_url($resetHtaccess); ?>">
            <?php submit_button(); ?>
        </form>
    </div>
</div>

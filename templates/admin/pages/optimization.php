<?php

if (! defined('ABSPATH')) {
    exit;
}

use SeoImageWP\Helpers\AttachmentMeta;

$attachmentsAlreadyReport = get_posts(array(
    'post_type' => 'attachment',
    'posts_per_page' => -1,
    'meta_key' => AttachmentMeta::REPORT,
    'meta_compare' => 'EXISTS'
));

$attachments = get_posts(array(
    'post_type' => 'attachment',
    'posts_per_page' => -1
));


$total = count($attachments);
$totalAlreadyReport = count($attachmentsAlreadyReport);
?>

<div id="wrap-seoimage">
    <div class="wrap">
        <h3><?php esc_html_e('SeoImage - Bulk Optimization', 'seoimage'); ?></h3>
        <hr />
        <p>
            <strong><?php esc_html_e('Total attachment(s) : ', 'seoimage'); ?></strong> <?php echo $total; ?>
        </p>
        <p>
            <strong><?php esc_html_e('Total report(s) : ', 'seoimage'); ?></strong> <?php echo $totalAlreadyReport; ?>
        </p>
        <button class="button button-primary button-hero">
            <?php esc_html_e('Regenerate all reports', 'seoimage'); ?>
        </button>
    </div>
</div>


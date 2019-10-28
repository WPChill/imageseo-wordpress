<?php


if (! defined('ABSPATH')) {
    exit;
}

$alreadyReport = $this->reportImageService->haveAlreadyReportByAttachmentId($post->ID);


if (!$alreadyReport) {
    ?>
    <a id="imageseo-<?php echo $post->ID; ?>" href="<?php echo esc_url(admin_url('admin-post.php?action=imageseo_report_attachment&attachment_id=' . $post->ID)); ?>" class="button-primary">
        <?php echo __('Analyze', 'imageseo'); ?>
    </a>
    <?php
    return;
}
$report = $this->reportImageService->getReportByAttachmentId($post->ID);

?>
<h3><?php esc_html_e('List of keywords representative of the context in which Google analyzes your image.', 'imageseo');
 ?></h3>
<i><?php esc_html_e('The keywords are currently only available in English. We will then offer you translations in several languages.', 'imageseo'); ?></i>
<div class="imageseo-table imageseo-table--context">
    <?php
    foreach ($report['alts'] as $alt) {
        if (empty($alt['name'])) {
            continue;
        } ?>
        <div class="imageseo-table-item">
            <div class="imageseo-table-item-meta imageseo-table-item-meta--alt">
                <?php echo $alt['name']; ?>
            </div>
            <div class="imageseo-table-item-meta imageseo-table-item-meta--score">
                <?php echo round($alt['score']); ?>%
            </div>
        </div>
    <?php
    }
    ?>
</div>
<hr />
<h3><?php esc_html_e('Keyword proposals corresponding to the analysis of the image content.', 'imageseo');
 ?></h3>
<i><?php esc_html_e('The keywords are currently only available in English. We will then offer you translations in several languages.', 'imageseo'); ?></i>
<div class="imageseo-table imageseo-table--context">
    <?php
    foreach ($report['labels'] as $alt) {
        if (empty($alt['name'])) {
            continue;
        } ?>
        <div class="imageseo-table-item">
            <div class="imageseo-table-item-meta imageseo-table-item-meta--alt">
                <?php echo $alt['name']; ?>
            </div>
            <div class="imageseo-table-item-meta imageseo-table-item-meta--score">
                <?php echo round($alt['score']); ?>%
            </div>
        </div>
    <?php
    }
    ?>
</div>


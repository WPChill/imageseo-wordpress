<?php


if (! defined('ABSPATH')) {
    exit;
}

$alreadyReport = $this->reportImageServices->haveAlreadyReportByAttachmentId($post->ID);


if (!$alreadyReport) {
    ?>
    <a id="seoimage-<?php echo $attachmentId; ?>" href="<?php echo esc_url(admin_url('admin-post.php?action=seoimage_report_attachment&attachment_id=' . $attachmentId)); ?>" class="button-primary">
        <?php echo __('Analyze', 'seoimage'); ?>
    </a>
    <?php
    return;
}
$report = $this->reportImageServices->getReportByAttachmentId($post->ID);

?>
<h3><?php esc_html_e('List of keywords representative of the context in which Google analyzes your image.', 'seoimage');
 ?></h3>
<i><?php esc_html_e('The keywords are currently only available in English. We will then offer you translations in several languages.', 'seoimage'); ?></i>
<div class="seoimage-table seoimage-table--context">
    <?php
    foreach ($report['alts'] as $alt) {
        if (empty($alt['name'])) {
            continue;
        } ?>
        <div class="seoimage-table-item">
            <div class="seoimage-table-item-meta seoimage-table-item-meta--alt">
                <?php echo $alt['name']; ?>
            </div>
            <div class="seoimage-table-item-meta seoimage-table-item-meta--score">
                <?php echo round($alt['score']); ?>%
            </div>
        </div>
    <?php
    }
    ?>
</div>
<hr />
<h3><?php esc_html_e('Keyword proposals corresponding to the analysis of the image content.', 'seoimage');
 ?></h3>
<i><?php esc_html_e('The keywords are currently only available in English. We will then offer you translations in several languages.', 'seoimage'); ?></i>
<div class="seoimage-table seoimage-table--context">
    <?php
    foreach ($report['labels'] as $alt) {
        if (empty($alt['name'])) {
            continue;
        } ?>
        <div class="seoimage-table-item">
            <div class="seoimage-table-item-meta seoimage-table-item-meta--alt">
                <?php echo $alt['name']; ?>
            </div>
            <div class="seoimage-table-item-meta seoimage-table-item-meta--score">
                <?php echo round($alt['score']); ?>%
            </div>
        </div>
    <?php
    }
    ?>
</div>


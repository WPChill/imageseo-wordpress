<?php

namespace ImageSeoWP\Actions\Front;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\ServerSoftware;

class RewriteRuleFilename
{
	public $generateFilename;
	
    public function __construct()
    {
        $this->generateFilename = imageseo_get_service('GenerateFilename');
    }

    /**
     * @deprecated 2.0.0
     *
     * @return void
     */
    public function hooks()
    {
        // add_action('init', [$this, 'rewriteRule']);
        // add_filter('query_vars', [$this, 'addQueryVars']);
        // add_action('template_redirect', [$this, 'redirectMediaFile']);
    }

    public function rewriteRule()
    {
        $isApache = apply_filters('imageseo_type_server_rewrite_rule', ServerSoftware::isApache());

        if ($isApache) {
            //APACHE
            add_rewrite_rule(
                '^medias/images/([^/]+)(.jpg|.jpeg|.png|.gif|.webp)$',
                'index.php?attachment_filename=$matches[1]&extension=$matches[2]',
                'top'
            );
        } else {
            // NGINX
            add_rewrite_rule(
                '^medias/images/([^/]+)$',
                'index.php?attachment_filename=$matches[1]',
                'top'
            );
        }

        if (false === get_option('_imageseo_flush_rewrite_rules')) {
            update_option('_imageseo_flush_rewrite_rules', 1);
            flush_rewrite_rules();
        }
    }

    public function addQueryVars($queryVars)
    {
        $queryVars[] = 'attachment_filename';
        $queryVars[] = 'extension';

        return $queryVars;
    }

    public function scaledImagePath($attachment_id, $size = 'thumbnail')
    {
        $file = get_attached_file($attachment_id, true);
        if (empty($size) || 'full' === $size) {
            // for the original size get_attached_file is fine
            return realpath($file);
        }
        if (!wp_attachment_is_image($attachment_id)) {
            return false; // the id is not referring to a media
        }
        $info = image_get_intermediate_size($attachment_id, $size);
        if (!is_array($info) || !isset($info['file'])) {
            return false; // probably a bad size argument
        }

        return realpath(str_replace(wp_basename($file), $info['file'], $file));
    }

    public function renderImage($path, $mimeType)
    {
        if (!file_exists($path)) {
            wp_redirect(site_url());
            exit;
        }
        $maxAge = apply_filters('imageseo_rename_file_max_age', 86400);
        $lastModifiedTime = filemtime($path);
        $etag = md5_file($path);

        header('Pragma: public');
        header('Cache-Control: max-age=' . $maxAge);
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + $maxAge));
        header(sprintf('Content-type: %s', $mimeType));
        header('Content-Length: ' . filesize($path));
        header('Accept-Ranges: bytes');

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModifiedTime) . ' GMT');
        header(sprintf('Etag: %s', $etag));

        readfile($path);
        exit;
    }

    public function redirectMediaFile()
    {
        if (!get_query_var('attachment_filename')) {
            return;
        }

        $links = get_option('imageseo_link_rename_files');
        if (empty($links)) {
            $links = [];
        }

        $filename = get_query_var('attachment_filename');

        if (defined('IMAGIFY_SLUG')) {
            $extension = get_query_var('extension');
            if ('.webp' === $extension) {
                $splitFilename = explode('.', $filename);
                $extensionSplit = array_pop($splitFilename);
                if (in_array($extensionSplit, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $filename = implode('.', $splitFilename);
                }
            }
        }

        if (isset($links[$filename])) {
            $this->renderImage($links[$filename]['path'], $links[$filename]['content-type']);
            exit;
        }

        $attachment = $this->generateFilename->getAttachmentIdByFilenameImageSeo($filename);

        if (!$attachment) {
            wp_redirect(home_url());

            return;
        }

        $data = $this->generateFilename->getFilenameDataImageSEOWithAttachmentId($attachment->ID, $filename);

        if (!isset($data['size'])) {
            header(sprintf('Content-type: %s', $attachment->post_mime_type));
            readfile($attachment->guid);
            exit;
        }

        $attachmentPath = $this->scaledImagePath($attachment->ID, $data['size']);

        if (!$attachmentPath || !file_exists($attachmentPath)) {
            header(sprintf('Content-type: %s', $attachment->post_mime_type));
            readfile($attachment->guid);
            exit;
        }

        $links[$filename] = [
            'content-type' => $attachment->post_mime_type,
            'path'         => $attachmentPath,
        ];

        $limitCacheLinks = apply_filters('imageseo_limit_cache_links', 100);

        if (count($links) < $limitCacheLinks) {
            update_option('imageseo_link_rename_files', $links);
        }

        $this->renderImage($attachmentPath, $attachment->post_mime_type);
        exit;
    }
}

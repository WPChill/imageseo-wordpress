<?php

namespace ImageSeoWP\Actions\Front;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\TypeContent;
use ImageSeoWP\Helpers\ServerSoftware;

class RenameFileContent
{
    public function __construct()
    {
        $this->renameFileService = imageseo_get_service('RenameFile');
    }

    /**
     * @return bool
     */
    protected function no_translate_action_ajax()
    {
        $action_ajax_no_translate = apply_filters('imageseo_ajax_no_translate', [
            'add-menu-item', // WP Core
            'query-attachments', // WP Core
            'avia_ajax_switch_menu_walker', // Enfold theme
            'query-themes', // WP Core
            'wpestate_ajax_check_booking_valability_internal', // WP Estate theme
            'wpestate_ajax_add_booking', // WP Estate theme
            'wpestate_ajax_check_booking_valability', // WP Estate theme
            'mailster_get_template', // Mailster Pro,
            'mmp_map_settings', // MMP Map,
            'elementor_ajax', // Elementor since 2.5
            'ct_get_svg_icon_sets', // Oxygen
            'oxy_render_nav_menu', // Oxygen
            'hotel_booking_ajax_add_to_cart', // Hotel booking plugin
            'imagify_get_admin_bar_profile', // Imagify Admin Bar
        ]);

        if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['action']) && in_array($_POST['action'], $action_ajax_no_translate)) { //phpcs:ignore
            return true;
        }

        if ('GET' === $_SERVER['REQUEST_METHOD'] && isset($_GET['action']) && in_array($_GET['action'], $action_ajax_no_translate)) { //phpcs:ignore
            return true;
        }

        return false;
    }

    public function hooks()
    {
        if (is_admin() &&
        (!wp_doing_ajax() || (wp_doing_ajax() && isset($_SERVER['HTTP_REFERER']) && false !== strpos($_SERVER['HTTP_REFERER'], 'wp-admin')))) {
            return;
        }
        if (is_admin() && (!wp_doing_ajax() || $this->no_translate_action_ajax())) {
            return;
        }

        add_action('init', [$this, 'updateContentFile'], 12);
    }

    public function updateContentFile()
    {
        if (!apply_filters('imageseo_frontend_active_rename_file', true)) {
            return;
        }

        $file = apply_filters('imageseo_debug_file', IMAGESEO_DIR . '/content.html');

        if (defined('IMAGESEO_DEBUG') && IMAGESEO_DEBUG && file_exists($file)) {
            echo $this->update(file_get_contents($file));
            die;
        } else {
            ob_start([$this, 'update']);
        }
    }

    public function getAttachmentIdByUrl($url)
    {
        $dir = wp_upload_dir();

        // baseurl never has a trailing slash
        if (false === strpos($url, $dir['baseurl'] . '/')) {
            // URL points to a place outside of upload directory
            return false;
        }

        $file = basename($url);

        $query = [
            'post_type'  => 'attachment',
            'fields'     => 'ids',
            'meta_query' => [
                [
                    'key'     => '_wp_attached_file',
                    'value'   => $file,
                    'compare' => 'LIKE',
                ],
            ],
        ];

        // query attachments
        $ids = get_posts($query);

        if (!empty($ids)) {
            foreach ($ids as $id) {
                // first entry of returned array is the URL
                if ($url === array_shift(wp_get_attachment_image_src($id, 'full'))) {
                    return $id;
                }
            }
        }

        $query['meta_query'][0]['key'] = '_wp_attachment_metadata';
        // query attachments again
        $ids = get_posts($query);

        if (empty($ids)) {
            return false;
        }

        foreach ($ids as $id) {
            $meta = wp_get_attachment_metadata($id);

            foreach ($meta['sizes'] as $size => $values) {
                if ($values['file'] === $file && $url === array_shift(wp_get_attachment_image_src($id, $size))) {
                    return $id;
                }
            }
        }

        return false;
    }

    public function update($content)
    {
        $type = TypeContent::isJson($content) ? 'json' : 'html';

        if ('json' === $type) {
            return $content;
        }

        $regex = '#<img[^>]* src=(?:\"|\')(?<src>([^"]*))(?:\"|\')[^>]*>#mU';

        preg_match_all($regex, $content, $matches);

		$matchesSrc = array_unique($matches['src']);

		$isNginx = apply_filters('imageseo_get_link_file_is_nginx', ServerSoftware::isNginx());

        foreach ($matchesSrc as $src) {
            if (false === strpos($src, 'wp-content/uploads')) {
                continue;
            }

            $attachmentId = $this->getAttachmentIdByUrl($src);

            if (!$attachmentId) {
                continue;
            }

            $filenames = $this->renameFileService->getAllFilenamesByImageSEO($attachmentId);
            if (empty($filenames)) {
                continue;
            }

            $metadata = wp_get_attachment_metadata($attachmentId);

            foreach ($filenames as $filename) {
                $srcBySize = wp_get_attachment_image_src($attachmentId, $filename['size']);
				$fullFilename = sprintf('%s.%s', $filename['filename_without_extension'], $filename['extension']);


				$newFilename = $this->renameFileService->getLinkFileImageSEO($fullFilename);

				$content = str_replace($srcBySize[0], $newFilename, $content);

				if ($isNginx) {
					$content = str_replace($newFilename . '.webp', $newFilename, $content);
				}

            }
		}

        return $content;
    }
}

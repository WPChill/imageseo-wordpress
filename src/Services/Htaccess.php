<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\CleanUrl;

class Htaccess
{
    const INSERT_REGEX = '@\n?# Created by ImageSEO(?:.*?)# End of ImageSEO\n?@sm';

    public function __construct()
    {
        $this->filename = ABSPATH . '/.htaccess';
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return is_writable($this->filename);
    }

    /**
     * Generate content for redirection htaccess.
     */
    public function generate()
    {
        $data = get_transient('_imageseo_redirect_images');
        if (empty($data) || false === $data) {
            return '';
        }

        $text[] = '# Created by ImageSEO';
        $text[] = '';

        // mod_rewrite section
        $text[] = '<IfModule mod_rewrite.c>';

        $now = time();
        $delay = WEEK_IN_SECONDS * 2;
        foreach ($data as $key => $value) {
            if (!isset($value['date_add']) || $now - $value['date_add'] > $delay) {
                continue;
            }

            if (empty($key) || empty($value['target'])) {
                continue;
            }

            $targetExist = CleanUrl::removeScheme(CleanUrl::removeDomainFromFilename($value['target']));
            if (array_key_exists($targetExist, $text)) {
                continue;
            }

            // /wp-content/uploads/2019/12/example.jpg http://example.local/wp-content/uploads/2019/12/new-image.jpg'
            $text[$key] = sprintf('RedirectPermanent %s %s', $key, $value['target']);
        }

        // End of mod_rewrite
        $text[] = '</IfModule>';
        $text[] = '';

        // End of redirection section
        $text[] = '# End of ImageSEO';

        $text = implode("\n", $text);

        return "\n" . $text . "\n";
    }

    /**
     * @param bool   $existing
     * @param string $newContent
     *
     * @return string
     */
    public function get($existing = false, $newContent)
    {
        if ($existing) {
            if (preg_match(self::INSERT_REGEX, $existing) > 0) {
                $newContent = preg_replace(self::INSERT_REGEX, str_replace('$', '\\$', $newContent), $existing);
            } else {
                $newContent = $newContent . "\n" . trim($existing);
            }
        }

        return trim($newContent);
    }

    /**
     * Save content in htaccess.
     *
     * @param string $content
     *
     * @return bool
     */
    public function save($content)
    {
        $existing = false;

        try {
            if (file_exists($this->filename)) {
                $existing = file_get_contents($this->filename);
            }

            $file = @fopen($this->filename, 'w');
            if ($file) {
                $result = fwrite($file, $this->get($existing, $content));
                fclose($file);

                return false !== $result;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}

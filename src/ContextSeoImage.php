<?php

namespace SeoImageWP;

if (! defined('ABSPATH')) {
    exit;
}

use SeoImageWP\BootstrapSeoImage;

/**
 * Only use for get one context
 */
abstract class ContextSeoImage
{

    /**
     * @static
     * @since 1.0
     * @var BootstrapSeoImage|null
     */
    protected static $context;

    /**
     * Create context if not exist
     *
     * @static
     * @since 1.0
     * @return void
     */
    public static function getContext()
    {
        if (null !== self::$context) {
            return self::$context;
        }

        self::$context = new BootstrapSeoImage();

        self::getClasses(__DIR__ . '/services', 'services', 'Services\\');
        self::getClasses(__DIR__ . '/actions', 'actions', 'Actions\\');



        return self::$context;
    }

    public static function getClasses($path, $type, $namespace = '')
    {
        $files      = array_diff(scandir($path), [ '..', '.' ]);
        foreach ($files as $filename) {
            $pathCheck = $path . '/' . $filename;
            if (is_dir($pathCheck)) {
                self::getClasses($pathCheck, $type, $namespace . $filename . '\\');
                continue;
            }

            $data = '\\SeoImageWP\\' . $namespace . str_replace('.php', '', $filename);

            switch ($type) {
                case 'services':
                    self::$context->setService($data);
                    break;
                case 'actions':
                    self::$context->setAction($data);
                    break;
            }
        }
    }
}

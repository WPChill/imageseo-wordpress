<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

class TagsToString
{
    protected $tagsAvailable = null;

    const REGEX = "#\[(.*?)\]#";

    /**
     * @return array
     */
    public function getTagsAvailable()
    {
        if ($this->tagsAvailable) {
            return apply_filters('imageseo_tags_available', $this->tagsAvailable);
        }

        $this->tagsAvailable = [];
        $files = array_diff(scandir(IMAGESEO_DIR . '/src/Tags'), ['..', '.']);
        foreach ($files as $filename) {
            $class = str_replace('.php', '', $filename);
            $classFile = '\\ImageSeoWP\\Tags\\' . $class;

            if (defined($classFile . '::NAME')) {
                $name = $classFile::NAME;
            } else {
                $name = strtolower($class);
            }

            $this->tagsAvailable[$name] = [
                'class' => $classFile,
                'name'  => $name,
                'input' => sprintf('[%s]', $name),
            ];
        }

        return apply_filters('imageseo_tags_available', $this->tagsAvailable);
    }

    /**
     * @param string $name
     * @param any    $params
     */
    public function __call($name, $params)
    {
        $tagsAvailable = $this->getTagsAvailable();

        if (0 === strpos($name, 'keyword')) {
            $splitKeyword = explode('_', $name);
            $name = 'keyword_X';
            $params['number'] = $splitKeyword[1];
        }

        if (!array_key_exists($name, $tagsAvailable)) {
            return '';
        }

        if (is_string($this->tagsAvailable[$name]['class'])) {
            $this->tagsAvailable[$name]['class'] = new $this->tagsAvailable[$name]['class']();
        }

        if (!is_string($this->tagsAvailable[$name]['class'])) {
            return $this->tagsAvailable[$name]['class']->getValue($params);
        }

        return '';
    }

    /**
     * @param string $string
     *
     * @return array
     */
    public function getTags($string): array
    {
        preg_match_all(self::REGEX, $string, $matches);

        return $matches;
    }

    public function getValueFromTag($tag, $params)
    {
        return call_user_func_array([$this, $tag], $params);
    }

    /**
     * @param string $template
     * @param int    $attachmentId
     *
     * @return string
     */
    public function replace($template, $attachmentId)
    {
        $tags = $this->getTags($template);

        if (!array_key_exists(1, $tags)) {
            return $template;
        }

        $tagsAvailable = $this->getTagsAvailable();

        foreach ($tags[1] as $key => $tag) {
            $value = $this->getValueFromTag($tag, ['attachmentId' => $attachmentId]);

            $template = str_replace($tags[0][$key], $value, $template);
        }

        $template = trim($template);
        $template = ltrim($template, '-');

        return $template;
    }
}

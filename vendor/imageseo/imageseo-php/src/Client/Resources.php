<?php

namespace ImageSeo\Client;

use ImageSeo\Client\Exceptions\ResourceNotExist;

trait Resources
{
    protected $resources = [];
    
    protected function loadResources($path = __DIR__ . '/Endpoints')
    {
        $files      = array_diff(scandir($path), [ '..', '.' ]);
        foreach ($files as $filename) {
            $pathCheck = $path . '/' . $filename;
            if (is_dir($pathCheck)) {
                $this->getClasses($pathCheck, $type, $namespace . $filename . '\\');
                continue;
            }

            $fileNoExtension = str_replace('.php', '', $filename);
            $class = '\\ImageSeo\Client\\Endpoints\\' . $namespace . $fileNoExtension;
            if (! defined($class . '::RESOURCE_NAME')) {
                continue;
            }
            
            $key = $class::RESOURCE_NAME;

            $this->resources[$key] = $class;
        }
    }

    /**
     * @param string $key
     * @return void
     */
    public function getResource($key)
    {
        if (! array_key_exists($key, $this->resources)) {
            throw new ResourceNotExist("$key not exist", 1);
        }

        if (is_string($this->resources[$key])) {
            $this->resources[$key] = new $this->resources[$key]($this->getHttpClient(), $this->getOptions());
        }

        return $this->resources[$key];
    }
}

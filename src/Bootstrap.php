<?php

namespace ImageSeoWP;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Init plugin
 *
 * @since 1.0.0
 */
class Bootstrap
{
    /**
     * List actions WordPress
     * @since 1.0.0
     * @var array
     */
    protected $actions = [];

    /**
     * List class services
     * @since 1.0.0
     * @var array
     */
    protected $services = [];

    /**
     * Set actions
     *
     * @since 1.0.0
     * @param array $actions
     * @return Bootstrap
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
        return $this;
    }

    public function setAction($action)
    {
        $this->actions[] = $action;
        return $this;
    }

    /**
     * Get services
     * @since 1.0.0
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set services
     * @since 1.0.0
     * @param array $services
     * @return Bootstrap
     */
    public function setServices($services)
    {
        foreach ($services as $service) {
            $this->setService($service);
        }
        return $this;
    }

    /**
     * Set a service
     * @since 1.0.0
     * @param string $service
     * @return Bootstrap
     */
    public function setService($service)
    {
        $name = explode('\\', $service);
        end($name);
        $key                             = key($name);
        $this->services[ $name[ $key ] ] = $service;
        return $this;
    }


    /**
     * Get services
     * @since 1.0.0
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Get one service by classname
     * @since 1.0.0
     * @param string $name
     * @return object
     */
    public function getService($name)
    {
        if (! array_key_exists($name, $this->services)) {
            return null;
            // @TODO : Throw exception
        }

        if (is_string($this->services[ $name ])) {
            $this->services[ $name ] = new $this->services[ $name ]();
        }

        return $this->services[ $name ];
    }

    /**
     * Init plugin
     * @since 1.0.0
     * @return void
     */
    public function initPlugin()
    {
        foreach ($this->actions as $action) {
            $action = new $action();
            if (method_exists($action, 'hooks')) {
                $action->hooks();
            }
        }
    }

    /**
     * Activate plugin
     * @since 1.0.0
     * @return void
     */
    public function activatePlugin()
    {
        foreach ($this->actions as $action) {
            $action = new $action();
            if (! method_exists($action, 'activate')) {
                continue;
            }

            $action->activate();
        }
    }

    /**
     * Deactivate plugin
     * @since 1.0.0
     * @return void
     */
    public function deactivatePlugin()
    {
        // Deactivate plugin
    }
}

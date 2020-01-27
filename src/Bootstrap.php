<?php

namespace ImageSeoWP;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Init plugin.
 */
class Bootstrap
{
    /**
     * List actions WordPress.
     *
     *
     * @var array
     */
    protected $actions = [];

    /**
     * List class services.
     *
     *
     * @var array
     */
    protected $services = [];

    /**
     * Set actions.
     *
     *
     * @param array $actions
     *
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
     * Get services.
     *
     *
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set services.
     *
     *
     * @param array $services
     *
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
     * Set a service.
     *
     *
     * @param string $service
     *
     * @return Bootstrap
     */
    public function setService($service)
    {
        $name = explode('\\', $service);
        end($name);
        $key = key($name);
        $this->services[$name[$key]] = $service;

        return $this;
    }

    /**
     * Get services.
     *
     *
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Get one service by classname.
     *
     *
     * @param string $name
     *
     * @return object
     */
    public function getService($name)
    {
        try {
            if (!array_key_exists($name, $this->services)) {
                return null;
                // @TODO : Throw exception
            }

            if (is_string($this->services[$name])) {
                $this->services[$name] = new $this->services[$name]();
            }

            return $this->services[$name];
        } catch (\Exception $th) {
            return null;
        }
    }

    /**
     * Init plugin.
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
     * Activate plugin.     */
    public function activatePlugin()
    {
        try {
            foreach ($this->actions as $action) {
                $action = new $action();
                if (!method_exists($action, 'activate')) {
                    continue;
                }

                $action->activate();
            }
        } catch (\Exception $th) {
            // No need
        }
    }

    /**
     * Deactivate plugin.
     */
    public function deactivatePlugin()
    {
        // Deactivate plugin
    }
}

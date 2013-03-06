<?php

namespace Tidumper\Command;

use ArrayAccess as ServicesInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Base class for tidumper commands
 */
abstract class Command extends BaseCommand
{
    /**
     * services
     *
     * @var ServicesInterface
     */
    protected $services;

    /**
     * inject Services before configure to be able
     * (allows default values to be adapted to application configuration)
     *
     * @param boolean $name
     * @param ServicesInterface $services
     */
    public function __construct($name = null, ServicesInterface $services)
    {
        $this->setServices($services);

        return parent::__construct($name);
    }

    /**
     * set services
     *
     * @param ServicesInterface $service
     * @return Command
     */
    public function setServices(ServicesInterface $services) {
        $this->services = $services;

        return $this;
    }

    /**
     * @return ServicesInterface
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * set a service parameter or callable
     *
     * @param string $serviceId
     * @param mixed $serviceValue
     * @return Command
     */
    public function set($serviceId, $serviceValue) {
        $this->getServices()->offsetSet($serviceId, $serviceValue);

        return $this;
    }

    /**
     * get a service (object) or parameter (string)
     *
     * @param string $service
     * @return string|Object
     */
    public function get($serviceId)
    {
        return $this->getServices()->offsetGet($serviceId);
    }

    /**
     * whether a service or parameter is defined
     *
     * @param string $serviceId
     * @return boolean
     */
    public function has($serviceId) {
        return $this->getServices()->offsetExists($serviceId);
    }

}

<?php

namespace Mcprohosting\Flywheel;

class Factory
{
    /**
     * The CallHandler instance to give to created wheels.
     *
     * @var CallHandler
     */
    protected $handler;

    public function __construct(CallHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Creates a wheel instance. An array of $levels or a single "base" function may be passed to $levels
     *
     * @param string $name
     * @param float $interval
     * @param array|callable $levels
     * @return Wheel
     */
    public function make($name, $interval, $levels = array())
    {
        if (is_callable($levels)) {
            $levels = array(0 => $levels);
        }

        return new Wheel($this->handler, $name, $interval, (array) $levels);
    }
} 
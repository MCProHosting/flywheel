<?php

namespace Mcprohosting\Flywheel;

class Wheel
{
    /**
     * Instance of the call handler.
     *
     * @var CallHandler
     */
    protected $handler;

    /**
     * The name of the flywheel, used for cache storage.
     *
     * @var string
     */
    public $name;

    /**
     * The period, in seconds, for which this Flywheel is examining.
     *
     * @var float
     */
    public $interval;

    /**
     * List of responses to give at varying amounts of requests per second, where the key is the requests per second and
     * the value is a callable.
     *
     * @var array
     */
    public $levels;

    public function __construct(CallHandler $handler, $name, $interval, $levels = array())
    {
        $this->handler  = $handler;
        $this->name     = $name;
        $this->interval = $interval;
        $this->levels   = $levels;
    }

    /**
     * Adds a level to the flywheel.
     *
     * @param integer $calls
     * @param callable $func
     * @return $this
     */
    public function addLevel($calls, $func)
    {
        $this->levels[$calls] = $func;

        ksort($this->levels);

        return $this;
    }

    /**
     * Runs the first applicable level in the wheel, with the same arguments as was used to spin()
     *
     * @return mixed
     */
    public function spin()
    {
        $calls = $this->handler->getCalls($this->name, $this->interval);
        $levels = array_keys($this->levels);

        end($levels);
        while ($l = prev($levels)) {
            if ($calls >= $l) {
                return $this->apply($l, func_get_args());
            }
        }

        return $this->apply($l, func_get_args());
    }

    /**
     * Increments the call number of the wheel and runs the given $level
     *
     * @param integer $level
     * @param array $args
     * @return mixed
     */
    protected function apply($level, $args)
    {
        $this->handler->incrementCalls($this->name, $this->interval);

        return call_user_func_array($this->levels[$level], $args);
    }
} 
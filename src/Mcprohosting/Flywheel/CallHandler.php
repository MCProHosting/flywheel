<?php

namespace Mcprohosting\Flywheel;

use Illuminate\Cache\StoreInterface;

class CallHandler
{
    /**
     * Instance of the cache.
     *
     * @var \Illuminate\Cache\StoreInterface
     */
    protected $cache;

    protected $data = array();

    public function __construct(StoreInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Retrieves the data associated with a particular wheel name.
     *
     * @param string $name
     * @return array
     */
    public function getData($name)
    {
        if (!array_key_exists($name, $this->data)) {
            $data = $this->cache->get('flywheel:' . $name);
            if (!$data) {
                $parts = array(0, 0);
            } else {
                $parts = explode(',', $data);
            }

            $this->data[$name] = $parts;
        }

        return $this->data[$name];
    }

    /**
     * Gets the current number of calls for the given wheel, extrapolating forwards as the calls "slow down".
     *
     * @param string $name
     * @param float $interval
     * @return integer
     */
    public function getCalls($name, $interval)
    {
        list($calls, $last_call) = $this->getData($name);

        $delta = 1 - ((microtime(true) - $last_call) / $interval);

        return $delta < 0 ? 0 : round($calls * $delta);
    }

    /**
     * Increments the number of calls on the flywheel.
     *
     * @param $name
     * @param $interval
     * @return $this
     */
    public function incrementCalls($name, $interval)
    {
        $calls = 1 + $this->getCalls($name, $interval);

        $time = microtime(true);

        $this->data[$name] = array($calls, $time);
        $this->cache->put('flywheel:' . $name, implode(',', $this->data[$name]), ceil($interval / 60));

        return $this;
    }
} 
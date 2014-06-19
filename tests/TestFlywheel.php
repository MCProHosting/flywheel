<?php

use Mcprohosting\Flywheel\CallHandler;
use Mcprohosting\Flywheel\Factory;
use Illuminate\Cache\ArrayStore;

class TestFlywheel extends PHPUnit_Framework_TestCase
{
    public function testCallHandlerGetsDefaults()
    {
        $store = new ArrayStore;
        $handler = new CallHandler($store);

        $this->assertEquals(array(0, 0), $handler->getData('foo'));
    }

    public function testCallHandlerGetsData()
    {
        $store = new ArrayStore;
        $handler = new CallHandler($store);

        $store->put('flywheel:foo', '4,5', 1);
        $this->assertEquals(array(4, 5), $handler->getData('foo'));

        $store->put('flywheel:foo', '6,7', 1);
        $this->assertEquals(array(4, 5), $handler->getData('foo'));
    }

    public function testCallHandlerGetsCalls()
    {
        $store = new ArrayStore;
        $handler = new CallHandler($store);

        $store->put('flywheel:foo', '10,' . (microtime(true) - 0.4), 1);

        $this->assertEquals(8, $handler->getCalls('foo', 2));
    }

    public function testCallHandlerIncrementsCalls()
    {
        $store = new ArrayStore;
        $handler = new CallHandler($store);

        $store->put('flywheel:foo', '10,' . (microtime(true) - 0.4), 1);
        $handler->incrementCalls('foo', 2);

        $this->assertStringStartsWith('9,', $store->get('flywheel:foo'));
        $this->assertEquals(9, $handler->getCalls('foo', 2));
    }

    public function testBaseFunction()
    {
        $store = new ArrayStore;
        $factory = new Factory(new CallHandler($store));

        $calls = 0;
        $wheel = $factory->make('foo', 1, function() use (&$calls) { $calls++; });
        $wheel->spin();
        $this->assertEquals(1, $calls);
    }

    public function testLevelsCenter()
    {
        $store = new ArrayStore;
        $factory = new Factory(new CallHandler($store));
        $store->put('flywheel:foo', '10,' . (microtime(true) - 0.1), 1);

        $calls = 0;
        $wheel = $factory->make('foo', 1, array(
            0  => function () {},
            5  => function () use (&$calls) { $calls++; },
            15 => function () {}
        ));
        $wheel->spin();
        $this->assertEquals(1, $calls);
    }

    public function testAddLevelsDynamically()
    {
        $store = new ArrayStore;
        $factory = new Factory(new CallHandler($store));
        $store->put('flywheel:foo', '10,' . (microtime(true) - 0.1), 1);

        $calls = 0;
        $wheel = $factory->make('foo', 1);
        $wheel->addLevel(0, function () {});
        $wheel->addLevel(5, function () use (&$calls) { $calls++; });
        $wheel->addLevel(15, function () {});

        $wheel->spin();
        $this->assertEquals(1, $calls);
    }
}
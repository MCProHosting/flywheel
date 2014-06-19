# Flywheel

Flywheel is a small Laravel package for reacting differently under varying levels of load. It isn't an extremely precise mechanism - it's optimized for speed and designed to hold up under rigorous circumstances (that's where it's meant to be used, after all!). Its intention is allowing the application to, under high strain such as a denial of service attack, forgo expensive but not mission-critical functionality.

It does this by storing a single string to the cache for each "flywheel". This is quite small and consists of two concatenated values, which are used to do a kind of linear regression to determine the load of the application on the interval. It's designed to be fast, not highly accurate, and to add the minimal possible overhead to the system.

## Usage

 1. Install the package with Laravel.
 2. Register its service provider, `Mcprohosting\Flywheel\FlywheelServiceProvider`
 3. You may wish to register its facade as an alias, `"Flywheel" => "Mcprohosting\Flywheel\Flywheel"`
 3. Profit?

Factory method: **Flywheel::make(string $name, float $interval, callable|array $level)**

Instantiate a new Flywheel. `$name` is a fairly arbitrary string, just used to identify the given wheel with the cache.
`$interval` should be the given interval, in seconds, that you want to "monitor" against. `$level` should be an array,
with its keys being the number of calls it takes to reach that "level" and values being callables that are called at
each level. For example:

```php
$wheel = Flywheel::make('loadStatus', 3, array(
    0  => function ($name) { echo "Load is A-Okay, $name!"; },
    10 => function ($name) { echo "We're getting some load, $name."; },
    50 => function ($name) { echo "We have so much load, $name!"; }
));

$wheel->spin('Connor');
```

Now, if this page gets between zero and ten visitors in a period of three seconds, it'll show the first message: "Load
is A-Okay, Connor!" If it gets between ten and fifty, it will likewise show the second message. Any more than that, the
third message will be shown. You may have noticed that any arguments that get passed to `spin()` will be passed on as
arguments to whatever is called for the current level.

You can alternately declare levels dynamically. This may be preferable, as you're able create the Wheel when the
application boots, but bind the logic on where and when it is necessary.

```php
$wheel = Flywheel::make('loadStatus', 3);
$wheel->addLevel(0, function ($name) { echo "Load is A-Okay, $name!"; });
$wheel->addLevel(10, function ($name) { echo "We're getting some load, $name."; });
$wheel->addLevel(50, function ($name) { echo "We have so much load, $name!"; });

$wheel->spin('Connor');
```
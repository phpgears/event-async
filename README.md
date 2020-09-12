[![PHP version](https://img.shields.io/badge/PHP-%3E%3D7.1-8892BF.svg?style=flat-square)](http://php.net)
[![Latest Version](https://img.shields.io/packagist/v/phpgears/event-async.svg?style=flat-square)](https://packagist.org/packages/phpgears/event-async)
[![License](https://img.shields.io/github/license/phpgears/event-async.svg?style=flat-square)](https://github.com/phpgears/event-async/blob/master/LICENSE)

[![Build Status](https://img.shields.io/travis/phpgears/event-async.svg?style=flat-square)](https://travis-ci.org/phpgears/event-async)
[![Style Check](https://styleci.io/repos/158755934/shield)](https://styleci.io/repos/158755934)
[![Code Quality](https://img.shields.io/scrutinizer/g/phpgears/event-async.svg?style=flat-square)](https://scrutinizer-ci.com/g/phpgears/event-async)
[![Code Coverage](https://img.shields.io/coveralls/phpgears/event-async.svg?style=flat-square)](https://coveralls.io/github/phpgears/event-async)

[![Total Downloads](https://img.shields.io/packagist/dt/phpgears/event-async.svg?style=flat-square)](https://packagist.org/packages/phpgears/event-async/stats)
[![Monthly Downloads](https://img.shields.io/packagist/dm/phpgears/event-async.svg?style=flat-square)](https://packagist.org/packages/phpgears/event-async/stats)

# Async Event

Event bus utilities for asynchronous bus handling

## Installation

### Composer

```
composer require phpgears/event-async
```

## Usage

Require composer autoload file

```php
require './vendor/autoload.php';
```

### Discriminator

Discriminates whether a event should or should not be enqueued based on arbitrary conditions

Three discriminators come bundled in this package

* `Gears\Event\Async\Discriminator\LocatorEventDiscriminator` selects events if they are present in the array provided
* `Gears\Event\Async\Discriminator\ClassEventDiscriminator` selects events by their class or interface
 * `Gears\Event\Async\Discriminator\ParameterEventDiscriminator` selects events by the presence of a event payload parameter (optionally by its value as well)

### Serializer

Abstract event queue uses serializers to do event serialization, so it can be sent to the message queue as a string message

Two serializers are available out of the box

* `Gears\Event\Async\Serializer\JsonEventSerializer`, is a general serializer allowing maximum compatibility in case of events being handled by other systems
* `Gears\Event\Async\Serializer\NativePhpEventSerializer`, is a PHP centric serializer employing PHP native serialization mechanism

You can create your own serializer if the one provided does not fit your needs, for example by using _JMS serializer_, by implementing `Gears\Event\Async\Serializer\EventSerializer` interface

### Distributed systems

On distributed systems, such as micro-service systems, events can be dequeued on a completely different part of the system, this part should of course know about events and their contents but could eventually not have access to the original event class itself and thus a transformation is needed

This can be solved either by transforming messages coming out from the message queue before handing them to the event serializer, or better by creating your custom `Gears\Event\Async\Serializer\EventSerializer` encapsulating this transformation

In most cases the transformation will be as simple as changing the event class to the one the dequeueing side knows. At the end events payload will most probably stay the same

### Event queue

This is the one responsible for actual async handling, which would normally be sending the serialized event to a message queue system such as RabbitMQ

No implementation is provided in this package but an abstract base class so you can extend from it

```php
use Gears\Event\Async\AbstractEventQueue;
use Gears\Event\Event;

class CustomEventQueue extends AbstractEventQueue
{
  public function send(Event $event): void
  {
    // Do the actual enqueue of $this->getSerializedEvent($event);
  }
}
```

You can require [event-async-queue-interop](https://github.com/phpgears/event-async-queue-interop) that uses [queue-interop](https://github.com/queue-interop/queue-interop) for enqueuing messages

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/phpgears/event-async/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/phpgears/event-async/blob/master/CONTRIBUTING.md)

## License

See file [LICENSE](https://github.com/phpgears/event-async/blob/master/LICENSE) included with the source code for a copy of the license terms.

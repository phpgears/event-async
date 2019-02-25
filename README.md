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

Async decorator for Event bus

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

### Asynchronous Events Bus

Event bus decorator to handle events asynchronously

#### Enqueue

```php
use Gears\Event\Async\AsyncEventBus;
use Gears\Event\Async\Serializer\JsonEventSerializer;
use Gears\Event\Async\Discriminator\ParameterEventDiscriminator;

/* @var \Gears\Event\EventBus $eventBus */

/* @var Gears\Event\Async\EventQueue $eventQueue */
$eventQueue = new CustomEventQueue(new JsonEventSerializer());

$asyncEventBus new AsyncEventBus(
    $eventBus,
    $eventQueue,
    new ParameterEventDiscriminator('async')
);

$asyncEvent = new CustomEvent(['async' => true]);

$asyncEventBus->dispatch($asyncEvent);
```

#### Dequeue

This part is highly dependent on your message queue, though event serializers can be used to deserialize queue message

This is just an example of the process

```php
use Gears\Event\Async\ReceivedEvent;
use Gears\Event\Async\Serializer\JsonEventSerializer;

/* @var \Gears\Event\Async\AsyncEventBus $asyncEventBus */
/* @var your_message_queue_manager $queue */

$serializer = new JsonEventSerializer();

while (true) {
  $message = $queue->getMessage();

  if ($message !== null) {
    $event = new ReceivedEvent($serializer->fromSerialized($message));

    $asyncEventBus->dispatch($event);
  }
}
```

Deserialized events should be wrapped in Gears\Event\Async\ReceivedEvent in order to avoid infinite loops should you decide to handle the events on an async bus. If you decide to use a non-async bus on the dequeue side you don't need to do this

### Discriminator

Discriminates whether a event should or should not be enqueued based on arbitrary conditions

Three discriminators are provided in this package

* `Gears\Event\Async\Discriminator\ArrayEventDiscriminator` selects events if they are present in the array provided
* `Gears\Event\Async\Discriminator\ClassEventDiscriminator` selects events by their class or interface
 * `Gears\Event\Async\Discriminator\ParameterEventDiscriminator` selects events by the presence of a event payload parameter (optionally by its value as well)

### Event queue

This is the one responsible for actual async handling, which would normally be send the serialized event to a message queue system such as RabbitMQ

No implementation is provided but an abstract base class so you can extend from it

```php
use Gears\Event\Async\AbstractEventQueue;

class CustomEventQueue extends AbstractEventQueue
{
  public function send(Event $event): void
  {
    // Do the actual enqueue of $this->getSerializedEvent($event);
  }
}
```

### Serializer

Abstract event queue uses serializers to do event serialization so it can be sent to the message queue as a string message

Two serializers are provided out of the box

* `Gears\Event\Async\Serializer\JsonEventSerializer` which is great in general or if you plan to use other languages aside PHP to handle async events
* `Gears\Event\Async\Serializer\NativeEventSerializer` only advised if you're only going to use PHP to dequeue events

It's easy to create your own serializer if this two does not fit your needs, for example by using [JMS serializer](https://github.com/schmittjoh/serializer), simply by implementing `Gears\Event\Async\Serializer\EventSerializer` interface

_This are helping classes that your custom implementation of `EventQueue` might not need_

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/phpgears/event-async/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/phpgears/event-async/blob/master/CONTRIBUTING.md)

## License

See file [LICENSE](https://github.com/phpgears/event-async/blob/master/LICENSE) included with the source code for a copy of the license terms.

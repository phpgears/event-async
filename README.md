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

Please mind that enqueuing process is independent of event handling, does not prevent the event from being handled. Enqueuing an event happens in first place and then the event is dispatched as normal to the wrapped event bus

#### Dequeue

This part is highly dependent on the message queue of your choosing, though event serializers can be used to deserialize queue message

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

Deserialized events should be wrapped in `Gears\Event\Async\ReceivedEvent` in order to avoid infinite loops should you decide to dispatch the events to an async event bus. **If you decide to use a non-async bus on the dequeue side you don't need to do this wrapping**

### Discriminator

Discriminates whether a event should or should not be enqueued based on arbitrary conditions

Three discriminators are provided in this package

* `Gears\Event\Async\Discriminator\ArrayEventDiscriminator` selects events if they are present in the array provided
* `Gears\Event\Async\Discriminator\ClassEventDiscriminator` selects events by their class or interface
 * `Gears\Event\Async\Discriminator\ParameterEventDiscriminator` selects events by the presence of a event payload parameter (optionally by its value as well)

### Event queue

This is the one responsible for actual async handling, which would normally be sending the serialized event to a message queue system such as RabbitMQ

No implementation is provided in this package but an abstract base class so you can extend from it

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

You can use [event-async-queue-interop](https://github.com/phpgears/event-async-queue-interop) that uses [queue-interop](https://github.com/queue-interop/queue-interop) for enqueuing messages

### Serializer

Abstract event queue uses serializers to do event serialization so it can be sent to the message queue as a string message

`Gears\Event\Async\Serializer\JsonEventSerializer` is directly provided as a general serializer allowing maximum compatibility in case of events being handled by other systems

You can create your own serializer if the one provided does not fit your needs, for example by using _JMS serializer_, by implementing `Gears\Event\Async\Serializer\EventSerializer` interface

### Distributed systems

On distributed systems, such as micro-service systems, events can be dequeued on a completely different part of the system, this part should of course know about events triggered and their contents but could eventually not have access to the event class itself

For example in the context of Domain Events on DDD a bounded context could react to events triggered by another completely different bounded context and of course won't be able to deserialize the original event as it is located on another domain

This can be solved in one of two ways, transform messages coming out from the message queue before handing them to the event serializer, or better by creating a custom `Gears\Event\Async\Serializer\EventSerializer` encapsulating this transformation

_Transformation can be as simple as changing event class to be reconstituted_

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/phpgears/event-async/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/phpgears/event-async/blob/master/CONTRIBUTING.md)

## License

See file [LICENSE](https://github.com/phpgears/event-async/blob/master/LICENSE) included with the source code for a copy of the license terms.

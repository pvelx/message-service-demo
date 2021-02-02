<?php declare(strict_types=1);


namespace App\Service\TriggerServiceGrpcAdapter;

use App\Service\TriggerServiceGrpcAdapter\BusMessage\TriggerExecutedMessage;
use App\Service\TriggerServiceGrpcAdapter\Event\TriggerExecutedEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AmqpMessageHandler implements MessageHandlerInterface
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(TriggerExecutedMessage $message)
    {
        $this->dispatcher->dispatch(new TriggerExecutedEvent($message->getTaskId()));
    }
}

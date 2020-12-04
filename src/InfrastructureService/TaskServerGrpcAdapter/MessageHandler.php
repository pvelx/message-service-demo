<?php


namespace App\InfrastructureService\TaskServerGrpcAdapter;

use App\InfrastructureService\TaskServerGrpcAdapter\BusMessage\TaskExecuteMessage;
use App\InfrastructureService\TaskServerGrpcAdapter\Event\TaskExecuteEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MessageHandler implements MessageHandlerInterface
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(TaskExecuteMessage $message)
    {
        $this->dispatcher->dispatch(new TaskExecuteEvent($message->getTaskId()));
    }
}

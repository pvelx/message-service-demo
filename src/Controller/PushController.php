<?php

namespace App\Controller;

use App\DomainService\TasksDeferredService\Config;
use App\DomainService\TasksDeferredService\TasksDeferredService;
use App\InfrastructureService\TriggerHookService\BusMessage\TaskExecuteMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class PushController extends AbstractController
{
    private $tasksDeferredService;
    private $bus;

    public function __construct(TasksDeferredService $tasksDeferredService, MessageBusInterface $bus)
    {
        $this->tasksDeferredService = $tasksDeferredService;
        $this->bus = $bus;
    }

    /**
     * @Route("/push", name="create")
     * @return Response
     * @throws \Exception
     */
    public function Create(): Response
    {
        $this->bus->dispatch(new TaskExecuteMessage(10));



        $event = $this->tasksDeferredService->create(33, Config::TASK_TYPE_SENDING_PUSH, 1);

        dump($event);
        return new Response();
    }
}

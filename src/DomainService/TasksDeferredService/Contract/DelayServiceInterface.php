<?php


namespace App\DomainService\TasksDeferredService;

use App\InfrastructureService\TriggerHookService\Exception\DeferredServiceExceptionInterface;
use Proto\Request;
use Proto\Response;

interface DelayServiceInterface
{
    /**
     * @param Request $request
     * @return Response
     * @throws DeferredServiceExceptionInterface
     */
    public function create(Request $request): Response;
}

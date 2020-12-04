<?php


namespace App\Service\TaskService\Contract;

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

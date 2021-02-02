<?php declare(strict_types=1);


namespace App\Contract\TriggerServiceAdapter;

use Proto\Request;
use Proto\Response;

interface TriggerServiceAdapterInterface
{
    /**
     * @param Request $request
     * @return Response
     * @throws TriggerServiceAdapterExceptionInterface
     */
    public function create(Request $request): Response;

    /**
     * @param Request $request
     * @return Response
     * @throws TriggerServiceAdapterExceptionInterface
     */
    public function delete(Request $request): Response;
}

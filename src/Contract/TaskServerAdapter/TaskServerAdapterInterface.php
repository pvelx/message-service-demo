<?php


namespace App\Contract\TaskServerAdapter;

use Proto\Request;
use Proto\Response;

//TaskServerAdapter
interface TaskServerAdapterInterface
{
    /**
     * @param Request $request
     * @return Response
     * @throws TaskServerAdapterExceptionInterface
     */
    public function create(Request $request): Response;
}

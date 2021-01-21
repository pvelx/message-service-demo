<?php declare(strict_types=1);


namespace App\Contract\TaskServerAdapter;

use Proto\Request;
use Proto\Response;

interface TaskServerAdapterInterface
{
    /**
     * @param Request $request
     * @return Response
     * @throws TaskServerAdapterExceptionInterface
     */
    public function create(Request $request): Response;

    /**
     * @param Request $request
     * @return Response
     * @throws TaskServerAdapterExceptionInterface
     */
    public function delete(Request $request): Response;
}

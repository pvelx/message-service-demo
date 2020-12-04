<?php


namespace App\InfrastructureService\TaskServerGrpcAdapter;


use App\DomainService\TaskService\Contract\DelayServiceInterface;
use App\InfrastructureService\TaskServerGrpcAdapter\Exception\TaskDeferredServiceException;
use Grpc\ChannelCredentials;
use Proto\Request;
use Proto\Response;
use Proto\TaskClient;

class TaskServerGrpcAdapter implements DelayServiceInterface
{
    private $client;

    public function __construct(string $host)
    {
        $this->client = new TaskClient($host, ['credentials' => ChannelCredentials::createInsecure()]);
    }

    /**
     * @param Request $taskRequest
     * @return Response
     * @throws TaskDeferredServiceException
     */
    public function create(Request $taskRequest): Response
    {
        /** @var Response $response */
        list($response, $status) = $this->client->Create($taskRequest)->wait();
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new TaskDeferredServiceException(printf("ERROR: code:%s details:%s", $status->code, $status->details));
        }

        return $response;
    }
}

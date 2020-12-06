<?php declare(strict_types=1);


namespace App\Service\TaskServerGrpcAdapter;

use App\Contract\TaskServerAdapter\TaskServerAdapterInterface;
use App\Service\TaskServerGrpcAdapter\Exception\TaskServerGrpcAdapterException;
use Grpc\ChannelCredentials;
use Proto\Request;
use Proto\Response;
use Proto\TaskClient;

class TaskServerGrpcAdapter implements TaskServerAdapterInterface
{
    private $client;

    public function __construct(string $host)
    {
        $this->client = new TaskClient($host, ['credentials' => ChannelCredentials::createInsecure()]);
    }

    /**
     * @param Request $taskRequest
     * @return Response
     * @throws TaskServerGrpcAdapterException
     */
    public function create(Request $taskRequest): Response
    {
        /** @var Response $response */
        list($response, $status) = $this->client->Create($taskRequest)->wait();
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new TaskServerGrpcAdapterException(printf("ERROR: code:%s details:%s", $status->code, $status->details));
        }

        return $response;
    }
}

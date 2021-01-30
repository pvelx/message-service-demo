<?php declare(strict_types=1);


namespace App\Service\TaskServerGrpcAdapter;

use App\Contract\TaskServerAdapter\TaskServerAdapterInterface;
use App\Service\TaskServerGrpcAdapter\Exception\TaskServerGrpcAdapterException;
use Grpc\ChannelCredentials;
use Proto\Request;
use Proto\Response;
use Proto\TaskClient;
use Throwable;

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
        return $this->call($taskRequest, 'Create');
    }

    /**
     * @param Request $taskRequest
     * @return Response
     * @throws TaskServerGrpcAdapterException
     */
    public function delete(Request $taskRequest): Response
    {
        return $this->call($taskRequest, 'Delete');
    }

    /**
     * @param Request $taskRequest
     * @param string $method
     * @return Response
     */
    private function call(Request $taskRequest, string $method): Response
    {
        try {
            /** @var Response $response */
            list($response, $status) = $this->client->{$method}($taskRequest)->wait();
        } catch (Throwable $e) {
            throw new TaskServerGrpcAdapterException($e);
        }

        if ($status->code !== \Grpc\STATUS_OK) {
            throw new TaskServerGrpcAdapterException(sprintf("ERROR: code:%s details:%s", $status->code, $status->details));
        }

        return $response;
    }
}

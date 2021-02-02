<?php declare(strict_types=1);


namespace App\Service\TriggerServiceGrpcAdapter;

use App\Contract\TriggerServiceAdapter\TriggerServiceAdapterInterface;
use App\Service\TriggerServiceGrpcAdapter\Exception\TriggerServiceGrpcAdapterException;
use Grpc\ChannelCredentials;
use Proto\Request;
use Proto\Response;
use Proto\TaskClient;
use Throwable;

class TriggerServiceGrpcAdapter implements TriggerServiceAdapterInterface
{
    private $client;

    public function __construct(string $host)
    {
        $this->client = new TaskClient($host, ['credentials' => ChannelCredentials::createInsecure()]);
    }

    /**
     * @param Request $taskRequest
     * @return Response
     * @throws TriggerServiceGrpcAdapterException
     */
    public function create(Request $taskRequest): Response
    {
        return $this->call($taskRequest, 'Create');
    }

    /**
     * @param Request $taskRequest
     * @return Response
     * @throws TriggerServiceGrpcAdapterException
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
            throw new TriggerServiceGrpcAdapterException($e);
        }

        if ($status->code !== \Grpc\STATUS_OK) {
            throw new TriggerServiceGrpcAdapterException(sprintf("ERROR: code:%s details:%s", $status->code, $status->details));
        }

        return $response;
    }
}

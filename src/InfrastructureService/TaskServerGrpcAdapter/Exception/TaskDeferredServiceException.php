<?php


namespace App\InfrastructureService\TaskServerGrpcAdapter\Exception;


use App\DomainService\TaskService\Contract\DeferredServiceExceptionInterface;

class TaskDeferredServiceException extends \Exception implements DeferredServiceExceptionInterface
{

}

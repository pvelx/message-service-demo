<?php


namespace App\Service\TaskServerGrpcAdapter\Exception;


use App\Service\TaskService\Contract\DeferredServiceExceptionInterface;

class TaskDeferredServiceException extends \Exception implements DeferredServiceExceptionInterface
{

}

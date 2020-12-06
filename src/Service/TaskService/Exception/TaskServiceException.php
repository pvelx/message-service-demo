<?php


namespace App\Service\TaskService\Exception;

use App\Contract\TaskService\TaskServiceExceptionInterface;
use Error;

class TaskServiceException extends Error implements TaskServiceExceptionInterface
{

}

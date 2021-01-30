<?php declare(strict_types=1);


namespace App\Service\TaskService\Exception;

use App\Contract\TaskService\TaskServiceExceptionInterface;
use Error;

class TaskServiceException extends Error implements TaskServiceExceptionInterface
{

}

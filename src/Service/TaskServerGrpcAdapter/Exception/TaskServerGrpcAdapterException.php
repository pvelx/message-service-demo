<?php

namespace App\Service\TaskServerGrpcAdapter\Exception;

use App\Contract\TaskServerAdapter\TaskServerAdapterExceptionInterface;

class TaskServerGrpcAdapterException extends \Error implements TaskServerAdapterExceptionInterface
{

}

<?php declare(strict_types=1);

namespace App\Service\TaskServerGrpcAdapter\Exception;

use App\Contract\TaskServerAdapter\TaskServerAdapterExceptionInterface;

class TaskServerGrpcAdapterException extends \Error implements TaskServerAdapterExceptionInterface
{

}

<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;

final class ShinobiApiException extends Exception implements ClientExceptionInterface
{
}
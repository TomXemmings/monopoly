<?php

namespace App\Exceptions;

use Exception;

class MonopolyApiException extends Exception
{
    protected array $responseData;

    public function __construct(string $message, int $code, array $responseData = [])
    {
        parent::__construct($message, $code);
        $this->responseData = $responseData;
    }

    public function getResponseData(): array
    {
        return $this->responseData;
    }
}

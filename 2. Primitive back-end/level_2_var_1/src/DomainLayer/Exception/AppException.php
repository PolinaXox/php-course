<?php

namespace App\DomainLayer\Exception;

use Exception;

class AppException extends Exception
{
    /**
     * @param int $httpStatusCode
     * @param string $httpStatusMessage
     * @param string $userMessage
     * @param string $backDevMessage
     * @param string $frontDevMessage
     */
    public function __construct(
        public readonly int    $httpStatusCode = 500,
        public readonly string $httpStatusMessage = 'Internal Server Error',
        public readonly string $userMessage = 'Something went wrong. Please try again later or turn to support.',
        public readonly string $backDevMessage = 'Something went wrong. Fix it!.',
        public readonly string $frontDevMessage = 'Something went wrong. Fix it!.',
    ){
        parent::__construct(message: $this->httpStatusMessage, code: $this->httpStatusCode);
    }

    /**
     * @param AppExceptionsEnum $ex
     * @param array $details
     * @return self
     */
    public static function fromEnum(AppExceptionsEnum $ex, array $details=[]): self {
         return  new self(
             httpStatusCode: $ex->getHttpStatusCode(),
             httpStatusMessage: $ex->getHttpStatusMessage(),
             userMessage: $ex->getUserMessage(),
             backDevMessage: $ex->getBackDevMessage($details),
             frontDevMessage: $ex->getFrontDevMessage($details),
         );
    }
}
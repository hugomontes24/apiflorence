<?php

class ErrorHandler
{
    public static function handleException(Throwable $exception): void
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();

        $response = [
            'message' => $message,
            'code' => $code,
            'file' => $file,
            'line' => $line,
            'trace' => $trace
        ];

        http_response_code(500);
        echo json_encode($response);
    }

    public static function handleError(
        int $errno, 
        string $errstr, 
        string $errfile, 
        int $errline
    ): bool
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }


}
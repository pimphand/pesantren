<?php

namespace App\Http\Controllers;

use App\Http\Services\LogActivityService;

abstract class Controller
{
    /**
     * create a new log entry
     */
    public static function createLog(string $logName, string $description, ?object $subjectType = null, ?array $properties = null, ?string $event = null): void
    {
        $log = new LogActivityService;
        $log->log($logName, $description, get_class($subjectType), $subjectType->id, $properties, $event);
    }
}

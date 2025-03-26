<?php

namespace App\Http\Services;

use Spatie\Activitylog\Models\Activity;

class LogActivityService
{
    /**
     * Log an activity to the database.
     */
    public function log(string $logName, string $description, ?string $subjectType = null, ?string $subjectId = null, ?array $properties = null, ?string $event = null): Activity
    {
        // Create a new activity log entry
        return Activity::create([
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'causer_type' => 'App\Models\User',
            'causer_id' => auth()->user()->id,
            'properties' => $properties,
            'event' => $event,
        ]);
    }
}

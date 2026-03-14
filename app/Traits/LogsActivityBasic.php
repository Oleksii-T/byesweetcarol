<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsActivityBasic
{
    use LogsActivity;

    public function activitiesBy()
    {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'causer');
    }

    public function createdIp(): Attribute
    {
        return new Attribute(
            get: function () {
                $activity = $this->activities()->where('log_name', 'models')->where('event', 'created')->first();
                $ip = $activity?->properties['general_info']['ip'] ?? null;

                return $ip;
            }
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('models')
            ->logAll()
            ->logExcept(['updated_at', 'last_active_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $props = $activity->properties->toArray();
        $props = array_merge($props, infoForActivityLog());
        $activity->properties = collect($props);
    }
}

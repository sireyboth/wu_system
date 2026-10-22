<?php
namespace App\Models;

class Campus extends IModel
{
    protected $fillable = [...DEFAULT_FIELD_AND_SHORTCUT, 'latitude', 'longitude', 'attendance_radius_meters'];

    protected $casts = [
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /** Geofencing is off for this campus unless all three are set. */
    public function hasGeofence(): bool
    {
        return $this->latitude !== null && $this->longitude !== null && $this->attendance_radius_meters !== null;
    }

    /**
     * Great-circle (haversine) distance in meters between this campus and
     * a given point — good enough at campus scale, no need for anything
     * more precise than that.
     */
    public function distanceInMetersFrom(float $lat, float $lng): float
    {
        $earthRadiusMeters = 6371000;
        $latDelta = deg2rad($lat - (float) $this->latitude);
        $lngDelta = deg2rad($lng - (float) $this->longitude);

        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad((float) $this->latitude)) * cos(deg2rad($lat)) * sin($lngDelta / 2) ** 2;

        return $earthRadiusMeters * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}

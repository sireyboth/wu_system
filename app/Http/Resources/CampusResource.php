<?php
namespace App\Http\Resources;

class CampusResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, [
            'shortcut'                 => $this->shortcut,
            'latitude'                 => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude'                => $this->longitude !== null ? (float) $this->longitude : null,
            'attendance_radius_meters' => $this->attendance_radius_meters,
            'has_geofence'             => $this->hasGeofence(),
        ]);
    }
}

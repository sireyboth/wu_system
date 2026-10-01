<?php
namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Invigilator extends IModel
{
    protected $fillable = [...DEFAULT_FIELD_AND_CODE, 'batch', 'department', 'room', 'valid_until'];

    protected array $searchable = ['name_en', 'name_kh', 'code', 'batch', 'department', 'room'];

    protected $casts = [
        'valid_until' => 'date',
    ];

    protected static function booted(): void
    {
        // Set once, never from input — it's the permanent QR target, so a
        // printed card keeps working after the invigilator is edited.
        static::creating(function (self $invigilator) {
            $invigilator->public_token ??= Str::random(40);
        });
    }

    public function histories()
    {
        return $this->hasMany(InvigilatorHistory::class)->orderByDesc('date')->orderByDesc('id');
    }

    public function getPublicUrlAttribute(): string
    {
        return route('invigilator.public', $this->public_token);
    }

    /**
     * Keyed by the public token (the public page shows it too), with a
     * version stamp so a replaced photo isn't served from browser cache.
     * photo_path itself is never mass-assigned — see uploadPhoto().
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        return route('invigilator.photo', $this->public_token) . '?v=' . ($this->updated_at?->timestamp ?? 0);
    }

    /** No end date means open-ended; the card says EXPIRED only once it has passed. */
    public function isExpired(): bool
    {
        return $this->valid_until !== null && $this->valid_until->endOfDay()->isPast();
    }

    /**
     * Whether the photo is a background-removed cutout (transparent
     * corners) — the card lets a cutout pop out above the arch, while an
     * ordinary rectangular photo stays clipped inside it. Cached per file
     * path; a new upload gets a new path, so the cache never goes stale.
     */
    public function photoIsCutout(): bool
    {
        $path = $this->photo_path;
        if (! $path) {
            return false;
        }

        return Cache::rememberForever('invigilator-photo-cutout:' . $path, function () use ($path) {
            $disk = Storage::disk('public');
            if (! $disk->exists($path)) {
                return false;
            }

            $image = @imagecreatefromstring($disk->get($path));
            if (! $image) {
                return false;
            }

            $w = imagesx($image) - 1;
            $h = imagesy($image) - 1;
            $transparent = 0;
            foreach ([[0, 0], [$w, 0], [0, $h], [$w, $h], [intdiv($w, 2), 0]] as [$x, $y]) {
                // GD alpha: 0 = opaque … 127 = fully transparent.
                if (((imagecolorat($image, $x, $y) >> 24) & 0x7F) > 100) {
                    $transparent++;
                }
            }
            imagedestroy($image);

            return $transparent >= 3;
        });
    }
}

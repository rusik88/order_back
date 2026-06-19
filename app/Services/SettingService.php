<?php
namespace App\Services;

use App\Models\Setting;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    private const CACHE_PREFIX = 'settings.';

    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever($this->cacheKey($key), function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return $setting->value;
        });
    }

    public function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value
            ]
        );

        Cache::forget($this->cacheKey($key));
    }

    public function setAll(array $settings): void
    {
        if(!empty($settings)) {
            foreach ($settings as $setting) {
                $this->set($setting['key'], $setting['value']);
            }

            Cache::forget(self::CACHE_PREFIX . 'all');
        }
    }

    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_PREFIX . 'all', function () {
            return Setting::all()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->key => [
                            'title' => $item->title,
                            'value' => $this->prepareValue($item->value, $item->type)
                        ],
                    ];
                })
                ->toArray();
        });
    }

    private function prepareValue(mixed $value, string $type = ''): mixed
    {
        return match ($type) {
            'number' => (int) $value,
            'role' => $this->prepareRoles($value),
            default => (string) $value,
        };
    }

    private function prepareRoles(mixed $value): array
    {
        $roles = Role::all();

        return $roles->map(function ($role) use ($value) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'selected' => in_array($role->id, (array) $value),
            ];
        })->toArray();
    }

    public function clearSettingsCache(): void
    {
        Cache::forget(self::CACHE_PREFIX . 'all');
    }

    private function cacheKey(string $key): string
    {
        return self::CACHE_PREFIX . $key;
    }
}

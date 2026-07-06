<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Setting;

class SettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = Setting::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public function setAll(array $settings): void
    {
        foreach ($settings as $setting) {
            $this->set(
                $setting['key'],
                $setting['value']
            );
        }
    }

    public function all(): array
    {
        return Setting::all()
            ->map(function ($item) {
                return [
                    'title' => $item->title,
                    'type'  => $item->type,
                    'key'   => $item->key,
                    'value' => $this->prepareValue(
                        $item->value,
                        $item->type
                    ),
                ];
            })
            ->values()
            ->toArray();
    }

    private function prepareValue(mixed $value, string $type = ''): mixed
    {
        return match ($type) {
            'number' => (int) $value,
            'role'   => $this->prepareRoles($value),
            default  => (string) $value,
        };
    }

    private function prepareRoles(mixed $value): array
    {
        return Role::query()
            ->where('slug', '!=', 'super_admin')
            ->get()
            ->map(function ($role) use ($value) {
                return [
                    'id'       => $role->id,
                    'name'     => $role->name,
                    'selected' => ($role->id == $value),
                ];
            })
            ->toArray();
    }
}

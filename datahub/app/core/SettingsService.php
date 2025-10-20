<?php
namespace App\Core;

use App\Models\Setting;

class SettingsService
{
    private Setting $settingModel;

    public function __construct()
    {
        $this->settingModel = new Setting();
    }

    public function get(string $key, $default = null)
    {
        $setting = $this->settingModel->get($key);
        return $setting['value'] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->settingModel->set($key, $value);
    }
}

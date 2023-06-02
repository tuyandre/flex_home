<?php

namespace Botble\Setting\Supports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

abstract class SettingStore
{
    protected array $data = [];

    protected bool $unsaved = false;

    protected bool $loaded = false;

    protected string $cacheKey = 'cms_settings_cache';

    protected int $settingTime = 86400;

    public function get(string|array $key, mixed $default = null): mixed
    {
        $this->load();

        return Arr::get($this->data, $key, $default);
    }

    public function has(string $key): bool
    {
        $this->load();

        return Arr::has($this->data, $key);
    }

    public function set(string|array $key, mixed $value = null): self
    {
        $this->load();
        $this->unsaved = true;

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                Arr::set($this->data, $k, $v);
            }
        } else {
            Arr::set($this->data, $key, $value);
        }

        return $this;
    }

    public function forget(string $key): self
    {
        $this->unsaved = true;

        if ($this->has($key)) {
            Arr::forget($this->data, $key);
        }

        return $this;
    }

    public function forgetAll(): self
    {
        $this->unsaved = true;
        $this->data = [];

        return $this;
    }

    public function all(): array
    {
        $this->load();

        return $this->data;
    }

    public function save(): bool
    {
        if (! $this->unsaved) {
            return false;
        }

        $this->write($this->data);
        $this->unsaved = false;

        $this->clearCache();

        return true;
    }

    public function load(bool $force = false): void
    {
        if (! $this->loaded || $force) {
            $this->data = $this->read();
            $this->loaded = true;
        }
    }

    protected function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    abstract protected function read(): array;

    abstract protected function write(array $data): void;

    abstract public function delete(array $keys = [], array $except = []);

    abstract public function newQuery(): Builder;
}

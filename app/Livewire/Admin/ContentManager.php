<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Services\ImageService;
use App\Support\SiteContent;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class ContentManager extends Component
{
    use WithFileUploads;

    /** @var array<string, mixed> */
    public array $data = [];

    /** @var array<string, mixed> */
    public array $uploads = [];

    public function mount(): void
    {
        foreach (SiteContent::fields() as $key => $field) {
            $this->data[$key] = (string) SiteContent::get($key);
        }
    }

    public function save(ImageService $images): void
    {
        $rules = [];
        $attributes = [];

        foreach (SiteContent::fields() as $key => $field) {
            $attributes["data.{$key}"] = $field['label'];
            $attributes["uploads.{$key}"] = $field['label'];

            $rules["data.{$key}"] = match ($field['type']) {
                'email' => ['nullable', 'email', 'max:190'],
                'url' => ['nullable', 'url:http,https', 'max:2048'],
                'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
                default => ['nullable', 'string', 'max:8000'],
            };

            if ($field['type'] === 'image') {
                $rules["uploads.{$key}"] = ['nullable', 'image', 'max:8192'];
            }
        }

        $this->validate($rules, [
            'data.*.regex' => 'Use a six-digit hex colour, for example #FF8200.',
        ], $attributes);

        $converted = [];

        foreach (SiteContent::imageFields() as $key) {
            $file = $this->uploads[$key] ?? null;

            if (! $file) {
                continue;
            }

            try {
                $converted[$key] = $images->toWebp($file, 'content', 2200, 84);
            } catch (\RuntimeException $exception) {
                foreach ($converted as $path) {
                    $images->delete($path);
                }

                $this->addError("uploads.{$key}", $exception->getMessage());

                return;
            }
        }

        foreach (SiteContent::fields() as $key => $field) {
            if ($field['type'] === 'image') {
                if (isset($converted[$key])) {
                    $old = (string) SiteContent::get($key);
                    $images->delete($old);
                    Setting::put($key, $converted[$key]);
                    $this->data[$key] = $converted[$key];
                }

                continue;
            }

            Setting::put($key, (string) ($this->data[$key] ?? ''));
        }

        $this->uploads = [];
        $this->dispatch('toast', message: 'Site content saved and live.', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.content-manager', [
            'groups' => SiteContent::groups(),
        ]);
    }
}

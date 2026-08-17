<?php

namespace App\Models\Concerns;

trait Localizable
{
    public function getLocalized(string $field): string
    {
        $locale = app()->getLocale();
        $swField = $field.'_sw';
        $enField = $field.'_en';

        if ($locale === 'sw' && $this->{$swField}) {
            return $this->{$swField};
        }

        return (string) ($this->{$enField} ?? '');
    }

    public function getTopicsAttribute(): array
    {
        $raw = $this->getLocalized('topics');

        if (blank($raw)) {
            return [];
        }

        $raw = str_replace(["\r", '|'], "\n", (string) $raw);

        return array_values(array_filter(array_map(
            'trim',
            explode("\n", $raw)
        )));
    }
}

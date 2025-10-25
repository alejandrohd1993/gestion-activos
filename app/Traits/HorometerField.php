<?php

namespace App\Traits;

use Filament\Forms\Components\TextInput;

trait HorometerField
{
    public static function makeHorometerField(string $name, string $label = 'Horómetro'): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->required()
            ->rule('regex:/^\d+:[0-5]\d:[0-5]\d$/')
            ->validationMessages([
                'regex' => 'El formato debe ser H:MM:SS (ejemplo 12:05:59)',
            ])
            ->placeholder('12:34:56')
            ->dehydrateStateUsing(function ($state) {
                if (! $state) {
                    return null;
                }

                [$hours, $minutes, $seconds] = explode(':', $state);

                return ((int) $hours * 3600) + ((int) $minutes * 60) + (int) $seconds;
            })
            ->afterStateHydrated(function ($set, $state) use ($name) {
                if (! $state) {
                    return;
                }

                $hours   = floor($state / 3600);
                $minutes = floor(($state % 3600) / 60);
                $seconds = $state % 60;

                $formatted = sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);

                $set($name, $formatted);
            });
    }
}

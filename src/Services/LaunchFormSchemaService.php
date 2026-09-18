<?php

declare(strict_types=1);

namespace Rimba\Workflow\Services;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

final class LaunchFormSchemaService
{
    public function build(array $fields): array
    {
        return array_map(function (array $f): Textarea|Select|DatePicker|Toggle|TextInput {
            $component = match ($f['type'] ?? 'text') {
                'textarea' => Textarea::make($f['name']),
                'select' => Select::make($f['name'])->options($f['options'] ?? []),
                'date' => DatePicker::make($f['name']),
                'boolean' => Toggle::make($f['name']),
                default => TextInput::make($f['name']),
            };

            return $component
                ->label($f['label'] ?? str($f['name'])->headline())
                ->required((bool) ($f['required'] ?? false));
        }, $fields);
    }
}

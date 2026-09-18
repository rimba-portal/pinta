<?php

namespace Rimba\Workflow\Services;

use Illuminate\Support\Facades\File;
use Rimba\Workflow\Exceptions\DefinitionNotFound;

class DefinitionRepository
{
    public function get(string $slug): array
    {
        $base = rtrim(config('pinta.definitions_path'), '/');
        foreach (['workflows', 'activities', 'workpackages'] as $type) {
            $path = $base . '/' . $type . '/' . $slug . '.json';
            if (File::exists($path)) return json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        }
        throw new DefinitionNotFound("Pinta definition [{$slug}] was not found.");
    }
}

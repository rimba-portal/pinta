<?php
declare(strict_types=1);
namespace Rimba\Workflow\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Rimba\Workflow\Definitions\WorkflowDefinition;
use Rimba\Workflow\Exceptions\DefinitionNotFound;

final class DefinitionRegistry
{
    public function __construct(private DefinitionValidator $validator) {}
    public function workflow(string $slug): WorkflowDefinition
    {
        $path=rtrim(config('pinta.definitions_path'),'/').'/workflows/'.$slug.'.json';
        if (!File::exists($path)) throw new DefinitionNotFound("Workflow definition [{$slug}] was not found.");
        $data=json_decode(File::get($path),true,512,JSON_THROW_ON_ERROR); $this->validator->assert($data);
        return WorkflowDefinition::fromArray($data);
    }
    public function workflows(): Collection
    {
        $path=rtrim(config('pinta.definitions_path'),'/').'/workflows';
        return collect(File::glob($path.'/*.json'))->map(fn(string $file)=>$this->workflow(pathinfo($file,PATHINFO_FILENAME)))->sortBy('title')->values();
    }
}

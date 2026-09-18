<?php

declare(strict_types=1);

namespace Rimba\Workflow\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Rimba\Workflow\Services\DefinitionValidator;

#[Description('Validate all Pinta workflow definitions')]
#[Signature('pinta:validate')]
final class ValidateWorkflowDefinitions extends Command
{
    public function handle(DefinitionValidator $v): int
    {
        $failed = false;
        foreach (File::glob(rtrim(config('pinta.definitions_path'), '/').'/workflows/*.json') as $file) {
            try {
                $d = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);
                $e = $v->validate($d);
                if ($e) {
                    $failed = true;
                    $this->error(basename($file));
                    foreach ($e as $x) {
                        $this->line('  - '.$x);
                    }
                } else {
                    $this->info('OK '.basename($file));
                }
            } catch (\Throwable $e) {
                $failed = true;
                $this->error(basename($file).': '.$e->getMessage());
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}

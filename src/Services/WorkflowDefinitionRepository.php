<?php

declare(strict_types=1);

namespace Rimba\Workflow\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Rimba\Workflow\Exceptions\DefinitionNotFound;
use Rimba\Workflow\Models\WorkflowDefinition;
use RuntimeException;

final class WorkflowDefinitionRepository
{
    public function __construct(
        private readonly DefinitionValidator $definitionValidator,
    ) {}

    /**
     * @return Collection<int, WorkflowDefinition>
     */
    public function all(): Collection
    {
        $directory = $this->directory();

        if (! File::isDirectory($directory)) {
            return collect();
        }

        return collect(File::glob($directory.'/*.json'))
            ->map(fn (string $path): WorkflowDefinition => $this->loadFile($path))
            ->sortBy(
                fn (WorkflowDefinition $workflow): string => mb_strtolower((string) $workflow->title)
            )
            ->values();
    }

    public function find(string $slug): WorkflowDefinition
    {
        $path = $this->pathFor($slug);

        if (! File::exists($path)) {
            throw new DefinitionNotFound(
                "Workflow definition [{$slug}] was not found."
            );
        }

        return $this->loadFile($path);
    }

    public function findOrNull(string $slug): ?WorkflowDefinition
    {
        try {
            return $this->find($slug);
        } catch (DefinitionNotFound) {
            return null;
        }
    }

    public function exists(string $slug): bool
    {
        return File::exists($this->pathFor($slug));
    }

    public function create(array $data): WorkflowDefinition
    {
        $workflowDefinition = WorkflowDefinition::makeDefinition($data);

        $slug = $this->normalizeSlug(
            (string) $workflowDefinition->getAttribute('slug')
        );

        if ($this->exists($slug)) {
            throw new RuntimeException(
                "Workflow definition [{$slug}] already exists."
            );
        }

        return $this->save($workflowDefinition);
    }

    public function save(WorkflowDefinition $workflow): WorkflowDefinition
    {
        $data = $workflow->toDefinitionArray();

        $slug = $this->normalizeSlug(
            (string) ($data['slug'] ?? '')
        );

        $data['slug'] = $slug;

        $errors = $this->definitionValidator->validate($data);

        if ($errors !== []) {
            throw new \InvalidArgumentException(
                implode(PHP_EOL, $errors)
            );
        }

        $directory = $this->directory();

        File::ensureDirectoryExists($directory);

        $destination = $this->pathFor($slug);

        $originalSlug = $workflow->getOriginal('slug');

        if (
            ! $workflow->exists &&
            File::exists($destination)
        ) {
            throw new RuntimeException(
                "Workflow definition [{$slug}] already exists."
            );
        }

        if (
            is_string($originalSlug) &&
            $originalSlug !== '' &&
            $originalSlug !== $slug &&
            File::exists($destination)
        ) {
            throw new RuntimeException(
                "Cannot rename workflow to [{$slug}] because it already exists."
            );
        }

        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_THROW_ON_ERROR
        ).PHP_EOL;

        $temporaryPath = $destination.'.tmp.'.bin2hex(random_bytes(6));

        try {
            $written = file_put_contents(
                $temporaryPath,
                $json,
                LOCK_EX
            );

            if ($written === false) {
                throw new RuntimeException(
                    "Unable to write temporary workflow definition [{$temporaryPath}]."
                );
            }

            if (! rename($temporaryPath, $destination)) {
                throw new RuntimeException(
                    "Unable to replace workflow definition [{$destination}]."
                );
            }
        } finally {
            if (File::exists($temporaryPath)) {
                File::delete($temporaryPath);
            }
        }

        if (
            is_string($originalSlug) &&
            $originalSlug !== '' &&
            $originalSlug !== $slug
        ) {
            $originalPath = $this->pathFor($originalSlug);

            if (File::exists($originalPath)) {
                File::delete($originalPath);
            }
        }

        return $this->find($slug);
    }

    public function delete(
        WorkflowDefinition|string $workflow
    ): void {
        $slug = $workflow instanceof WorkflowDefinition
            ? (string) $workflow->getAttribute('slug')
            : $workflow;

        $slug = $this->normalizeSlug($slug);

        $path = $this->pathFor($slug);

        if (! File::exists($path)) {
            throw new DefinitionNotFound(
                "Workflow definition [{$slug}] was not found."
            );
        }

        if (! File::delete($path)) {
            throw new RuntimeException(
                "Unable to delete workflow definition [{$slug}]."
            );
        }
    }

    public function duplicate(
        WorkflowDefinition|string $workflow,
        string $newSlug,
        ?string $newTitle = null,
    ): WorkflowDefinition {
        $source = $workflow instanceof WorkflowDefinition
            ? $workflow
            : $this->find($workflow);

        $newSlug = $this->normalizeSlug($newSlug);

        if ($this->exists($newSlug)) {
            throw new RuntimeException(
                "Workflow definition [{$newSlug}] already exists."
            );
        }

        $data = $source->toDefinitionArray();

        $data['slug'] = $newSlug;
        $data['title'] = $newTitle
            ?? (($data['title'] ?? $source->slug).' Copy');

        $data['version'] = 1;

        return $this->create($data);
    }

    public function validate(
        WorkflowDefinition|array $workflow
    ): array {
        $data = $workflow instanceof WorkflowDefinition
            ? $workflow->toDefinitionArray()
            : $workflow;

        return $this->definitionValidator->validate($data);
    }

    private function loadFile(string $path): WorkflowDefinition
    {
        try {
            $data = json_decode(
                File::get($path),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $jsonException) {
            throw new RuntimeException(sprintf(
                'Invalid JSON in workflow definition [%s\]: %s',
                basename($path),
                $jsonException->getMessage()
            ), $jsonException->getCode(), previous: $jsonException);
        }

        if (! is_array($data)) {
            throw new RuntimeException(
                sprintf(
                    'Workflow definition [%s] must contain a JSON object.',
                    basename($path)
                )
            );
        }

        $filenameSlug = pathinfo($path, PATHINFO_FILENAME);
        $definitionSlug = (string) ($data['slug'] ?? '');

        if ($definitionSlug === '') {
            throw new RuntimeException(
                "Workflow definition [{$filenameSlug}] does not contain a slug."
            );
        }

        if ($filenameSlug !== $definitionSlug) {
            throw new RuntimeException(
                "Workflow filename [{$filenameSlug}] does not match its slug [{$definitionSlug}]."
            );
        }

        $errors = $this->definitionValidator->validate($data);

        if ($errors !== []) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid workflow definition [%s\]:%s%s",
                    $definitionSlug,
                    PHP_EOL,
                    implode(PHP_EOL, $errors)
                )
            );
        }

        return WorkflowDefinition::fromDefinition($data);
    }

    private function directory(): string
    {
        return rtrim(
            (string) config('pinta.definitions_path'),
            DIRECTORY_SEPARATOR
        ).DIRECTORY_SEPARATOR.'workflows';
    }

    private function pathFor(string $slug): string
    {
        $slug = $this->normalizeSlug($slug);

        return $this->directory()
            .DIRECTORY_SEPARATOR
            .$slug
            .'.json';
    }

    private function normalizeSlug(string $slug): string
    {
        $slug = trim($slug);

        if ($slug === '') {
            throw new \InvalidArgumentException(
                'Workflow slug is required.'
            );
        }

        /**
         * Allows:
         *
         * hr.workforce.recruitment
         * om.manpower_acquisition.manpower_request
         * expense-claim
         */
        if (
            preg_match(
                '/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/',
                $slug
            ) !== 1
        ) {
            throw new \InvalidArgumentException(
                "Invalid workflow slug [{$slug}]. "
                .'Use lowercase letters, numbers, dots, underscores, or hyphens.'
            );
        }

        return $slug;
    }
}

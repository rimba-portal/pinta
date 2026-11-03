<?php

declare(strict_types=1);

namespace Rimba\Request\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use JsonException;
use RuntimeException;
use Throwable;

#[Description('Seed Packstub forms, workflows, triggers, and RIMBA offerings from a service bundle.')]
#[Signature('rimba:seed-flows
        {bundle? : Bundle name or path to a JSON file}
        {--fresh : Replace seeded workflow triggers and versions}
        {--dry-run : Validate and display planned changes without writing}
        {--force : Allow execution in production}')]
final class SeedFlowsCommand extends Command
{
    /**
     * Stable UUID namespace for seeded workflow IDs.
     *
     * Do not change this after workflows have been seeded.
     */
    private const WORKFLOW_UUID_NAMESPACE =
        'f58c1e13-72cb-4cf6-b87e-c607b3750701';

    /**
     * @var array<string, int|string>
     */
    private array $forms = [];

    /**
     * @var array<string, string>
     */
    private array $workflows = [];

    /**
     * @var array<string, int>
     */
    private array $orgUnits = [];

    /**
     * @var array<string, int>
     */
    private array $orgTeams = [];

    private string $formsTable;

    private string $workflowsTable;

    private string $workflowVersionsTable;

    private string $workflowTriggersTable;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly ConnectionInterface $connection,
    ) {
        parent::__construct();

        $this->formsTable = (string) config(
            'packstub-form-builder.tables.forms',
            'form_builder_forms',
        );

        $this->workflowsTable = (string) config(
            'packstub-flow.tables.workflows',
            'flow_workflows',
        );

        $this->workflowVersionsTable = (string) config(
            'packstub-flow.tables.versions',
            'flow_workflow_versions',
        );

        $this->workflowTriggersTable = (string) config(
            'packstub-flow.tables.triggers',
            'flow_workflow_triggers',
        );
    }

    public function handle(): int
    {
        if (
            app()->environment('production')
            && ! $this->option('force')
        ) {
            $this->components->error(
                'Use --force to seed service bundles in production.',
            );

            return self::FAILURE;
        }

        try {
            $path = $this->resolveBundlePath(
                bundle: $this->argument('bundle'),
            );

            $bundle = $this->loadBundle($path);

            $this->validateDatabaseStructure();
            $this->validateBundle($bundle);

            $this->displaySummary($path, $bundle);

            if ($this->option('dry-run')) {
                $this->components->info(
                    'Dry run completed. No database changes were made.',
                );

                return self::SUCCESS;
            }

            $this->connection->transaction(function () use ($bundle): void {
                $this->seedForms(
                    forms: $bundle['form_builder_forms'] ?? [],
                );

                $this->seedWorkflows(
                    workflows: $bundle['flow_workflows'] ?? [],
                );

                $this->seedWorkflowTriggers(
                    triggers: $bundle['flow_workflow_triggers'] ?? [],
                );

                $this->seedOfferings(
                    offerings: $bundle['req_offerings'] ?? [],
                );
            });

            $this->newLine();

            $this->components->info(
                'Service bundle seeded successfully.',
            );

            return self::SUCCESS;
        } catch (Throwable $throwable) {
            report($throwable);

            $this->components->error($throwable->getMessage());

            if ($this->output->isVerbose()) {
                $this->newLine();
                $this->line($throwable->getTraceAsString());
            }

            return self::FAILURE;
        }
    }

    /**
     * @throws RuntimeException
     */
    private function resolveBundlePath(mixed $bundle): string
    {
        if (! is_string($bundle) || blank($bundle)) {
            $bundle = 'document';
        }

        /*
         * Allow an explicit absolute or relative file path.
         */
        $directPath = $this->normalizePath($bundle);

        if ($this->filesystem->isFile($directPath)) {
            return $directPath;
        }

        /*
         * Otherwise resolve a package service bundle name.
         *
         * Example:
         * php artisan rimba:seed-service document
         *
         * resolves to:
         * packages/rimba/request/database/seeders/data/services/document.json
         */
        $filename = Str::endsWith($bundle, '.json')
            ? $bundle
            : "{$bundle}.json";

        $packagePath = __DIR__
            .'/../database/seeders/data/services/'
            .$filename;

        if ($this->filesystem->isFile($packagePath)) {
            return $packagePath;
        }

        /*
         * Also permit application-level bundles.
         */
        $applicationPath = database_path(
            'seeders/data/services/'.$filename,
        );

        if ($this->filesystem->isFile($applicationPath)) {
            return $applicationPath;
        }

        throw new RuntimeException(
            "Service seed bundle [{$bundle}] was not found. "
            ."Checked [{$packagePath}] and [{$applicationPath}].",
        );
    }

    private function normalizePath(string $path): string
    {
        if (Str::startsWith($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        return base_path($path);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     * @throws RuntimeException
     */
    private function loadBundle(string $path): array
    {
        $decoded = json_decode(
            $this->filesystem->get($path),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        if (! is_array($decoded)) {
            throw new RuntimeException(
                "Service bundle [{$path}] must contain a JSON object.",
            );
        }

        return $decoded;
    }

    /**
     * Validate only the tables that this command actually writes.
     *
     * Lifecycle and phase tables are intentionally absent because the
     * lifecycle catalogue is JSON consumed by the Filament catalogue page.
     */
    private function validateDatabaseStructure(): void
    {
        $requiredTables = [
            $this->formsTable,
            $this->workflowsTable,
            $this->workflowVersionsTable,
            $this->workflowTriggersTable,
            'req_offerings',
            'org_units',
            'org_teams',
        ];

        $missing = collect($requiredTables)
            ->reject(
                fn (string $table): bool => Schema::hasTable($table),
            )
            ->values();

        if ($missing->isNotEmpty()) {
            throw new RuntimeException(
                'Required tables are missing: '
                .$missing->implode(', ')
                .'. Run the package migrations first.',
            );
        }
    }

    /**
     * @param  array<string, mixed>  $bundle
     */
    private function validateBundle(array $bundle): void
    {
        foreach (
            [
                'form_builder_forms',
                'flow_workflows',
                'flow_workflow_triggers',
                'req_offerings',
            ] as $collection
        ) {
            if (
                isset($bundle[$collection])
                && ! is_array($bundle[$collection])
            ) {
                throw new RuntimeException(
                    "Bundle key [{$collection}] must be an array.",
                );
            }
        }

        /*
         * The lifecycle block is accepted as catalogue metadata only.
         * It is not a database seed target.
         */
        if (
            isset($bundle['lifecycle'])
            && ! is_array($bundle['lifecycle'])
        ) {
            throw new RuntimeException(
                'Bundle key [lifecycle] must be an object when provided.',
            );
        }

        foreach ($bundle['form_builder_forms'] ?? [] as $index => $form) {
            $this->requireKeys(
                record: $form,
                keys: ['natural_key', 'name', 'slug'],
                context: "form_builder_forms.{$index}",
            );
        }

        foreach ($bundle['flow_workflows'] ?? [] as $index => $workflow) {
            $this->requireKeys(
                record: $workflow,
                keys: ['natural_key', 'name', 'definition'],
                context: "flow_workflows.{$index}",
            );
        }

        foreach ($bundle['req_offerings'] ?? [] as $index => $offering) {
            $this->requireKeys(
                record: $offering,
                keys: [
                    'code',
                    'name',
                    'org_unit_id',
                    'form_id',
                    'workflow_id',
                ],
                context: "req_offerings.{$index}",
            );
        }
    }

    /**
     * @param  array<string, mixed>  $record
     * @param  array<int, string>  $keys
     */
    private function requireKeys(
        array $record,
        array $keys,
        string $context,
    ): void {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $record)) {
                throw new RuntimeException(
                    "Missing required key [{$context}.{$key}].",
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>  $bundle
     */
    private function displaySummary(
        string $path,
        array $bundle,
    ): void {
        $this->components->info(
            'Service bundle validated.',
        );

        $this->table(
            ['Item', 'Value'],
            [
                ['File', $path],
                ['Bundle', $bundle['bundle'] ?? 'Not specified'],
                [
                    'Lifecycle metadata',
                    isset($bundle['lifecycle'])
                        ? 'Present, not persisted'
                        : 'Not included',
                ],
                [
                    'Forms',
                    count($bundle['form_builder_forms'] ?? []),
                ],
                [
                    'Workflows',
                    count($bundle['flow_workflows'] ?? []),
                ],
                [
                    'Triggers',
                    count($bundle['flow_workflow_triggers'] ?? []),
                ],
                [
                    'Offerings',
                    count($bundle['req_offerings'] ?? []),
                ],
            ],
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $forms
     */
    private function seedForms(array $forms): void
    {
        foreach ($forms as $form) {
            $naturalKey = (string) $form['natural_key'];
            $slug = (string) $form['slug'];

            $existing = DB::table($this->formsTable)
                ->where('slug', $slug)
                ->first();

            $values = [
                'name' => $form['name'],
                'description' => $form['description'] ?? null,
                'fields' => $this->json($form['fields'] ?? []),
                'is_active' => (bool) ($form['is_active'] ?? true),
                'submit_label' => $form['submit_label'] ?? null,
                'success_message' => $form['success_message'] ?? null,
                'redirect_url' => $form['redirect_url'] ?? null,
                'notification_emails' => $this->nullableJson(
                    $form['notification_emails'] ?? null,
                ),
                'store_submissions' => (bool) (
                    $form['store_submissions'] ?? true
                ),
                'opens_at' => $form['opens_at'] ?? null,
                'closes_at' => $form['closes_at'] ?? null,
                'settings' => $this->nullableJson(
                    $form['settings'] ?? null,
                ),
                'updated_at' => now(),
            ];

            if ($existing === null) {
                $formId = DB::table($this->formsTable)->insertGetId([
                    'slug' => $slug,
                    ...$values,
                    'created_at' => now(),
                ]);

                $action = 'created';
            } else {
                DB::table($this->formsTable)
                    ->where('id', $existing->id)
                    ->update($values);

                $formId = $existing->id;
                $action = 'updated';
            }

            $this->forms[$naturalKey] = $formId;

            $this->components->twoColumnDetail(
                "Form {$slug}",
                $action,
            );
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $workflows
     */
    private function seedWorkflows(array $workflows): void
    {
        foreach ($workflows as $workflow) {
            $naturalKey = (string) $workflow['natural_key'];

            /*
             * flow_workflows has no code or slug column.
             *
             * Therefore generate a stable UUID from the natural key. The same
             * bundle and natural key always produce the same workflow ID.
             */
            $workflowId = (string) Str::uuid5(
                self::WORKFLOW_UUID_NAMESPACE,
                $naturalKey,
            );

            $definition = $workflow['definition'] ?? [];

            /*
             * Preserve the natural key inside definition metadata too.
             */
            $definition['seed'] = array_replace(
                $definition['seed'] ?? [],
                [
                    'bundle_key' => $naturalKey,
                    'managed_by' => self::class,
                ],
            );

            $existing = DB::table($this->workflowsTable)
                ->where('id', $workflowId)
                ->exists();

            DB::table($this->workflowsTable)->updateOrInsert(
                ['id' => $workflowId],
                [
                    'tenant_type' => $workflow['tenant_type'] ?? null,
                    'tenant_id' => $workflow['tenant_id'] ?? null,
                    'name' => $workflow['name'],
                    'description' => $workflow['description'] ?? null,
                    'definition' => $this->json($definition),
                    'is_active' => (bool) (
                        $workflow['is_active'] ?? false
                    ),
                    'prune_after_days' => $workflow['prune_after_days'] ?? null,
                    'max_consecutive_failures' => $workflow['max_consecutive_failures'] ?? null,
                    'consecutive_failures' => 0,
                    'on_failure_workflow_id' => null,
                    'created_by' => $workflow['created_by'] ?? 'rimba:seed-service',
                    'updated_by' => $workflow['updated_by'] ?? 'rimba:seed-service',
                    'created_at' => $existing
                        ? DB::raw('created_at')
                        : now(),
                    'updated_at' => now(),
                ],
            );

            $this->seedWorkflowVersion(
                workflowId: $workflowId,
                definition: $definition,
                createdBy: (string) (
                    $workflow['updated_by']
                    ?? $workflow['created_by']
                    ?? 'rimba:seed-service'
                ),
            );

            $this->workflows[$naturalKey] = $workflowId;

            $this->components->twoColumnDetail(
                "Workflow {$naturalKey}",
                $existing ? 'updated' : 'created',
            );
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function seedWorkflowVersion(
        string $workflowId,
        array $definition,
        string $createdBy,
    ): void {
        $encodedDefinition = $this->json($definition);

        $latest = DB::table($this->workflowVersionsTable)
            ->where('workflow_id', $workflowId)
            ->orderByDesc('number')
            ->first();

        /*
         * Avoid creating a new version when the definition is unchanged.
         */
        if (
            $latest !== null
            && $this->canonicalJson($latest->definition)
                === $this->canonicalJson($encodedDefinition)
        ) {
            return;
        }

        if ($this->option('fresh')) {
            DB::table($this->workflowVersionsTable)
                ->where('workflow_id', $workflowId)
                ->delete();

            $versionNumber = 1;
        } else {
            $versionNumber = ((int) ($latest->number ?? 0)) + 1;
        }

        DB::table($this->workflowVersionsTable)->insert([
            'id' => (string) Str::uuid(),
            'workflow_id' => $workflowId,
            'number' => $versionNumber,
            'definition' => $encodedDefinition,
            'created_by' => $createdBy,
            'created_at' => now(),
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $triggers
     */
    private function seedWorkflowTriggers(array $triggers): void
    {
        $grouped = collect($triggers)->groupBy(
            fn (array $trigger): string => $this->referenceKey(
                value: $trigger['workflow'] ?? null,
                prefix: '@workflow:',
            ),
        );

        foreach ($grouped as $workflowKey => $workflowTriggers) {
            $workflowId = $this->workflowId($workflowKey);

            /*
             * The migration does not define a uniqueness constraint for
             * workflow_id + node_id + type. Replace the bundle-managed
             * trigger set to keep this command idempotent.
             */
            DB::table($this->workflowTriggersTable)
                ->where('workflow_id', $workflowId)
                ->delete();

            foreach ($workflowTriggers as $workflowTrigger) {
                DB::table($this->workflowTriggersTable)->insert([
                    'id' => (string) Str::uuid(),
                    'workflow_id' => $workflowId,
                    'node_id' => $workflowTrigger['node_id'],
                    'type' => $workflowTrigger['type'],
                    'config' => $this->nullableJson(
                        $workflowTrigger['config'] ?? null,
                    ),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->components->twoColumnDetail(
                "Triggers {$workflowKey}",
                (string) $workflowTriggers->count(),
            );
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $offerings
     */
    private function seedOfferings(array $offerings): void
    {
        foreach ($offerings as $offering) {
            $code = (string) $offering['code'];

            $orgUnitId = $this->resolveOrgUnit(
                value: $offering['org_unit_id'],
            );

            $orgTeamId = $this->resolveOrgTeam(
                value: $offering['org_team_id'] ?? null,
                orgUnitId: $orgUnitId,
            );

            $formId = $this->resolveForm(
                value: $offering['form_id'] ?? null,
            );

            $workflowId = $this->resolveWorkflow(
                value: $offering['workflow_id'] ?? null,
            );

            $existing = DB::table('req_offerings')
                ->where('code', $code)
                ->first();

            $values = [
                'name' => $offering['name'],
                'description' => $offering['description'] ?? null,
                'org_unit_id' => $orgUnitId,
                'org_team_id' => $orgTeamId,
                'form_id' => $formId,
                'workflow_id' => $workflowId,
                'visibility' => $offering['visibility'] ?? 'internal',
                'availability' => $offering['availability'] ?? 'draft',
                'icon' => $offering['icon'] ?? null,
                'color' => $offering['color'] ?? null,
                'sort' => (int) ($offering['sort'] ?? 0),
                'settings' => $this->nullableJson(
                    $offering['settings'] ?? null,
                ),
                'updated_at' => now(),
            ];

            if ($existing === null) {
                DB::table('req_offerings')->insert([
                    'id' => (string) Str::uuid(),
                    'code' => $code,
                    ...$values,
                    'created_at' => now(),
                ]);

                $action = 'created';
            } else {
                DB::table('req_offerings')
                    ->where('id', $existing->id)
                    ->update($values);

                $action = 'updated';
            }

            $this->components->twoColumnDetail(
                "Offering {$code}",
                $action,
            );
        }
    }

    private function resolveOrgUnit(mixed $value): int
    {
        $key = $this->referenceKey(
            value: $value,
            prefix: '@org_unit:',
        );

        if (isset($this->orgUnits[$key])) {
            return $this->orgUnits[$key];
        }

        /*
         * org_units has code, uuid, and name. Prefer code, then UUID, then
         * exact name. A missing unit is considered a seed-data error.
         */
        $orgUnit = DB::table('org_units')
            ->where('code', $key)
            ->orWhere('uuid', $key)
            ->orWhere('name', $key)
            ->first();

        if ($orgUnit === null) {
            throw new RuntimeException(
                "Organizational unit [{$key}] was not found.",
            );
        }

        return $this->orgUnits[$key] = (int) $orgUnit->id;
    }

    private function resolveOrgTeam(
        mixed $value,
        int $orgUnitId,
    ): ?int {
        if ($value === null || $value === '') {
            return null;
        }

        $key = $this->referenceKey(
            value: $value,
            prefix: '@org_team:',
        );

        $cacheKey = "{$orgUnitId}:{$key}";

        if (isset($this->orgTeams[$cacheKey])) {
            return $this->orgTeams[$cacheKey];
        }

        /*
         * Team codes are resolved inside the selected organizational unit.
         * This prevents assigning another unit's same-named team.
         */
        $orgTeam = DB::table('org_teams')
            ->where('org_unit_id', $orgUnitId)
            ->where(function ($query) use ($key): void {
                $query
                    ->where('code', $key)
                    ->orWhere('name', $key);
            })
            ->first();

        if ($orgTeam === null) {
            throw new RuntimeException(
                "Organizational team [{$key}] was not found "
                ."under org unit ID [{$orgUnitId}].",
            );
        }

        return $this->orgTeams[$cacheKey] = (int) $orgTeam->id;
    }

    private function resolveForm(mixed $value): int|string|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        $key = $this->referenceKey(
            value: $value,
            prefix: '@form:',
        );

        if (isset($this->forms[$key])) {
            return $this->forms[$key];
        }

        $formId = DB::table($this->formsTable)
            ->where('slug', $key)
            ->value('id');

        if ($formId === null) {
            throw new RuntimeException(
                "Form [{$key}] was not found.",
            );
        }

        return $this->forms[$key] = $formId;
    }

    private function resolveWorkflow(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $key = $this->referenceKey(
            value: $value,
            prefix: '@workflow:',
        );

        return $this->workflowId($key);
    }

    private function workflowId(string $naturalKey): string
    {
        if (isset($this->workflows[$naturalKey])) {
            return $this->workflows[$naturalKey];
        }

        $workflowId = (string) Str::uuid5(
            self::WORKFLOW_UUID_NAMESPACE,
            $naturalKey,
        );

        if (
            ! DB::table($this->workflowsTable)
                ->where('id', $workflowId)
                ->exists()
        ) {
            throw new RuntimeException(
                "Workflow [{$naturalKey}] was not found.",
            );
        }

        return $this->workflows[$naturalKey] = $workflowId;
    }

    private function referenceKey(
        mixed $value,
        string $prefix,
    ): string {
        if (! is_string($value) || blank($value)) {
            throw new RuntimeException(
                "Expected reference starting with [{$prefix}].",
            );
        }

        if (! Str::startsWith($value, $prefix)) {
            throw new RuntimeException(
                "Reference [{$value}] must start with [{$prefix}].",
            );
        }

        $key = Str::after($value, $prefix);

        if (blank($key)) {
            throw new RuntimeException(
                "Reference [{$value}] does not contain a key.",
            );
        }

        return $key;
    }

    /**
     * @throws JsonException
     */
    private function json(mixed $value): string
    {
        return json_encode(
            $value,
            JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_THROW_ON_ERROR,
        );
    }

    /**
     * @throws JsonException
     */
    private function nullableJson(mixed $value): ?string
    {
        return $value === null
            ? null
            : $this->json($value);
    }

    /**
     * Normalize JSON before comparing workflow definitions.
     *
     * @throws JsonException
     */
    private function canonicalJson(mixed $value): string
    {
        if (is_string($value)) {
            $value = json_decode(
                $value,
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        }

        return $this->json($this->sortRecursively($value));
    }

    private function sortRecursively(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (Arr::isAssoc($value)) {
            ksort($value);
        }

        foreach ($value as $key => $item) {
            $value[$key] = $this->sortRecursively($item);
        }

        return $value;
    }
}

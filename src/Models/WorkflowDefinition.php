<?php

declare(strict_types=1);

namespace Rimba\Workflow\Models;

use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;

#[WithoutIncrementing]
#[WithoutTimestamps]
final class WorkflowDefinition extends Model
{
    /**
     * Workflow slug is the logical primary key.
     */
    protected $primaryKey = 'slug';

    /**
     * Slugs are strings, for example:
     * hr.workforce.recruitment
     */
    protected $keyType = 'string';

    /**
     * JSON workflow definitions may contain package-specific metadata,
     * so allow all definition attributes to be filled.
     */
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'states' => 'array',
            'transitions' => 'array',
            'activities' => 'array',
            'start_form' => 'array',
            'inputs' => 'array',
            'outputs' => 'array',
            'initiator_roles' => 'array',
            'owner_roles' => 'array',
            'participant_roles' => 'array',
            'approver_roles' => 'array',
            'published' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Prevent accidental database persistence.
     *
     * WorkflowDefinitionRepository must be used instead.
     */
    public function save(array $options = []): bool
    {
        throw new \LogicException(
            'WorkflowDefinition is JSON-backed. Use WorkflowDefinitionRepository::save().'
        );
    }

    public function delete(): ?bool
    {
        throw new \LogicException(
            'WorkflowDefinition is JSON-backed. Use WorkflowDefinitionRepository::delete().'
        );
    }

    /**
     * Return data suitable for writing back to the JSON definition.
     */
    public function toDefinitionArray(): array
    {
        $data = $this->attributesToArray();

        return array_filter(
            $data,
            static fn (mixed $value): bool => $value !== null
        );
    }

    /**
     * Create a persisted virtual model from JSON data.
     */
    public static function fromDefinition(array $data): self
    {
        $model = new self;

        $model->setRawAttributes($data, true);
        $model->exists = true;

        return $model;
    }

    /**
     * Create a new, unsaved JSON definition model.
     */
    public static function makeDefinition(array $data = []): self
    {
        $model = new self($data);

        $model->exists = false;

        return $model;
    }
}

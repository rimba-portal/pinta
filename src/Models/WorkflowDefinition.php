<?php

declare(strict_types=1);

namespace Rimba\Workflow\Models;

use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use LogicException;

#[WithoutIncrementing]
#[WithoutTimestamps]
final class WorkflowDefinition extends Model
{
    /**
     * Workflow slug is the logical primary key.
     */
    protected $primaryKey = 'slug';

    /**
     * Workflow slugs are strings.
     */
    protected $keyType = 'string';

    /**
     * Allow workflow definition attributes to be filled.
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
     * Use WorkflowDefinitionRepository::save() instead.
     */
    public function save(array $options = []): bool
    {
        throw new LogicException(
            'WorkflowDefinition is JSON-backed. '
            .'Use WorkflowDefinitionRepository::save().'
        );
    }

    /**
     * Prevent accidental database deletion.
     *
     * Use WorkflowDefinitionRepository::delete() instead.
     */
    public function delete(): ?bool
    {
        throw new LogicException(
            'WorkflowDefinition is JSON-backed. '
            .'Use WorkflowDefinitionRepository::delete().'
        );
    }

    /**
     * Return decoded definition data suitable for writing to JSON.
     */
    public function toDefinitionArray(): array
    {
        return array_filter(
            $this->attributesToArray(),
            static fn (mixed $value): bool => $value !== null,
        );
    }

    /**
     * Create an existing virtual model from decoded JSON data.
     *
     * Do not use setRawAttributes() here.
     *
     * The decoded JSON contains actual PHP arrays. Calling fill()
     * passes the values through Eloquent's attribute setters so array
     * casts are stored internally in the expected raw JSON format.
     */
    public static function fromDefinition(array $data): self
    {
        $model = new self;

        $model->fill($data);

        $model->exists = true;
        $model->wasRecentlyCreated = false;

        $model->syncOriginal();

        return $model;
    }

    /**
     * Create a new unsaved virtual model.
     */
    public static function makeDefinition(array $data = []): self
    {
        $model = new self;

        $model->fill($data);

        $model->exists = false;
        $model->wasRecentlyCreated = false;

        return $model;
    }
}

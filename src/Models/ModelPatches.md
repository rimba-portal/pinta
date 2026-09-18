# Required model merges

## WorkflowInstance
Add `use HasPintaExecutionRelations;` inside the class.

## ActivityInstance
Add:
```php
public function assignments(): HasMany
{
    return $this->hasMany(config('pinta.models.activity_assignment'), 'activity_instance_id');
}
```

## WorkflowEngine::start()
Resolve the typed definition through `DefinitionRegistry`, authorize `init.{slug}` at the calling boundary, persist `owner_team_*` if resolved by the host, then call `CreateWorkflowActivities` after creating the instance.

## WorkflowEngine::perform()
Lock the instance row with `lockForUpdate()`, authorize the actor, validate optional transition guards, emit terminal lifecycle events, and activate activities associated with the destination state.

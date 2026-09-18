<?php
declare(strict_types=1);
namespace Rimba\Workflow\Definitions;

final readonly class WorkflowDefinition
{
    public function __construct(
        public string $slug,
        public string $title,
        public string $template,
        public int $version,
        public string $initialState,
        public array $states,
        public array $transitions,
        public array $activities,
        public array $startForm,
        public array $inputs,
        public array $outputs,
        public array $initiatorRoles,
        public array $ownerRoles,
        public array $participantRoles,
        public array $approverRoles,
        public ?string $ownerTeamSlug,
        public array $raw,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            slug: $data['slug'], title: $data['title'], template: $data['template'] ?? 'approval_workflow',
            version: (int) ($data['version'] ?? 1), initialState: $data['initial_state'], states: $data['states'],
            transitions: $data['transitions'] ?? [], activities: $data['activities'] ?? [], startForm: $data['start_form'] ?? [],
            inputs: $data['inputs'] ?? [], outputs: $data['outputs'] ?? [], initiatorRoles: $data['initiator_roles'] ?? [],
            ownerRoles: $data['owner_roles'] ?? [], participantRoles: $data['participant_roles'] ?? [],
            approverRoles: $data['approver_roles'] ?? [], ownerTeamSlug: $data['owner_team_slug'] ?? null, raw: $data,
        );
    }

    public function permission(string $ability): string { return $ability.'.'.$this->slug; }
    public function availableTransitions(string $state): array { return array_values(array_filter($this->transitions, fn(array $t) => $t['from'] === $state)); }
}

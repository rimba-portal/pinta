<?php

declare(strict_types=1);

namespace Rimba\Request\Flow\Actions;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Packstub\Flow\Nodes\Actions\RequestApproval;
use Packstub\Flow\Support\Placeholders;
use Packstub\Flow\Support\WaitRequest;
use Rimba\Request\Contracts\ResolvesRoleApprovers;
use RuntimeException;

final class AssignActionToRoles extends RequestApproval
{
    public function getName(): string
    {
        return 'Assign Action to Roles';
    }

    public function getDescription(): string
    {
        return 'Pauses the workflow until an eligible member of one of the selected roles approves or rejects the action.';
    }

    public function getIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('roles')
                ->label('Eligible Roles')
                ->helperText(
                    'Any eligible staff member holding one of these roles may complete the action.',
                )
                ->options(
                    fn (): array => app(
                        ResolvesRoleApprovers::class,
                    )->roleOptions(),
                )
                ->multiple()
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('title')
                ->label('Action Title')
                ->placeholder(
                    'Review {{ model.document.code }} revision {{ model.revision }}',
                )
                ->required()
                ->maxLength(255),

            Textarea::make('body')
                ->label('Instructions')
                ->placeholder(
                    'Review the document revision for completeness, accuracy and suitability.',
                )
                ->rows(4),

            TextInput::make('timeout')
                ->label('Timeout')
                ->helperText(
                    'Use 0 to wait indefinitely.',
                )
                ->numeric()
                ->minValue(0)
                ->default(0),

            Select::make('timeout_unit')
                ->label('Timeout Unit')
                ->options([
                    'minutes' => 'Minutes',
                    'hours' => 'Hours',
                    'days' => 'Days',
                ])
                ->default('hours')
                ->required(),

            Toggle::make('notify')
                ->label('Send Database Notification')
                ->helperText(
                    'Creates a Filament database notification for eligible users.',
                )
                ->default(true),

            Toggle::make('email')
                ->label('Send Email')
                ->helperText(
                    'Requires a configured Laravel mailer. Leave disabled when SMTP is unavailable.',
                )
                ->default(false),
        ];
    }

    public function getPlaceholders(): array
    {
        return [
            ...Placeholders::documentation(),

            '{{ approval.outcome }}' => 'The approval outcome.',

            '{{ approval.by }}' => 'The email of the user who completed the action.',

            '{{ approval.comment }}' => 'The comment entered when completing the action.',
        ];
    }

    public function createWait(
        array $config,
        array $payload,
    ): WaitRequest {
        $roles = array_values(
            array_filter(
                array_map(
                    static fn (mixed $role): string => trim((string) $role),
                    (array) ($config['roles'] ?? []),
                ),
            ),
        );

        if ($roles === []) {
            throw new RuntimeException(
                'Assign Action to Roles requires at least one eligible role.',
            );
        }

        $approvers = app(
            ResolvesRoleApprovers::class,
        )->emailsForRoles(
            roles: $roles,
            payload: $payload,
        );

        $approvers = array_values(
            array_unique(
                array_filter(
                    array_map(
                        static fn (string $email): string => strtolower(trim($email)),
                        $approvers,
                    ),
                    static fn (string $email): bool => filter_var(
                        $email,
                        FILTER_VALIDATE_EMAIL,
                    ) !== false,
                ),
            ),
        );

        if ($approvers === []) {
            throw new RuntimeException(
                'No eligible authenticated users were found for the selected roles.',
            );
        }

        $timeout = (int) ($config['timeout'] ?? 0)
            * match ($config['timeout_unit'] ?? 'hours') {
                'minutes' => 60,
                'days' => 86400,
                default => 3600,
            };

        $model = $payload['model'] ?? null;

        return new WaitRequest(
            type: 'approval',
            outcomes: [
                'approved',
                'rejected',
            ],
            timeoutSeconds: $timeout > 0
                ? $timeout
                : null,
            meta: [
                'title' => $this->interpolate(
                    $config['title'] ?? '',
                    $payload,
                ),

                'body' => $this->interpolate(
                    $config['body'] ?? '',
                    $payload,
                ),

                /*
                 * Packstub expects approval identities as email
                 * addresses even though the node is configured
                 * using Rimba roles.
                 */
                'approvers' => $approvers,

                /*
                 * Retain the selected roles for audit/display.
                 * Packstub ignores additional metadata safely.
                 */
                'roles' => $roles,

                'subject' => is_object($model)
                    ? class_basename($model)
                        .' #'
                        .Placeholders::stringify($model)
                    : null,
            ],
        );
    }
}

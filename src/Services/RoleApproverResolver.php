<?php

declare(strict_types=1);

namespace Rimba\Request\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Request\Contracts\ResolvesRoleApprovers;

final class RoleApproverResolver implements ResolvesRoleApprovers
{
    public function roleOptions(): array
    {
        /*
         * Replace the table/column source here with the canonical
         * Rimba role catalogue query.
         *
         * Keys should be stable role names or codes, not numeric IDs.
         */
        return DB::table('roles')
            ->orderBy('name')
            ->pluck('name', 'name')
            ->all();
    }

    public function emailsForRoles(
        array $roles,
        array $payload,
    ): array {
        $model = $payload['model'] ?? null;

        /*
         * Extract workflow scope from the triggering model.
         *
         * For DocumentRevision:
         *
         * DocumentRevision
         *     -> document
         *     -> owner / OrgTeam context
         */
        $this->orgUnitId($model);
        $this->orgTeamId($model);

        /*
         * IMPORTANT:
         *
         * Replace this query with your real Staff-role assignment
         * relationships/tables. The method must finally return email
         * addresses belonging to Flow::userModel().
         */
        return $this->resolveEmails();
    }

    private function resolveEmails(): array
    {
        /*
         * This must be implemented against your actual:
         *
         * Staff
         * Role
         * Staff-role pivot
         * User mapping
         * OrgUnit/OrgTeam scope
         *
         * Deliberately not guessed here.
         */
        return [];
    }

    private function orgUnitId(
        mixed $model,
    ): int|string|null {
        if (! $model instanceof Model) {
            return null;
        }

        return data_get(
            $model,
            'org_unit_id',
            data_get(
                $model,
                'document.org_unit_id',
            ),
        );
    }

    private function orgTeamId(
        mixed $model,
    ): int|string|null {
        if (! $model instanceof Model) {
            return null;
        }

        return data_get(
            $model,
            'org_team_id',
            data_get(
                $model,
                'document.org_team_id',
            ),
        );
    }
}

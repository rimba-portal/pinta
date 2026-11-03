<?php

declare(strict_types=1);

namespace Rimba\Request\Contracts;

interface ResolvesRoleApprovers
{
    /**
     * Options displayed in the Packstub node configuration.
     *
     * @return array<string, string>
     */
    public function roleOptions(): array;

    /**
     * Resolve selected role names/codes to authenticated-user emails.
     *
     * @param  array<int, string>  $roles
     * @param  array<string, mixed>  $payload
     * @return array<int, string>
     */
    public function emailsForRoles(
        array $roles,
        array $payload,
    ): array;
}

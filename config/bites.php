<?php

declare(strict_types=1);

use Rimba\Organization\Models\OrgTeam;
use Rimba\Organization\Models\OrgUnit;
use Rimba\People\Models\Staff;
use Rimba\Request\Models\Offering;
use Rimba\Request\Models\Request;

return [
    'request' => [
        'models' => [
            'offering' => Offering::class,
            'request' => Request::class,

            'staff' => Staff::class,
            'org_unit' => OrgUnit::class,
            'org_team' => OrgTeam::class,
        ],
        'phases' => [
            'submitted' => 'oir.01',
        ],
    ],
];

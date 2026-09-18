<?php
declare(strict_types=1); namespace Rimba\Workflow\Events;
use Illuminate\Foundation\Events\Dispatchable; use Rimba\Workflow\Models\ActivityAssignment;
final class ActivityAssigned {use Dispatchable; public function __construct(public ActivityAssignment $assignment){}}

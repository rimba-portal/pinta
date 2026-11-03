<?php

declare(strict_types=1);

namespace Rimba\Request\Enums;

enum State: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Rejected = 'rejected';
    case Closed = 'closed';
    case Archived = 'archived';
}

<?php

namespace Rimba\Workflow\Enums;

enum WorkflowAction: string
{
    case Submit = 'submit';
    case Approve = 'approve';
    case Reject = 'reject';
    case Return = 'return';
    case Assign = 'assign';
    case Complete = 'complete';
    case Cancel = 'cancel';

    public function label(): string
    {
        return str($this->value)->headline()->toString();
    }
    public function color(): string
    {
        return match ($this) {
            self::Submit => 'primary',
            self::Approve, self::Complete => 'success',
            self::Reject => 'danger',
            self::Return => 'warning',
            self::Assign => 'info',
            self::Cancel => 'gray'
        };
    }
    public function icon(): string
    {
        return match ($this) {
            self::Submit => 'heroicon-o-paper-airplane',
            self::Approve => 'heroicon-o-check-circle',
            self::Reject => 'heroicon-o-x-circle',
            self::Return => 'heroicon-o-arrow-uturn-left',
            self::Assign => 'heroicon-o-user-plus',
            self::Complete => 'heroicon-o-check',
            self::Cancel => 'heroicon-o-no-symbol'
        };
    }
}

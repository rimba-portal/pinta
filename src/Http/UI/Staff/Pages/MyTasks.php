<?php

declare(strict_types=1);

namespace Rimba\Workflow\Http\UI\Staff\Pages;

use Filament\Pages\Page;
use Rimba\Workflow\Services\TaskInboxService;

class MyTasks extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected string $view = 'bites::staff.my-tasks';

    protected static string|\UnitEnum|null $navigationGroup = 'ToDo';

    public array $tasks = [];

    public function mount(TaskInboxService $inbox): void
    {
        $this->tasks = $inbox->queryFor(auth()->user())->get()->toArray();
    }
}

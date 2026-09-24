<?php

declare(strict_types=1);

namespace Rimba\Workflow\Http\UI\Team\Resources\WorkflowDefinitions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Workflow\Http\UI\Team\Resources\WorkflowDefinitions\Pages\ManageWorkflowDefinitions;
use Rimba\Workflow\Http\UI\Team\Resources\WorkflowDefinitions\Schemas\WorkflowDefinitionSchema;
use Rimba\Workflow\Http\UI\Team\Resources\WorkflowDefinitions\Tables\WorkflowDefinitionsTable;
use Rimba\Workflow\Models\WorkflowDefinition;
use UnitEnum;

final class WorkflowDefinitionResource extends Resource
{
    protected static ?string $model = WorkflowDefinition::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup =
        'Workflow Studio';

    protected static ?string $navigationLabel =
        'Workflow Definitions';

    protected static ?string $modelLabel =
        'Workflow Definition';

    protected static ?string $pluralModelLabel =
        'Workflow Definitions';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'workflow-definitions';

    public static function form(Schema $schema): Schema
    {
        return WorkflowDefinitionSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowDefinitionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWorkflowDefinitions::route('/'),
        ];
    }
}

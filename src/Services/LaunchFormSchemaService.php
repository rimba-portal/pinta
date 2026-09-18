<?php
declare(strict_types=1); namespace Rimba\Workflow\Services;
use Filament\Forms\Components\{DatePicker,Select,TextInput,Textarea,Toggle};
final class LaunchFormSchemaService {public function build(array $fields):array{return array_map(fn(array $f)=>match($f['type']??'text'){'textarea'=>Textarea::make($f['name']),'select'=>Select::make($f['name'])->options($f['options']??[]),'date'=>DatePicker::make($f['name']),'boolean'=>Toggle::make($f['name']),default=>TextInput::make($f['name'])}->label($f['label']??str($f['name'])->headline())->required((bool)($f['required']??false)),$fields);}}

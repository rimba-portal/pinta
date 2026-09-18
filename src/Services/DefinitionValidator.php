<?php
declare(strict_types=1);
namespace Rimba\Workflow\Services;

use Rimba\Workflow\Enums\WorkflowAction;

final class DefinitionValidator
{
    public function validate(array $d): array
    {
        $errors=[];
        foreach (['slug','title','initial_state','states'] as $key) if (!array_key_exists($key,$d)) $errors[]="Missing required key: {$key}.";
        if ($errors) return $errors;
        if (!isset($d['states'][$d['initial_state']])) $errors[]='initial_state must exist in states.';
        $seen=[];
        foreach ($d['transitions'] ?? [] as $i=>$t) {
            foreach (['from','action','to'] as $key) if (!isset($t[$key])) $errors[]="Transition {$i} is missing {$key}.";
            if (!isset($t['from'],$t['to'],$t['action'])) continue;
            if (!isset($d['states'][$t['from']])) $errors[]="Transition {$i} has unknown from state {$t['from']}.";
            if (!isset($d['states'][$t['to']])) $errors[]="Transition {$i} has unknown to state {$t['to']}.";
            if (WorkflowAction::tryFrom($t['action']) === null) $errors[]="Transition {$i} has unsupported action {$t['action']}.";
            $key=$t['from'].'|'.$t['action']; if(isset($seen[$key])) $errors[]="Duplicate transition {$key}."; $seen[$key]=true;
        }
        foreach ($d['activities'] ?? [] as $i=>$a) if (!isset($a['slug'],$a['state'])) $errors[]="Activity {$i} requires slug and state.";
        return $errors;
    }

    public function assert(array $definition): void
    {
        $errors=$this->validate($definition);
        if ($errors) throw new \InvalidArgumentException(implode(PHP_EOL,$errors));
    }
}

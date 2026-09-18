# Rimba Pinta

Enterprise Workflow Engine and Execution Platform for Rimba.

---

# Overview

Pinta is the execution layer of the Rimba ecosystem.

Pinta orchestrates:

- Activities
- Workflows
- WorkPackages
- Events
- Dependencies
- Resources
- Authorizations
- Cost Centers

Pinta follows a JSON-first architecture.

Definitions are stored in source control.

Runtime instances are stored in the database.

---

# Core Concepts

## Activity

Atomic unit of work.

Examples:

- Approve Request
- Assign Laptop
- Conduct Interview
- Capture Signature
- Inspect Product

Characteristics:

- Atomic
- Executable
- Assignable
- Auditable
- Produces an outcome

---

## Workflow

Reusable business capability.

Examples:

- Recruitment
- Hiring
- Purchase Requisition
- Access Request
- Maintenance Request
- Safety Incident

Characteristics:

- Reusable
- Business-owned
- State driven
- Composed of activities

---

## WorkPackage

Business outcome.

Examples:

- Staff Onboarding
- Staff Offboarding
- New Product Introduction
- ERP Upgrade
- Plant Expansion

Characteristics:

- Coordinates workflows
- Handles dependencies
- Tracks completion
- Represents a business outcome

---

# Architecture

WorkPackage
    ↓
Workflow
    ↓
Activity

---

# Workflow Actions

The MVP supports seven standard actions.

| Action | Meaning |
|----------|----------|
| submit | Move work into next step |
| approve | Accept and proceed |
| reject | Reject and stop |
| return | Send back for rework |
| assign | Assign ownership |
| complete | Finish execution |
| cancel | Cancel workflow |

---

# Namespace

```php
Rimba\Workflow\
```

---

# Installation

## Requirements

- PHP 8.3+
- Laravel 11+
- Rimba Asas 1.3+

---

## Install

```bash
composer require rimba/pinta:@alpha
```

---

## Publish Configuration

```bash
php artisan vendor:publish --tag=pinta-config
```

---

## Publish Definitions

```bash
php artisan vendor:publish --tag=pinta-definitions
```

Creates:

```text
definitions/

├── activities/
├── workflows/
├── workpackages/
├── checklists/
├── instructions/
├── events/
├── transitions/
└── policies/
```

---

## Publish Migrations

```bash
php artisan vendor:publish --tag=pinta-migrations
```

---

## Run Migrations

```bash
php artisan migrate
```

---

## Verify Installation

```php
app(
    \Rimba\Workflow\Contracts\WorkflowEngine::class
);
```

Expected:

```php
Rimba\Workflow\Services\WorkflowEngine
```

---

# Configuration

## config/pinta.php

```php
return [

    'definitions_path' => base_path('definitions'),

    'tables' => [

        'workflow_instances' =>
            'pinta_workflow_instances',

        'activity_instances' =>
            'pinta_activity_instances',

        'transitions' =>
            'pinta_transitions',

    ],

];
```

---

# Runtime Tables

## Workflow Instances

```text
pinta_workflow_instances
```

Stores:

- definition slug
- current state
- status
- context
- initiator
- subject

---

## Activity Instances

```text
pinta_activity_instances
```

Stores:

- activity definition
- assignee
- status
- due date
- completion date

---

## Transitions

```text
pinta_transitions
```

Stores:

- action
- from state
- to state
- actor
- timestamp
- payload

---

# Workflow Definition

Workflow definitions are stored as JSON.

Example:

```json
{
  "type": "workflow",
  "template": "approval_workflow",
  "code": "WF-HR-001",
  "slug": "hr.position_approval",
  "title": "Position Approval",
  "version": 1,

  "initial_state": "draft",

  "states": {
    "draft": {},
    "review": {},
    "completed": {},
    "rejected": {}
  },

  "transitions": [
    {
      "from": "draft",
      "action": "submit",
      "to": "review"
    },
    {
      "from": "review",
      "action": "approve",
      "to": "completed"
    },
    {
      "from": "review",
      "action": "reject",
      "to": "rejected"
    }
  ]
}
```

---

# Starting A Workflow

```php
$engine = app(
    \Rimba\Workflow\Contracts\WorkflowEngine::class
);

$instance = $engine->start(
    'hr.position_approval',
    $subject,
    auth()->user()
);
```

---

# Transition Workflow

## Submit

```php
$engine->perform(
    $instance,
    'submit',
    auth()->user()
);
```

---

## Approve

```php
$engine->perform(
    $instance,
    'approve',
    auth()->user()
);
```

---

## Reject

```php
$engine->perform(
    $instance,
    'reject',
    auth()->user()
);
```

---

## Return

```php
$engine->perform(
    $instance,
    'return',
    auth()->user()
);
```

---

## Complete

```php
$engine->perform(
    $instance,
    'complete',
    auth()->user()
);
```

---

# Available Actions

```php
$actions =
    $engine->availableActions($instance);
```

Example:

```php
[
    WorkflowAction::Approve,
    WorkflowAction::Reject,
]
```

---

# Testing

## Create Definition

```text
definitions/workflows/example.approval.json
```

```json
{
  "slug": "example.approval",

  "initial_state": "draft",

  "states": {
    "draft": {},
    "review": {},
    "completed": {},
    "rejected": {}
  },

  "transitions": [
    {
      "from": "draft",
      "action": "submit",
      "to": "review"
    },
    {
      "from": "review",
      "action": "approve",
      "to": "completed"
    },
    {
      "from": "review",
      "action": "reject",
      "to": "rejected"
    }
  ]
}
```

---

## Start

```php
$instance =
    $engine->start(
        'example.approval',
        $subject,
        auth()->user()
    );
```

Expected:

```php
draft
```

---

## Submit

```php
$engine->perform(
    $instance,
    'submit'
);
```

Expected:

```php
review
```

---

## Approve

```php
$engine->perform(
    $instance,
    'approve'
);
```

Expected:

```php
completed
```

---

## Verify Audit Trail

```php
$instance->transitions;
```

Expected:

```text
submit
approve
```

---

# Filament Integration

## Import

```php
use Rimba\Workflow\Actions\Buttons;
```

---

## Single Action

```php
Buttons::make('approve');
```

---

## All Standard Actions

```php
protected function getHeaderActions(): array
{
    return Buttons::all();
}
```

---

## Manual Buttons

```php
[
    Buttons::make('submit'),
    Buttons::make('approve'),
    Buttons::make('reject'),
    Buttons::make('return'),
    Buttons::make('assign'),
    Buttons::make('complete'),
    Buttons::make('cancel'),
]
```

---

# Canonical Workflow Lifecycle

```text
Trigger
    ↓
Qualification
    ↓
Authorization
    ↓
Planning
    ↓
Assignment
    ↓
Execution
    ↓
Verification
    ↓
Completion
    ↓
Audit
```

---

# WorkPackage Philosophy

WorkPackages coordinate workflows.

Workflows execute activities.

Activities perform work.

Example:

```text
Staff Onboarding

├── HR Onboarding
├── Email Request
├── Access Request
├── Laptop Request
├── Payroll Enrollment
└── Training Enrollment
```

---

# Event Driven Architecture

```text
State Change
      ↓
Event
      ↓
Handler
      ↓
Action
```

Examples:

```text
workflow.completed
workflow.cancelled
activity.completed
staff.hired
staff.terminated
```

# Permission convention
- `init.{workflow-slug}`: start a workflow
- `work.{workflow-slug}`: execute assigned work
- `view.{workflow-slug}`: inspect an instance
- `own.{workflow-slug}`: administer the definition in Team Panel

`PermissionSynchronizer` is intentionally an integration contract. Bind it to the concrete `rimba/boleh` permission API in the host application. The included null implementation keeps Pinta installable without guessing Boleh internals.

---

# Future Roadmap

## Phase 1

Completed

- Definition Repository
- Workflow Runtime
- State Machine
- Transition Engine
- Audit Trail
- Filament Actions

---

## Phase 2

- Activity Engine
- Assignment Engine
- Due Dates
- Comments
- Attachments

---

## Phase 3

- Event Engine
- Event Subscriptions
- Automatic Workflow Triggers

---

## Phase 4

- Dependency Engine
- Fan-In
- Fan-Out
- Sequential Dependencies
- Parallel Dependencies

---

## Phase 5

- WorkPackage Engine
- Workflow Orchestration
- Completion Rules

---

## Phase 6

- Authorization Engine
- Cost Center Governance
- Resource Allocation

---

## Phase 7

- Checklist Engine
- Work Instruction Engine

---

## Phase 8

- Analytics
- SLA Tracking
- Bottleneck Analysis
- Dashboarding

---

# Design Principles

Activity
    = Atomic Work

Workflow
    = Business Capability

WorkPackage
    = Business Outcome

Pinta
    = Enterprise Execution Platform

---

# License

MIT
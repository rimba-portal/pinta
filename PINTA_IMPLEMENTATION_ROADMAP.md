# PINTA_IMPLEMENTATION_ROADMAP.md

## Purpose

This document serves as the master implementation roadmap for **rimba/pinta**.

It captures the intended architecture, implementation order, package responsibilities, and guiding principles so future design discussions remain aligned.

---

# Vision

Pinta is not merely a workflow engine.

Pinta is the enterprise execution layer for Rimba.

Its purpose is to orchestrate:

- Activities
- Workflows
- WorkPackages
- Resources
- Events
- Authorizations
- Dependencies

across the entire TOS ecosystem.

---

# Core Architecture

```text
WorkPackage
    ↓
Workflow
    ↓
Activity
```

---

## Activity

The smallest executable unit.

Examples:

```text
Approve Request
Assign Laptop
Conduct Interview
Inspect Product
Create User Account
```

Characteristics:

- Atomic
- Executable
- Assignable
- Auditable
- Produces an outcome

---

## Workflow

A reusable business capability.

Examples:

```text
Recruitment
Hiring
Purchase Requisition
Access Request
Laptop Request
Safety Incident
Payroll Processing
```

Characteristics:

- Reusable
- Business-owned
- State driven
- Composed of activities

---

## WorkPackage

A workflow orchestration layer.

Examples:

```text
Staff Onboarding
Staff Offboarding
New Product Introduction
Plant Expansion
Office Relocation
Organization Restructuring
Disaster Recovery
```

Characteristics:

- Coordinates workflows
- Handles dependencies
- Tracks overall completion
- Represents a business outcome

---

# Guiding Principles

## Principle 1

Activities execute work.

```text
Activity
    = Action
```

---

## Principle 2

Workflows deliver capabilities.

```text
Workflow
    = Business Capability
```

---

## Principle 3

WorkPackages deliver outcomes.

```text
WorkPackage
    = Business Outcome
```

---

## Principle 4

Workflow definitions are stored as JSON.

Runtime instances are stored in the database.

---

## Principle 5

WorkPackage definitions are stored as JSON.

Runtime orchestration state is stored in the database.

---

## Principle 6

Everything should be event-driven.

```text
State Changes
    ↓
Events
    ↓
Triggers
    ↓
Actions
```

---

# Folder Structure

```text
definitions/

├── activities/
│
├── workflows/
│
├── workpackages/
│
├── checklists/
│
├── instructions/
│
├── events/
│
├── transitions/
│
└── policies/
```

---

# Canonical Workflow Lifecycle

Every workflow is constructed from the following canonical states.

```text
Trigger
Qualification
Authorization
Planning
Assignment
Execution
Verification
Completion
Audit
```

---

## State Definitions

| State | Purpose |
|---------|---------|
| Trigger | Workflow initiation |
| Qualification | Validate request |
| Authorization | Approval or governance |
| Planning | Determine execution |
| Assignment | Assign responsibility |
| Execution | Perform work |
| Verification | Confirm outcome |
| Completion | Close workflow |
| Audit | Traceability and retention |

---

# Implementation Phases

---

# Phase 0 — Foundation

## Objective

Finalize core architecture.

## Deliverables

```text
Activity
Workflow
WorkPackage
Checklist
WorkInstruction
Event
Trigger
Dependency
```

## Output

```text
Architecture Baseline
```

---

# Phase 1 — Activity Engine

## Objective

Implement atomic execution.

## Models

```text
ActivityDefinition
ActivityInstance
```

## Features

```text
Assignment
Status Tracking
Comments
Attachments
Due Dates
Escalation
```

## Output

```text
Task Runtime Engine
```

---

# Phase 2 — Workflow Engine

## Objective

Implement reusable business capabilities.

## Models

```text
WorkflowDefinition
WorkflowInstance
WorkflowState
WorkflowTransition
```

## Features

```text
Start Workflow
Advance State
Reject State
Cancel Workflow
Resume Workflow
```

## Output

```text
Workflow Runtime Engine
```

---

# Phase 3 — Event Engine

## Objective

Enable event-driven execution.

## Models

```text
EventDefinition
EventInstance
EventSubscription
```

## Examples

```text
workflow.completed
workflow.cancelled
activity.completed
staff.hired
staff.terminated
```

## Output

```text
Enterprise Event Bus
```

---

# Phase 4 — Dependency Engine

## Objective

Support workflow relationships.

## Dependency Types

```text
Finish-To-Start
Start-To-Start
Finish-To-Finish
```

## Features

```text
Blocked
Ready
Running
Waiting
Completed
```

## Output

```text
Dependency Management Engine
```

---

# Phase 5 — WorkPackage Engine

## Objective

Implement orchestration.

## Models

```text
WorkPackageDefinition
WorkPackageInstance
WorkPackageNode
```

## Features

```text
Start Child Workflow
Track Child Status
Monitor Dependencies
Aggregate Progress
Determine Completion
```

## Output

```text
Workflow Orchestration Engine
```

---

# Phase 6 — Graph Engine

## Objective

Visualize orchestration and dependencies.

## Visualizations

```text
Workflow Graph
Dependency Graph
Execution Graph
WorkPackage Graph
```

## Output

```text
Workflow Designer
Workflow Viewer
```

---

# Phase 7 — Checklist Engine

## Objective

Handle unordered work.

## Principles

Checklist ≠ Workflow

Checklist items are independent.

## Output

```text
Checklist Runtime
```

---

# Phase 8 — Work Instruction Engine

## Objective

Standardize execution.

## Example

```text
Issue Laptop

1. Retrieve Asset
2. Scan Asset
3. Assign Asset
4. Capture Acknowledgement
```

## Output

```text
Work Instruction Runtime
```

---

# Phase 9 — Authorization Engine

## Objective

Centralize approvals.

## Authorization Types

```text
Manager Approval
Department Approval
Cost Center Approval
Role Approval
Committee Approval
```

## Output

```text
Authorization Framework
```

---

# Phase 10 — Cost Center Engine

## Objective

Support financial governance.

## Cost Types

```text
budget_check
cost_commitment
cost_transfer
cost_realization
informational
```

## Output

```text
Cost Center Governance
```

---

# Phase 11 — Resource Engine

## Objective

Manage resource allocations.

## Resources

```text
People
Organizations
Teams
Assets
Equipment
Contractors
Locations
```

## Output

```text
Resource Allocation Engine
```

---

# Phase 12 — SLA Engine

## Objective

Track commitments.

## Features

```text
Due Dates
Reminders
Escalation
Breach Detection
```

## Output

```text
SLA Management
```

---

# Phase 13 — Audit Engine

## Objective

Provide compliance-grade traceability.

## Audit Data

```text
Who
What
When
Old Value
New Value
Reason
Outcome
```

## Output

```text
Immutable Audit Trail
```

---

# Phase 14 — Analytics Engine

## Objective

Provide operational intelligence.

## Metrics

```text
Lead Time
Cycle Time
Wait Time
Approval Time
SLA Compliance
Bottlenecks
```

## Output

```text
Pinta Analytics Dashboard
```

---

# Phase 15 — TOS Integration

## Objective

Make Pinta the execution layer of the TOS.

## Party

Responsible.

```text
Initiator
Owner
Participant
Approver
```

---

## Organization

Owns business workflows.

```text
Department
Division
Business Unit
```

---

## Team

Executes activities.

```text
Assigned Team
Resolver Team
Execution Team
```

---

## Offering

Exposes workflows.

Examples:

```text
Laptop Service
Recruitment Service
Training Service
Procurement Service
```

---

## Supply

Consumes and produces resources.

Examples:

```text
Laptop
PPE
Locker
Access Card
Vehicle
Software License
```

---

# Orchestration Example

## Staff Onboarding

```text
Staff Onboarding

├── Onboarding
├── Email Request
├── Access Request
├── Laptop Request
├── Locker Request
├── PPE Request
└── Payroll Enrollment
```

Parent completion rule:

```text
ALL REQUIRED CHILDREN COMPLETE
```

---

# Future Pinta JSON Schema

```json
{
  "slug": "hr.workpackage.staff_onboarding",

  "completion_rule": "all_required",

  "children": [

    {
      "workflow": "hr.workforce.onboarding",
      "required": true
    },

    {
      "workflow": "it.identity.email_request",
      "required": true
    },

    {
      "workflow": "it.support.laptop_request",
      "required": true,
      "depends_on": [
        "it.identity.email_request"
      ]
    }
  ]
}
```

---

# Recommended Development Order

Build in the following sequence:

```text
1. Foundation
2. Activity Engine
3. Workflow Engine
4. Event Engine
5. Dependency Engine
6. WorkPackage Engine
7. Checklist Engine
8. Work Instruction Engine
9. Authorization Engine
10. Cost Center Engine
11. Resource Engine
12. SLA Engine
13. Audit Engine
14. Analytics Engine
15. TOS Integration
16. Graph Designer
```

---

# Final Architectural Decision

The future of Pinta is:

```text
Activity
    = Atomic Work

Workflow
    = Reusable Business Capability

WorkPackage
    = Orchestration Layer

Pinta
    = Enterprise Execution Platform
```

WorkPackages orchestrate Workflow instances.

Workflows execute Activities.

Activities perform work.

This architecture should be used as the baseline reference for all future Pinta design and implementation discussions.
``
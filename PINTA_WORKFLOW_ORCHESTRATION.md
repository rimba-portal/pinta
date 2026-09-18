# PINTA_WORKFLOW_ORCHESTRATION.md

# Purpose

This document defines the orchestration philosophy of Rimba Pinta.

Its purpose is to explain:

- Activities
- Workflows
- WorkPackages
- Events
- Dependencies
- Fan-Out
- Fan-In
- Parallel Execution
- Resource Coordination

This document is the conceptual bridge between workflow templates and implementation.

---

# Core Principle

Pinta separates execution into three layers.

```text
WorkPackage
    ↓
Workflow
    ↓
Activity
```

Each layer serves a different purpose.

---

# Activity

Activities perform work.

Examples:

```text
Approve Request
Issue Laptop
Conduct Interview
Inspect Product
Capture Signature
```

Characteristics:

- Atomic
- Single owner
- Single outcome
- Single responsibility

Activities do not orchestrate anything.

---

# Workflow

Workflows deliver business capabilities.

Examples:

```text
Recruitment
Hiring
Laptop Request
Access Request
Purchase Requisition
Safety Incident
```

Characteristics:

- Reusable
- Business owned
- Executable
- State driven

Workflows execute activities.

Workflows should not coordinate other workflows.

---

# WorkPackage

WorkPackages deliver business outcomes.

Examples:

```text
Staff Onboarding
Staff Offboarding
NPI
ERP Implementation
Plant Expansion
```

Characteristics:

- Cross-functional
- Coordinates workflows
- Manages dependencies
- Tracks overall progress

---

# Golden Rule

If something coordinates workflows:

```text
It is NOT a Workflow.
It is a WorkPackage.
```

---

# Example

## Wrong

```text
Staff Onboarding

Trigger
 ↓
Create Email
 ↓
Create Access
 ↓
Issue Laptop
 ↓
Setup Payroll
```

This incorrectly makes onboarding a workflow.

---

## Correct

```text
WorkPackage

Staff Onboarding

├── Onboarding Workflow
├── Email Workflow
├── Access Workflow
├── Laptop Workflow
└── Payroll Workflow
```

Each child remains independently reusable.

---

# Workflow Orchestration

A WorkPackage orchestrates workflow instances.

```text
WorkPackage

Start Workflow A

Start Workflow B

Wait For Workflow C

Complete Package
```

---

# Orchestration Components

A WorkPackage owns:

```text
Children
Dependencies
Completion Rules
Progress Rules
Event Rules
Resource Rules
```

---

# Child Workflow

Example:

```json
{
  "workflow": "it.laptop_request"
}
```

Represents a workflow instance.

---

# Dependency

Prevent workflow execution until prerequisite workflow completes.

Example:

```json
{
  "workflow": "it.laptop_request",

  "depends_on": [
    "it.email_request"
  ]
}
```

Meaning:

```text
Email Request
     ↓
Laptop Request
```

---

# Completion Rule

Determines when package completes.

---

## All Required

```json
{
  "completion_rule": {
    "mode": "all_required_children_completed"
  }
}
```

Meaning:

```text
All required workflows must finish.
```

---

## Any Required

```json
{
  "completion_rule": {
    "mode": "any_required_child_completed"
  }
}
```

Meaning:

```text
One workflow completion is sufficient.
```

---

## Percentage Based

```json
{
  "completion_rule": {
    "mode": "percentage",
    "threshold": 80
  }
}
```

---

# Dependency Patterns

---

# Sequential

```text
A
 ↓
B
 ↓
C
```

Example:

```text
Approval
 ↓
Purchase
 ↓
Receiving
```

---

# Parallel

```text
       A

   /   |   \

  B    C    D
```

Example:

```text
Email Setup
Locker Setup
Access Setup
```

all run simultaneously.

---

# Fan-Out

One event starts multiple workflows.

```text
A

├── B
├── C
└── D
```

Example:

```text
Employee Hired

├── Create Email
├── Create Access
├── Setup Payroll
└── Issue Laptop
```

---

# Fan-In

Multiple workflows must complete before another starts.

```text
B
C
D

 ↓

 E
```

Example:

```text
Email Complete
Laptop Complete
Access Complete

 ↓

Onboarding Complete
```

---

# Event Driven Orchestration

Pinta should be event-driven.

Events trigger actions.

---

# Event Flow

```text
Activity Completed
      ↓

Workflow Event

      ↓

Package Event

      ↓

New Workflow Starts
```

---

# Event Example

```json
{
  "event": "staff.created",

  "handlers": [

    {
      "start_workflow": "it.email_request"
    },

    {
      "start_workflow": "security.access_request"
    }

  ]
}
```

---

# Staff Onboarding Example

## Business View

```text
Staff Onboarding

Create Staff

Email Account

Access Card

Laptop

Locker

Payroll

Training
```

---

## Orchestration View

```text
Staff Onboarding

├── HR Onboarding
│
├── Email Request
│
├── Access Request
│
├── Laptop Request
│
├── Locker Request
│
├── Payroll Enrollment
│
└── Training Enrollment
```

---

## Dependency View

```text
HR Onboarding
     │
     ▼

Email Request
     │
     ▼

Laptop Request

Access Request

Payroll Enrollment
```

---

## JSON Example

```json
{
  "type": "workpackage",

  "slug": "hr.staff_onboarding",

  "completion_rule": {
    "mode": "all_required_children_completed"
  },

  "children": [

    {
      "workflow": "hr.onboarding"
    },

    {
      "workflow": "it.email_request",

      "depends_on": [
        "hr.onboarding"
      ]
    },

    {
      "workflow": "it.laptop_request",

      "depends_on": [
        "it.email_request"
      ]
    },

    {
      "workflow": "security.access_request",

      "depends_on": [
        "hr.onboarding"
      ]
    }

  ]
}
```

---

# NPI Example

NPI is not a workflow.

NPI is a WorkPackage.

---

## Business View

```text
New Product Introduction
```

---

## Package View

```text
NPI

├── Process Design
├── Routing Definition
├── Product Costing
├── Validation
├── MES Configuration
├── Training
└── Production Release
```

---

## Dependency Graph

```text
Process Design
      │
      ▼

Routing Definition

 ├────────────┐
 ▼            ▼

Costing    Validation

               │
               ▼

          Training

               │
               ▼

       Production Release
```

---

## JSON Example

```json
{
  "type": "workpackage",

  "slug": "engineering.npi",

  "children": [

    {
      "workflow": "engineering.process_design"
    },

    {
      "workflow": "engineering.routing_definition",

      "depends_on": [
        "engineering.process_design"
      ]
    },

    {
      "workflow": "finance.product_costing",

      "depends_on": [
        "engineering.routing_definition"
      ]
    },

    {
      "workflow": "engineering.process_validation",

      "depends_on": [
        "engineering.routing_definition"
      ]
    }

  ]
}
```

---

# Resource Coordination

WorkPackages may coordinate resources.

Examples:

```text
Staff
Equipment
Machine
Location
Budget
```

Workflows consume resources.

Activities utilize resources.

---

# Cost Center Coordination

WorkPackages may span multiple cost centers.

Example:

```text
Requestor Cost Center

        ↓

Provider Cost Center

        ↓

Shared Delivery
```

Examples:

```text
IT Project

Engineering Services

Facility Upgrade

MES Implementation
```

---

# Future Pinta Runtime

Pinta runtime should orchestrate:

```text
Activities
Workflows
WorkPackages
Resources
Events
Dependencies
Authorizations
Cost Centers
```

---

# Final Principle

```text
Activities
    perform work

Workflows
    deliver capabilities

WorkPackages
    deliver outcomes

Events
    trigger action

Dependencies
    control execution

Pinta
    orchestrates everything
```

---

# Architecture Summary

```text
Activity
    ↓

Workflow
    ↓

WorkPackage
    ↓

Enterprise Execution
```

This document should remain the primary orchestration reference for all future Pinta development.
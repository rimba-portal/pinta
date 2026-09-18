# PINTA_TEMPLATE_LIBRARY.md

# 1. Purpose

This document defines the standard execution templates used by Rimba Pinta.

The objective is to standardize:

- Activities
- Workflows
- WorkPackages
- Work Instructions
- Events
- Dependencies
- Orchestration

across all future modules including:

- WFM
- MES
- QMS
- EHS
- DMS
- Procurement
- Facilities
- Maintenance
- Projects

---

# 2. Core Concepts

Pinta separates execution into three layers.

```text
WorkPackage
    ↓
Workflow
    ↓
Activity
```

---

## Activity

Atomic unit of work.

Examples:

```text
Approve Request
Issue Laptop
Capture Signature
Inspect Product
```

Characteristics:

- Single executor
- Single outcome
- Independently auditable

---

## Workflow

Reusable business capability.

Examples:

```text
Recruitment
Hiring
Access Request
Purchase Requisition
Maintenance Request
```

Characteristics:

- Business owned
- Reusable
- State driven
- Composed of activities

---

## WorkPackage

Business outcome.

Examples:

```text
Staff Onboarding
Staff Offboarding
NPI
ERP Upgrade
Office Relocation
```

Characteristics:

- Cross-functional
- Coordinates workflows
- Manages dependencies
- Produces end-to-end outcome

---

# 3. Activity Templates

---

# Checklist Activity

## Purpose

Used where several actions must be completed but ordering is not important.

Examples:

```text
Safety Audit
Shift Handover
New Hire Day 1
Machine Startup
```

## Lifecycle

```text
Open
 ↓
In Progress
 ↓
Completed
```

## Complete JSON Example

```json
{
  "type": "activity",

  "template": "checklist_activity",

  "code": "CHK-HR-ONBOARDING-DAY1",

  "slug": "hr.onboarding_day1",

  "title": "New Hire Day 1 Checklist",

  "completion_rule": "all_items_completed",

  "items": [
    {
      "code": "collect_documents",
      "title": "Collect Employment Documents",
      "required": true
    },
    {
      "code": "issue_id_card",
      "title": "Issue ID Card",
      "required": true
    },
    {
      "code": "assign_locker",
      "title": "Assign Locker",
      "required": true
    }
  ]
}
```

---

# Work Instruction Activity

## Purpose

Used where execution must follow a defined procedure.

Examples:

```text
Issue Laptop
Machine Setup
Calibration
Material Receiving
```

## Lifecycle

```text
Open
 ↓
Execute Steps
 ↓
Verification
 ↓
Complete
```

## Complete JSON Example

```json
{
  "type": "activity",

  "template": "work_instruction_activity",

  "code": "WI-IT-001",

  "slug": "it.issue_laptop",

  "title": "Issue Laptop",

  "instruction": {

    "steps": [

      {
        "sequence": 10,
        "action": "retrieve_asset"
      },

      {
        "sequence": 20,
        "action": "scan_asset_barcode"
      },

      {
        "sequence": 30,
        "action": "assign_asset"
      },

      {
        "sequence": 40,
        "action": "capture_acknowledgement"
      }

    ]
  }
}
```

---

# 4. Workflow Templates

---

# Simple Workflow

## Purpose

Basic execution workflow with no approval requirements.

Examples:

```text
Inspection
Reporting
Analysis
Data Collection
```

## Lifecycle

```text
Trigger
 ↓
Execution
 ↓
Verification
 ↓
Completion
 ↓
Audit
```

## JSON Example

```json
{
  "type": "workflow",

  "template": "simple_workflow",

  "code": "WF-QA-DAILY",

  "slug": "qa.daily_inspection",

  "title": "Daily Inspection",

  "states": [
    "trigger",
    "execution",
    "verification",
    "completion",
    "audit"
  ],

  "activity_templates": [
    "perform_inspection",
    "verify_results"
  ]
}
```

---

# Approval Workflow

## Purpose

Used when approval is the primary function.

Examples:

```text
Position Approval
Document Approval
Budget Approval
Policy Approval
```

## Lifecycle

```text
Trigger
 ↓
Qualification
 ↓
Authorization
 ↓
Completion
 ↓
Audit
```

## JSON Example

```json
{
  "type": "workflow",

  "template": "approval_workflow",

  "code": "WF-HR-POSITION",

  "slug": "hr.position_approval",

  "title": "Position Approval",

  "states": [
    "trigger",
    "qualification",
    "authorization",
    "completion",
    "audit"
  ],

  "authorization": [

    {
      "role": "department_head"
    },

    {
      "role": "hr_director"
    }

  ]
}
```

---

# Service Request Workflow

## Purpose

Requestor requests a provider to deliver a service.

Examples:

```text
Laptop Request
Access Request
Locker Request
Facility Request
```

## Lifecycle

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

## JSON Example

```json
{
  "type": "workflow",

  "template": "service_request_workflow",

  "code": "WF-IT-LAPTOP",

  "slug": "it.laptop_request",

  "title": "Laptop Request",

  "states": [

    "trigger",
    "qualification",
    "authorization",
    "planning",
    "assignment",
    "execution",
    "verification",
    "completion",
    "audit"

  ],

  "cost_type": "cost_commitment",

  "sla_days": 5
}
```

---
# Incident Workflow

## Purpose

Used when an unplanned event, disruption, violation, or occurrence must be investigated and resolved.

Examples:

```text
Safety Incident
IT Incident
Cybersecurity Incident
Quality Incident
Environmental Incident
Customer Escalation
```

## Characteristics

- Event-driven
- Severity-based prioritization
- Requires investigation
- May generate corrective actions
- Produces audit evidence

## Lifecycle

```text
Trigger
 ↓
Classification
 ↓
Assignment
 ↓
Investigation
 ↓
Resolution
 ↓
Verification
 ↓
Closure
 ↓
Audit
```

## Complete JSON Example

```json
{
  "type": "workflow",

  "template": "incident_workflow",

  "code": "WF-EHS-SAFETY-INCIDENT",

  "slug": "ehs.safety_incident",

  "title": "Safety Incident",

  "states": [
    "trigger",
    "classification",
    "assignment",
    "investigation",
    "resolution",
    "verification",
    "closure",
    "audit"
  ],

  "severity_levels": [
    "low",
    "medium",
    "high",
    "critical"
  ],

  "roles": {
    "owner": "ehs_manager",
    "investigator": "ehs_officer",
    "approver": "site_manager"
  },

  "outputs": [
    "investigation_report",
    "corrective_action",
    "closure_record"
  ]
}
```

---

# Case Workflow

## Purpose

Used when a business matter requires investigation, decisions, actions, and controlled closure.

Examples:

```text
Employee Grievance
Disciplinary Investigation
Whistleblower Case
Legal Dispute
Supplier Dispute
Compliance Case
```

## Characteristics

- Long-running
- Multiple decisions
- Multiple actions
- Evidence-based
- Case record retained

## Lifecycle

```text
Open
 ↓
Assessment
 ↓
Investigation
 ↓
Decision
 ↓
Action
 ↓
Closure
 ↓
Archive
```

## Complete JSON Example

```json
{
  "type": "workflow",

  "template": "case_workflow",

  "code": "WF-HR-GRIEVANCE",

  "slug": "hr.grievance_case",

  "title": "Employee Grievance Case",

  "states": [
    "open",
    "assessment",
    "investigation",
    "decision",
    "action",
    "closure",
    "archive"
  ],

  "roles": {
    "owner": "employee_relations_manager",
    "investigator": "hr_business_partner",
    "approver": "hr_director"
  },

  "outputs": [
    "case_findings",
    "decision_record",
    "closure_record"
  ]
}
```

---

# Maintenance Workflow

## Purpose

Used for maintenance of equipment, machines, facilities, tooling, or utilities.

Examples:

```text
Preventive Maintenance
Corrective Maintenance
Calibration
Building Maintenance
Utility Maintenance
```

## Characteristics

- Resource-driven
- Asset-focused
- Work-instruction based
- Requires verification

## Lifecycle

```text
Trigger
 ↓
Assessment
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

## Complete JSON Example

```json
{
  "type": "workflow",

  "template": "maintenance_workflow",

  "code": "WF-MNT-CM",

  "slug": "maintenance.corrective",

  "title": "Corrective Maintenance",

  "states": [
    "trigger",
    "assessment",
    "planning",
    "assignment",
    "execution",
    "verification",
    "completion",
    "audit"
  ],

  "resources": [
    "machine",
    "technician",
    "work_instruction"
  ],

  "priority_levels": [
    "low",
    "medium",
    "high",
    "critical"
  ]
}
```

---

# Procurement Workflow

## Purpose

Used to acquire goods or services.

Examples:

```text
Purchase Requisition
Capital Equipment Purchase
Supplier Engagement
Contract Services
```

## Characteristics

- Budget controlled
- Cost center governed
- Supplier-dependent
- Produces financial commitments

## Lifecycle

```text
Request
 ↓
Budget Verification
 ↓
Approval
 ↓
Sourcing
 ↓
Ordering
 ↓
Receiving
 ↓
Verification
 ↓
Closure
 ↓
Audit
```

## Complete JSON Example

```json
{
  "type": "workflow",

  "template": "procurement_workflow",

  "code": "WF-PROC-PR",

  "slug": "procurement.purchase_requisition",

  "title": "Purchase Requisition",

  "states": [
    "request",
    "budget_verification",
    "approval",
    "sourcing",
    "ordering",
    "receiving",
    "verification",
    "closure",
    "audit"
  ],

  "cost_type": "cost_commitment",

  "authorization": [
    "cost_center_owner",
    "procurement_manager"
  ],

  "outputs": [
    "approved_pr",
    "purchase_order",
    "receipt_record"
  ]
}
```

---

# Production Workflow

## Purpose

Used by Manufacturing Execution Systems (MES).

Examples:

```text
Die Attach
Wire Bond
Assembly
Molding
Inspection
Packaging
```

## Characteristics

- Operation-based
- Route-driven
- Resource-intensive
- Produces genealogy records

## Lifecycle

```text
Release
 ↓
Dispatch
 ↓
Setup
 ↓
Execution
 ↓
Inspection
 ↓
Move Next
 ↓
Completion
 ↓
Genealogy
```

## Complete JSON Example

```json
{
  "type": "workflow",

  "template": "production_workflow",

  "code": "WF-MES-WIREBOND",

  "slug": "mes.wire_bond",

  "title": "Wire Bond",

  "states": [
    "release",
    "dispatch",
    "setup",
    "execution",
    "inspection",
    "move_next",
    "completion",
    "genealogy"
  ],

  "required_resources": [
    "operator",
    "machine",
    "material",
    "work_instruction"
  ],

  "outputs": [
    "production_record",
    "wip_record",
    "genealogy_record"
  ]
}
```

---

# Quality Workflow

## Purpose

Used to validate quality requirements.

Examples:

```text
Incoming Inspection
In-Process Inspection
Customer Complaint
Supplier NCR
CAPA
```

## Characteristics

- Verification-focused
- Disposition-based
- Evidence-driven
- Often linked to CAPA

## Lifecycle

```text
Trigger
 ↓
Inspection
 ↓
Disposition
 ↓
Corrective Action
 ↓
Verification
 ↓
Closure
 ↓
Audit
```

## Complete JSON Example

```json
{
  "type": "workflow",

  "template": "quality_workflow",

  "code": "WF-QA-INCOMING",

  "slug": "quality.incoming_inspection",

  "title": "Incoming Inspection",

  "states": [
    "trigger",
    "inspection",
    "disposition",
    "corrective_action",
    "verification",
    "closure",
    "audit"
  ],

  "dispositions": [
    "accept",
    "reject",
    "rework",
    "quarantine"
  ]
}
```

---

# Event Driven Workflow

## Purpose

Workflow starts automatically because another event occurred.

Examples:

```text
Auto Create Email Account
Auto Generate Access Request
Auto Create CAPA
Auto Create Maintenance Request
```

## Characteristics

- No manual triggering
- Event subscription based
- Highly automatable

## Lifecycle

```text
Event
 ↓
Execution
 ↓
Completion
```

## Complete JSON Example

```json
{
  "type": "workflow",

  "template": "event_driven_workflow",

  "code": "WF-IT-AUTO-EMAIL",

  "slug": "it.auto_create_email",

  "title": "Auto Create Email Account",

  "trigger": {
    "event": "staff.created"
  },

  "states": [
    "trigger",
    "execution",
    "completion"
  ],

  "outputs": [
    "email_account"
  ]
}
```

---

# Scheduled Workflow

## Purpose

Workflow executes on a defined schedule.

Examples:

```text
Payroll Processing
Cycle Counting
Calibration
Management Review
Monthly Reporting
```

## Characteristics

- Time-triggered
- Repeatable
- Predictable
- Often compliance-driven

## Lifecycle

```text
Schedule
 ↓
Execution
 ↓
Completion
 ↓
Audit
```

## Complete JSON Example

```json
{
  "type": "workflow",

  "template": "scheduled_workflow",

  "code": "WF-HR-PAYROLL",

  "slug": "hr.payroll_processing",

  "title": "Payroll Processing",

  "schedule": {
    "frequency": "monthly",
    "day": 25
  },

  "states": [
    "schedule",
    "execution",
    "completion",
    "audit"
  ],

  "outputs": [
    "payroll_register",
    "payslips"
  ]
}
```

---

# 5. WorkPackage Templates

WorkPackages represent business outcomes.

Unlike workflows, WorkPackages coordinate multiple workflows.

```text
WorkPackage
    ↓
Workflow
    ↓
Activity
```

A WorkPackage owns:

- orchestration
- dependency management
- completion logic
- progress management

A WorkPackage does NOT execute work.

The child workflows execute the work.

---

# Onboarding Package

## Purpose

Provision a new person, organization, or entity.

Examples:

```text
Staff Onboarding
Contractor Onboarding
Vendor Onboarding
Customer Onboarding
```

## Characteristics

- Cross-functional
- Parallel execution
- Dependency driven
- Resource provisioning

## Typical Structure

```text
Onboarding

├── Core Registration
├── Identity Setup
├── Access Setup
├── Equipment Provisioning
├── Training
└── Enrollment
```

## Lifecycle

```text
Create Package
 ↓
Start Child Workflows
 ↓
Monitor Dependencies
 ↓
Await Completion
 ↓
Package Complete
```

## Complete JSON Example

```json
{
  "type": "workpackage",

  "template": "onboarding_package",

  "code": "WP-HR-ONBOARDING",

  "slug": "hr.staff_onboarding",

  "title": "Staff Onboarding",

  "completion_rule": {
    "mode": "all_required_children_completed"
  },

  "children": [

    {
      "workflow": "hr.onboarding",
      "required": true
    },

    {
      "workflow": "it.email_request",
      "required": true
    },

    {
      "workflow": "it.laptop_request",
      "required": true,
      "depends_on": [
        "it.email_request"
      ]
    },

    {
      "workflow": "facilities.locker_request",
      "required": true
    },

    {
      "workflow": "security.access_request",
      "required": true
    },

    {
      "workflow": "hr.training_enrollment",
      "required": true
    }

  ]
}
```

---

# Offboarding Package

## Purpose

Controlled removal of access, resources, and responsibilities.

Examples:

```text
Employee Separation
Contractor Exit
Vendor Termination
Customer Closure
```

## Characteristics

- Asset recovery
- Access revocation
- Knowledge preservation
- Administrative closure

## Typical Structure

```text
Offboarding

├── Access Removal
├── Asset Recovery
├── Knowledge Transfer
├── Payroll Closure
└── Archive
```

## Complete JSON Example

```json
{
  "type": "workpackage",

  "template": "offboarding_package",

  "code": "WP-HR-OFFBOARDING",

  "slug": "hr.staff_offboarding",

  "title": "Staff Offboarding",

  "completion_rule": {
    "mode": "all_required_children_completed"
  },

  "children": [

    {
      "workflow": "security.disable_access",
      "required": true
    },

    {
      "workflow": "it.asset_recovery",
      "required": true
    },

    {
      "workflow": "hr.exit_clearance",
      "required": true
    },

    {
      "workflow": "hr.payroll_closure",
      "required": true
    },

    {
      "workflow": "hr.archive_employment",
      "required": true
    }

  ]
}
```

---

# Project Package

## Purpose

Coordinate delivery of a project.

Examples:

```text
ERP Upgrade
Plant Relocation
System Migration
Office Refurbishment
Cybersecurity Program
```

## Characteristics

- Multi-phase
- Budget controlled
- Resource managed
- Milestone driven

## Typical Structure

```text
Project

├── Planning
├── Procurement
├── Execution
├── Testing
└── Handover
```

## Complete JSON Example

```json
{
  "type": "workpackage",

  "template": "project_package",

  "code": "WP-ERP-IMPLEMENTATION",

  "slug": "project.erp_implementation",

  "title": "ERP Implementation",

  "completion_rule": {
    "mode": "all_required_children_completed"
  },

  "children": [

    {
      "workflow": "project.planning"
    },

    {
      "workflow": "procurement.acquire_solution"
    },

    {
      "workflow": "project.configuration"
    },

    {
      "workflow": "project.testing"
    },

    {
      "workflow": "project.training"
    },

    {
      "workflow": "project.handover"
    }

  ]
}
```

---

# NPI Package

## Purpose

Coordinate introduction of a new product into production.

Examples:

```text
New IC Package
New Customer Program
New Manufacturing Process
New Product Family
```

## Characteristics

- Highly orchestrated
- Cross-functional
- Sequential and parallel dependencies
- Manufacturing focused

## Typical Structure

```text
NPI

├── Process Design
├── Routing Definition
├── Product Costing
├── Process Validation
├── MES Setup
├── Training
└── Production Release
```

## Complete JSON Example

```json
{
  "type": "workpackage",

  "template": "npi_package",

  "code": "WP-NPI",

  "slug": "engineering.npi",

  "title": "New Product Introduction",

  "completion_rule": {
    "mode": "all_required_children_completed"
  },

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
    },

    {
      "workflow": "mes.configuration",

      "depends_on": [
        "engineering.routing_definition"
      ]
    },

    {
      "workflow": "engineering.operator_training",

      "depends_on": [
        "engineering.process_validation"
      ]
    },

    {
      "workflow": "manufacturing.production_release",

      "depends_on": [
        "engineering.operator_training",
        "mes.configuration"
      ]
    }

  ]
}
```

---

# 6. Dependency Patterns

Dependencies determine when workflows may start.

---

# Sequential Pattern

## Structure

```text
A
 ↓
B
 ↓
C
```

## Usage

```text
Approval
 ↓
Purchase
 ↓
Receiving
```

## JSON

```json
{
  "workflow": "receiving",

  "depends_on": [
    "purchase"
  ]
}
```

---

# Parallel Pattern

## Structure

```text
       A

   /   |   \

  B    C    D
```

## Usage

```text
Onboarding

├── Email Setup
├── Access Setup
└── Locker Assignment
```

## JSON

```json
{
  "workflow": "email_setup"
}
```

```json
{
  "workflow": "access_setup"
}
```

```json
{
  "workflow": "locker_assignment"
}
```

No dependencies required.

---

# Fan-Out Pattern

## Structure

```text
A

├── B
├── C
└── D
```

## Usage

```text
Employee Hired
   ↓

Create:

Email
Laptop
Access
Payroll
```

---

## JSON

```json
{
  "event": "staff.hired",

  "start_workflows": [

    "email_request",
    "laptop_request",
    "access_request",
    "payroll_enrollment"

  ]
}
```

---

# Fan-In Pattern

## Structure

```text
B
C
D

 ↓

 E
```

## Usage

```text
Complete:

Email
Laptop
Access

Before:

Onboarding Complete
```

## JSON

```json
{
  "workflow": "onboarding_complete",

  "depends_on": [
    "email_request",
    "laptop_request",
    "access_request"
  ]
}
```

---

# 7. Event Architecture

Pinta should be event-driven.

Events are first-class citizens.

---

# Event Categories

## Workflow Events

```text
workflow.started
workflow.completed
workflow.cancelled
workflow.failed
```

---

## Activity Events

```text
activity.assigned
activity.completed
activity.rejected
```

---

## Resource Events

```text
staff.created
staff.terminated

asset.assigned
asset.returned

machine.breakdown
machine.repaired
```

---

## Business Events

```text
hiring.completed
supplier.approved

npi.released

purchase.approved
```

---

## Event Example

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

# 8. Cost Center Model

Cost Centers are not workflows.

Cost Centers are governance constructs used within workflows.

A workflow may:

- Consume budget
- Commit budget
- Transfer budget
- Realize actual cost

without changing its lifecycle.

---

# Cost Types

## none

No financial relevance.

Examples:

```text
Policy Review
Quality Reporting
Market Analysis
```

---

## informational

Reports or analyzes financial information.

Examples:

```text
Variance Analysis
Profitability Analysis
Workforce Analysis
```

---

## indirect

May influence future costs.

Examples:

```text
Capacity Planning
Strategic Planning
Risk Management
```

---

## budget_check

Requires budget availability verification.

Examples:

```text
Manpower Request
Position Approval
Purchase Requisition
Project Request
```

---

## cost_commitment

Commits future spend.

Examples:

```text
Purchase Order
Hiring Approval
Capital Expenditure
Project Approval
```

---

## cost_transfer

Moves cost responsibility.

Examples:

```text
Workforce Transfer
Department Reorganization
Asset Transfer
```

---

## cost_realization

Creates actual accounting entries.

Examples:

```text
Payroll
Financial Close
Inventory Adjustment
Supplier Payment
```

---

# Cost Center JSON Example

```json
{
  "cost_type": "budget_check",

  "cost_center": {
    "required": true,

    "approval_roles": [
      "cost_center_owner"
    ]
  }
}
```

---

# Multi-Cost-Center Example

```json
{
  "requestor_cost_center": "IT",

  "provider_cost_center": "ENGINEERING",

  "cross_charge": true
}
```

---

# Cost Center Approval Pattern

```text
Request
 ↓
Requestor Cost Center Approval
 ↓
Provider Review
 ↓
Provider Cost Center Approval
 ↓
Execution
```

Examples:

```text
Engineering Service Request
IT Development Request
Facility Upgrade Request
Maintenance Project
```

---

# 9. Authorization Model

Authorization is a reusable policy.

Authorization should never be hardcoded into workflow templates.

---

# Approval Types

## User Approval

```json
{
  "type": "user",
  "value": "john.doe"
}
```

---

## Role Approval

```json
{
  "type": "role",
  "value": "department_head"
}
```

---

## Position Approval

```json
{
  "type": "position",
  "value": "engineering_manager"
}
```

---

## Organization Approval

```json
{
  "type": "organization",
  "value": "finance_department"
}
```

---

## Cost Center Approval

```json
{
  "type": "cost_center_owner"
}
```

---

## Committee Approval

```json
{
  "type": "committee",
  "value": "change_control_board"
}
```

---

# Authorization Example

```json
{
  "authorization": [

    {
      "type": "role",
      "value": "department_head"
    },

    {
      "type": "cost_center_owner"
    },

    {
      "type": "role",
      "value": "hr_director"
    }

  ]
}
```

---

# 10. Resource Model

Resources are allocatable entities.

Workflows consume resources.

Activities utilize resources.

WorkPackages coordinate resources.

---

# Resource Categories

---

## Workforce

```text
Staff
Contractor
Vendor Personnel
Temporary Worker
```

---

## Organization

```text
Department
Team
Division
Business Unit
```

---

## Asset

```text
Laptop
Tooling
Vehicle
Printer
Equipment
```

---

## Manufacturing

```text
Machine
Production Line
Work Center
Tool
Fixture
```

---

## Facility

```text
Building
Room
Desk
Locker
Parking Space
```

---

## Supply

```text
Material
Component
PPE
Consumable
Spare Parts
```

---

# Resource Example

```json
{
  "resource_requirements": [

    {
      "type": "staff",
      "role": "technician",
      "quantity": 1
    },

    {
      "type": "machine",
      "role": "wire_bond_machine",
      "quantity": 1
    }

  ]
}
```

---

# 11. MES Mapping

Pinta should not become MES.

MES should be built on top of Pinta.

---

# Mapping

| MES Concept | Pinta Concept |
|------------|---------------|
| Operation | Activity |
| Route | Workflow |
| Production Order | WorkPackage |
| Traveler | Workflow Instance |
| Dispatch List | Activity Queue |
| Machine | Resource |
| Operator | Resource |
| Tool | Resource |
| Work Instruction | Activity Instruction |
| Genealogy | Audit Trail |
| WIP | Workflow Runtime State |

---

# Example Production Order

Traditional MES:

```text
Lot ABC123

Die Attach
 ↓
Wire Bond
 ↓
Molding
 ↓
Inspection
```

Pinta:

```text
Production Order
        ↓

WorkPackage

├── Die Attach Workflow
├── Wire Bond Workflow
├── Molding Workflow
└── Inspection Workflow
```

---

# Example MES Route

```json
{
  "route": "QFN",

  "operations": [

    "die_attach",
    "wire_bond",
    "molding",
    "trim_form",
    "inspection"

  ]
}
```

---

# Example MES Operation

```json
{
  "operation": "wire_bond",

  "activity": "perform_wire_bond",

  "checklist": "wire_bond_setup",

  "instruction": "WI-MES-WB-001"
}
```

---

# 12. Future File Structure

Recommended Pinta repository structure.

```text
rimba/pinta

definitions/

├── activities/
├── workflows/
├── workpackages/
├── checklists/
├── instructions/
├── events/
├── policies/
├── templates/
└── orchestration/

runtime/

├── activities/
├── workflows/
├── workpackages/
├── events/
└── resources/
```

---

# Template Structure

```text
templates/

workflow/
activity/
workpackage/
```

---

## Workflow Templates

```text
simple_workflow
approval_workflow
service_request_workflow
incident_workflow
case_workflow
maintenance_workflow
procurement_workflow
production_workflow
quality_workflow
event_driven_workflow
scheduled_workflow
```

---

## Activity Templates

```text
checklist_activity
work_instruction_activity
```

---

## WorkPackage Templates

```text
onboarding_package
offboarding_package
project_package
npi_package
```

---

# 13. JSON Design Standards

All future Pinta objects should follow a common structure.

---

# Universal Definition Pattern

```json
{
  "type": "",

  "template": "",

  "code": "",

  "slug": "",

  "title": "",

  "description": "",

  "configuration": {}
}
```

---

# Activity Example

```json
{
  "type": "activity",

  "template": "work_instruction_activity",

  "code": "ACT-001",

  "slug": "issue_laptop",

  "title": "Issue Laptop"
}
```

---

# Workflow Example

```json
{
  "type": "workflow",

  "template": "service_request_workflow",

  "code": "WF-001",

  "slug": "it.laptop_request",

  "title": "Laptop Request"
}
```

---

# WorkPackage Example

```json
{
  "type": "workpackage",

  "template": "onboarding_package",

  "code": "WP-001",

  "slug": "hr.staff_onboarding",

  "title": "Staff Onboarding"
}
```

---

# 14. Template Design Rules

Before creating a new template ask:

---

## Is It Atomic?

```text
Yes
    → Activity
```

---

## Is It Reusable?

```text
Yes
    → Workflow
```

---

## Is It An Outcome?

```text
Yes
    → WorkPackage
```

---

## Does It Coordinate Multiple Workflows?

```text
Yes
    → WorkPackage
```

---

## Does It Belong To One Business Function?

```text
Yes
    → Workflow
```

---

## Is It A Single Action?

```text
Yes
    → Activity
```

---

# Common Mistakes To Avoid

---

## Wrong

```text
Staff Onboarding
    as Workflow
```

---

## Correct

```text
Staff Onboarding
    = WorkPackage
```

---

## Wrong

```text
NPI
    as Workflow
```

---

## Correct

```text
NPI
    = WorkPackage
```

---

## Wrong

```text
Issue Laptop
    as Workflow
```

---

## Correct

```text
Issue Laptop
    = Activity
```

---

# 15. Final Pinta Architecture

---

# Foundational Principle

```text
Activity
    = Atomic Work
```

Examples:

```text
Approve Request
Assign Asset
Inspect Material
```

---

```text
Workflow
    = Business Capability
```

Examples:

```text
Recruitment
Hiring
Purchase Requisition
Access Request
Maintenance Request
```

---

```text
WorkPackage
    = Business Outcome
```

Examples:

```text
Staff Onboarding
Staff Offboarding
NPI
ERP Upgrade
Plant Expansion
```

---

# Future Vision

```text
Rimba

├── Siapa
├── Boleh
├── WFM
├── DMS
├── MES
├── QMS
├── EHS
├── PMO
└── Pinta
```

---

# Pinta's Responsibility

```text
Activity Runtime
Workflow Runtime
WorkPackage Orchestration
Event Bus
Dependency Engine
Authorization Engine
Cost Center Governance
Resource Allocation
Audit Trail
Analytics
```

---

# Final Definition

```text
Activity
    performs work

Workflow
    delivers capability

WorkPackage
    delivers outcome

Pinta
    orchestrates execution
```

---

# End Of PINTA_TEMPLATE_LIBRARY.md

This document, together with:

```text
PINTA_IMPLEMENTATION_ROADMAP.md
PINTA_WORKFLOW_ORCHESTRATION.md
```

forms the initial architectural baseline for building Rimba Pinta.

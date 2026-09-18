# Rimba Pinta: Gaps 1-12 Upgrade

Production-oriented overlay for the current `Rimba\\Workflow` package. Copy these files into `rimba/pinta`, merge the config and service-provider registrations, then run migrations.

## Covered gaps
1. Typed JSON definition registry
2. Slug-derived permission synchronization contract
3. Team ownership metadata and authorization
4. Runtime activity creation and assignment
5. Staff task inbox query/page
6. Permission-filtered workflow catalog
7. Schema-driven launch forms
8. Parent/child workflow links
9. Structured workflow outputs
10. Domain notifications and lifecycle events
11. Policies for definitions, instances, and activities
12. Definition validation and Artisan validation command

## Permission convention
- `init.{workflow-slug}`: start a workflow
- `work.{workflow-slug}`: execute assigned work
- `view.{workflow-slug}`: inspect an instance
- `own.{workflow-slug}`: administer the definition in Team Panel

`PermissionSynchronizer` is intentionally an integration contract. Bind it to the concrete `rimba/boleh` permission API in the host application. The included null implementation keeps Pinta installable without guessing Boleh internals.

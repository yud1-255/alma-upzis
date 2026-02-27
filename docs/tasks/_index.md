# Task Index

## Current Status

**Phase:** [Current phase number and name]  
**Active:** [Task ID currently in progress]  
**Blocked:** [Any blocked tasks and why]

---

## Phase 1: [Name]

**Exit Criteria:** [What's true when phase is complete]

| Task | Title | Status | Depends On |
|------|-------|--------|------------|
| [001](./001-project-setup.md) | Project Setup | Not Started | — |
| [002](./002-ci-pipeline.md) | CI Pipeline | Not Started | 001 |
| [003](./003-deployment.md) | Deployment Config | Not Started | 001 |

## Phase 2: [Name]

**Exit Criteria:** [What's true when phase is complete]  
**Requires:** Phase 1 complete

| Task | Title | Status | Depends On |
|------|-------|--------|------------|
| [004](./004-domain-models.md) | Domain Models | Not Started | 001 |
| [005](./005-database-schema.md) | Database Schema | Not Started | 004 |
| [006](./006-repository-layer.md) | Repository Layer | Not Started | 004, 005 |

## Phase 3: [Name]

| Task | Title | Status | Depends On |
|------|-------|--------|------------|

---

## Status Values

- **Not Started** — Ready to begin (dependencies met)
- **Blocked** — Waiting on dependency or decision
- **In Progress** — Actively being worked
- **Review** — Implementation done, under review
- **Done** — Merged and verified

## Dependency Graph

```
001 ──┬──▶ 002
      │
      └──▶ 003
      │
      └──▶ 004 ──▶ 005 ──┬──▶ 006
                        │
                        └──▶ 007
```

## Creating New Task

1. Identify which phase and dependencies
2. Copy `000-template.md` to `[NNN]-[slug].md`
3. Add to index with dependencies
4. Update dependency graph if complex

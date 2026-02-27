# Task [NNN]: [Title]

| Phase | Status | Story |
|-------|--------|-------|
| [N] | Not Started | [Link or "N/A"] |

---

## Objective

[One sentence: what is accomplished when this task is done]

---

## Preconditions

- [x] [Dependency task] completed
- [x] [Required decision/ADR] accepted
- [ ] [Any setup needed]

---

## Scope

**Implement:**
- [Specific deliverable 1]
- [Specific deliverable 2]

**Boundaries:**
- [Explicit limit — e.g., "no authentication yet"]
- [Explicit limit — e.g., "happy path only, error handling in Task XXX"]

---

## Implementation Notes

<!-- Specific guidance for implementation. Remove if not needed. -->

**Approach:**
[Brief description of how to implement, if non-obvious]

**Key Files:**
- `path/to/create/or/modify`
- `path/to/create/or/modify`

**Patterns to Follow:**
- [Reference to skill or existing code pattern]

---

## Done Criteria

<!-- All must be true for task to be complete -->

- [ ] [Verifiable criterion — e.g., "Tests pass: `make test`"]
- [ ] [Verifiable criterion — e.g., "Endpoint matches spec in `specs/openapi.yaml`"]
- [ ] [Verifiable criterion — e.g., "No lint errors: `make lint`"]
- [ ] [Verifiable criterion — e.g., "Can deploy: `make deploy-dry-run`"]

---

## Verification

```bash
# Commands to verify task is complete
make test
make lint
curl http://localhost:8080/health
```

---

## References

- **RFC:** `docs/rfc/[NNN]-[name].md#[section]`
- **Story:** `docs/stories/[capability]/[story].md`
- **Related Tasks:** [NNN], [NNN]

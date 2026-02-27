# RFC-[NNN]: [Title]

| Status | Author | Capability | Last Updated |
|--------|--------|------------|--------------|
| Draft | [Name] | [C[N] Link](../prd/[NNN]-[name].md) | [Date] |

**Status Values:** Draft → Review → Accepted → Implementing → Done | Superseded

---

## 1. Context

**Trigger:** [Why this RFC now?]

**Requirements:** (from Capability PRD)
- [Key requirement 1]
- [Key requirement 2]

**Constraints:**
- [Technical/business/timeline constraint]
- [Technical/business/timeline constraint]

---

## 2. Decision

### Summary
[2-3 sentences: architectural approach and key choices]

### Architecture
```
[ASCII diagram — keep simple]

┌──────────┐     ┌──────────┐     ┌──────────┐
│  Client  │────▶│   API    │────▶│    DB    │
└──────────┘     └──────────┘     └──────────┘
```

### Technology Choices

| Concern | Choice | Rationale |
|---------|--------|-----------|
| Language | | |
| Database | | |
| Framework | | |
| Deployment | | |

---

## 3. Design Details

### 3.1 [Component Name]

**Responsibility:** [Single sentence]

**Interface:**
```
[Signatures or pseudocode]
DoThing(input) -> (Output, error)
```

**Error Cases:**
| Condition | Error | Notes |
|-----------|-------|-------|
| [Case] | [Error type] | [Handling] |

### 3.2 [Component Name]
<!-- Repeat structure -->

---

## 4. Data Model

### Entities
```
[Entity]
├── id: UUID (PK)
├── [field]: [type]
└── [field]: [type]
```

### State Transitions (if applicable)
```
[State A] ──action──▶ [State B] ──action──▶ [State C]
```

---

## 5. API Design

**Spec:** `specs/openapi.yaml`

**Endpoints:**
| Method | Path | Purpose |
|--------|------|---------|
| | | |

**Response Envelope:**
```json
{
  "data": {},
  "error": { "code": "", "message": "" },
  "meta": { "request_id": "" }
}
```

---

## 6. Alternatives Considered

### [Alternative Name]
- **Approach:** [Brief description]
- **Rejected because:** [Specific reason]

---

## 7. Risks

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| | L/M/H | L/M/H | |

---

## 8. Implementation Phases

### Phase 1: [Name]
**Exit Criteria:** [What's true when done]

**Scope:**
- [High-level deliverable]
- [High-level deliverable]

**Tasks:** `docs/tasks/phase-1/` or `docs/tasks/` with phase-1 prefix

### Phase 2: [Name]
**Depends on:** Phase 1  
**Exit Criteria:** [What's true when done]

**Scope:**
- [High-level deliverable]

---

## 9. Operational Concerns

**Logging:** [What and at what level]

**Metrics:** [Key metrics to expose]

**Alerts:** [Alert conditions]

**Rollback:** [How to revert]

---

## 10. Security

- [ ] [Concern and mitigation]
- [ ] [Concern and mitigation]

---

## 11. Open Questions

- [ ] [Question] — [Owner], [Due]

---

## References
- [Link]

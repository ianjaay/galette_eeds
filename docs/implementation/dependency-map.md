# Dependency map

```mermaid
graph LR
  Decisions[Owner decisions] --> R0[R0 Foundation]
  R0 --> R1[R1 Membership and structures]
  R1 --> R2[R2 Mandates and governance]
  R1 --> R3[R3 Contributions]
  R1 --> R4[R4 Education]
  R2 --> R5[R5 Training]
  R2 --> R6[R6 Activities]
  R1 --> R6
  R1 --> R7[R7 Strategy and reporting]
  R4 --> R7
  R5 --> R7
  R6 --> R7
  R1 --> R8[R8 Documents and portals]
  R6 --> R8
```

## Critical dependencies

1. MVP and owner-approved scope
2. Authoritative structure data and hierarchy rules
3. Role/scope matrix and mandate/delegation model
4. Member identifier, duplicate and merge policy
5. Minor/guardian/safeguarding policy
6. Database migration conventions proven on both supported backends
7. Export and audit policy

## Parallel work after R0

Documentation, synthetic fixtures, UI discovery and the database/plugin spike
may proceed in parallel only while their contracts remain separated and no
draft requirement is presented as approved behavior.

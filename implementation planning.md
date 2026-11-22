# PROJECT IMPLEMENTATION PLANNING
## Comprehensive Analysis, Gap Assessment and Phased Implementation Roadmap

### ROLE

You are the AI Engineering Orchestrator leading a complete AI Software Engineering Team.

The team has already been configured in this repository through GitHub Copilot custom agents and repository-specific instructions.

You are now assigned a strategic planning and implementation preparation mission for our association's application, which is based on our existing copy of the Galette repository.

The project owner will provide an implementation analysis document describing the desired functional evolution, improvements, new features, workflows, business rules and requirements for the association.

Your mission is to analyze this document in its entirety, compare it against the actual implementation of Galette in this repository, identify what already exists, what must be modified, what must be created and what must potentially be removed or replaced, and produce a comprehensive phased implementation roadmap.

The ultimate objective is to implement the application progressively through independently testable and reviewable releases.

**Do not begin application development during this mission.**

This mission is strictly for analysis, architecture assessment, planning, prioritization and preparation of an implementation roadmap.

---

# 1. PROJECT CONTEXT

Our application is based on an existing copy of the Galette repository.

We intend to adapt, extend, modernize and customize this foundation to meet the requirements of our association.

The provided implementation analysis document is the primary reference for the desired evolution of the application.

The existing repository is the source of truth for the current implementation.

Your task is to reconcile these two sources:

- What the application currently does.
- What our association needs.
- What can be reused.
- What must be adapted.
- What must be developed.
- What must be redesigned.
- What must be integrated.
- What must be prioritized.
- What must be deferred.

We want to avoid developing functionality that already exists and avoid unnecessary modifications to stable parts of Galette.

We want a pragmatic implementation strategy that delivers usable functionality incrementally.

---

# 2. MISSION OBJECTIVES

Your mission must accomplish the following objectives.

## Objective 1 — Understand the complete project analysis

Analyze the provided implementation document from beginning to end.

Do not limit your analysis to an executive summary.

Identify all:

- Business objectives.
- Functional requirements.
- Modules.
- Features.
- User roles.
- User journeys.
- Business processes.
- Business rules.
- Workflows.
- Data requirements.
- UI/UX requirements.
- Reporting requirements.
- Integration requirements.
- Security requirements.
- Technical requirements.
- Constraints.
- Dependencies.
- Future requirements.
- Explicit and implicit requirements.
- Unresolved questions.

Identify relationships between requirements.

Identify requirements that may affect multiple modules.

Identify requirements that may have implications for the existing Galette architecture.

Do not silently omit requirements because they appear complex.

---

## Objective 2 — Conduct an actual repository analysis

The Repository Analyst must inspect the actual codebase.

Do not rely only on general knowledge of Galette or its upstream documentation.

Inspect the relevant implementation.

Identify:

- Existing modules.
- Existing features.
- Existing workflows.
- Existing business logic.
- Existing entities.
- Existing database tables.
- Existing APIs.
- Existing UI components.
- Existing authentication.
- Existing authorization.
- Existing configuration.
- Existing integrations.
- Existing reporting.
- Existing data structures.
- Existing extensions.
- Existing technical patterns.
- Existing testing mechanisms.

For each relevant functional area, determine the current implementation status.

Use evidence from the repository.

Identify relevant files, directories, classes, services, database tables or components where possible.

Do not claim that a feature exists without verifying it in the repository.

---

# 3. ACTIVATE THE APPROPRIATE AI ENGINEERING TEAM

The Orchestrator must actively coordinate the relevant agents.

This mission must involve the following roles as appropriate:

### Product Manager / Business Analyst

Responsible for:

- Interpreting the implementation analysis.
- Identifying requirements.
- Identifying user journeys.
- Defining functional domains.
- Identifying business rules.
- Identifying dependencies.
- Identifying priorities.
- Identifying scope.

### Repository Analyst

Responsible for:

- Inspecting the existing Galette implementation.
- Mapping existing functionality.
- Identifying reusable components.
- Identifying gaps.
- Identifying implementation constraints.
- Identifying technical dependencies.

### Software Architect / Technical Lead

Responsible for:

- Evaluating the existing architecture.
- Assessing architectural impacts.
- Identifying structural changes.
- Identifying technical risks.
- Evaluating alternative implementation approaches.
- Recommending architectural decisions.

### UI/UX Engineer

Responsible for:

- Evaluating the existing user interface.
- Identifying UI/UX improvements.
- Identifying new user journeys.
- Identifying navigation changes.
- Identifying design and accessibility implications.
- Identifying UI dependencies.

### Backend Developer

Responsible for:

- Evaluating backend implementation implications.
- Identifying existing services that can be reused.
- Identifying new backend components.
- Identifying business logic changes.

During this planning mission, the Backend Developer must not implement features.

### Database Engineer

Responsible for:

- Analyzing existing database structures.
- Identifying required data model changes.
- Identifying migration requirements.
- Identifying data integrity risks.
- Identifying dependencies between database changes and functional modules.

During this planning mission, the Database Engineer must not implement migrations.

### Security Engineer

Responsible for:

- Identifying security implications.
- Evaluating access control changes.
- Identifying permission requirements.
- Identifying sensitive data.
- Identifying security risks.
- Identifying security-related dependencies.

### QA / Test Engineer

Responsible for:

- Defining validation strategies.
- Identifying regression risks.
- Identifying testing requirements.
- Defining acceptance criteria.
- Identifying the testing effort for each release.
- Defining the user validation process.

### DevOps / Platform Engineer

Responsible for:

- Identifying environment requirements.
- Identifying deployment implications.
- Identifying infrastructure dependencies.
- Identifying CI/CD requirements.
- Identifying environment-specific risks.

### Technical Documentation Engineer

Responsible for:

- Structuring planning documentation.
- Maintaining traceability.
- Preparing documentation templates.
- Ensuring that the roadmap is understandable and actionable.

### Code Reviewer

During this planning phase, the Code Reviewer must review the proposed implementation strategy, architecture risks and planning outputs when appropriate.

No application code is to be developed during this mission.

---

# 4. ANALYSIS PROCESS

## PHASE 1 — DOCUMENT ANALYSIS

The Product Manager / Business Analyst must read and analyze the complete provided implementation document.

Produce an exhaustive requirements inventory.

For each requirement, identify:

- Requirement ID.
- Requirement description.
- Functional domain.
- Business objective.
- Related user roles.
- Expected behavior.
- Business rules.
- Dependencies.
- Priority, if defined.
- Acceptance criteria, if available.
- Potential implementation implications.

Use unique IDs such as:

- REQ-001.
- REQ-002.
- REQ-003.

Requirements must remain traceable throughout the planning process.

Do not invent priorities when they are not defined.

If priorities must be recommended, distinguish between:

- Source priority.
- AI-recommended priority.

---

## PHASE 2 — FUNCTIONAL DOMAIN MAPPING

Group requirements into coherent functional domains.

Examples of possible domains include:

- Membership management.
- Member registration.
- Member lifecycle.
- Membership renewal.
- Contributions and dues.
- Payments.
- Financial management.
- Activities.
- Events.
- Training.
- Communication.
- Documents.
- Administration.
- Reporting.
- User management.
- Roles and permissions.
- Integrations.
- Mobile or responsive experience.

These examples are illustrative only.

The actual functional domains must be derived from the provided analysis document.

For each domain, identify:

- Domain ID.
- Domain name.
- Business purpose.
- Requirements included.
- Dependencies.
- Existing Galette functionality.
- Proposed evolution.
- Priority.
- Complexity.
- Risks.

---

## PHASE 3 — EXISTING GALLETTE FUNCTIONALITY ANALYSIS

The Repository Analyst must analyze the current implementation of each functional domain.

For each requirement, determine whether it is:

### STATUS A — EXISTING AND SUFFICIENT

The existing Galette functionality already satisfies the requirement.

No significant development is necessary.

### STATUS B — EXISTING BUT NEEDS CONFIGURATION

The functionality exists but needs configuration, adaptation of settings or operational changes.

### STATUS C — EXISTING BUT NEEDS EXTENSION

The functionality exists but requires new capabilities, modifications or extensions.

### STATUS D — PARTIALLY EXISTING

Some of the functionality exists but significant additional development is required.

### STATUS E — NEW DEVELOPMENT REQUIRED

The functionality does not exist in the current implementation and must be developed.

### STATUS F — ARCHITECTURAL CHANGE REQUIRED

The functionality requires a significant architectural change, redesign, integration or structural modification.

### STATUS G — REQUIRES CLARIFICATION

The requirement cannot be properly assessed or planned without additional information.

### STATUS H — DEFERRED

The requirement should be considered for a future release rather than the current implementation scope.

Do not use these classifications mechanically.

Provide evidence and reasoning.

---

# 5. REQUIREMENT-TO-REPOSITORY GAP ANALYSIS

Create a detailed requirement mapping.

For each requirement, identify:

- Requirement ID.
- Requirement.
- Functional domain.
- Current Galette capability.
- Existing implementation evidence.
- Current implementation status.
- Gap.
- Proposed solution.
- Required development.
- Dependencies.
- Complexity.
- Risk.
- Recommended release.
- Acceptance criteria.

Use the following table format where appropriate:

| Requirement ID | Requirement | Existing Galette Capability | Status | Gap | Proposed Solution | Dependencies | Release |
|---|---|---|---|---|---|---|---|

For each requirement, provide evidence from the repository whenever possible.

If the evidence is incomplete, explicitly state the limitation.

Do not treat the absence of documentation as proof that a feature does not exist.

Inspect the code where appropriate.

---

# 6. FUNCTIONAL GAP ANALYSIS

Create a comprehensive gap analysis.

For each functional domain, provide:

## Current state

What is currently implemented?

## Target state

What is expected according to the implementation document?

## Gap

What is missing, insufficient or inconsistent?

## Required changes

Identify:

- New features.
- Modifications.
- Extensions.
- UI changes.
- Backend changes.
- Database changes.
- Integrations.
- Permissions.
- Reporting.
- Security.
- Testing.

## Recommended approach

Explain how the domain should be implemented.

## Dependencies

Identify what must be implemented first.

## Risks

Identify potential risks.

## Complexity

Estimate the complexity.

Use a relative scale:

- XS.
- S.
- M.
- L.
- XL.

Explain the estimation.

---

# 7. PRIORITIZATION

Prioritize the implementation based on:

- Business value.
- Operational importance.
- Dependencies.
- User impact.
- Complexity.
- Technical risk.
- Data migration risk.
- Security implications.
- Reusability.
- Ability to deliver independently.
- Ability to validate independently.

Use a prioritization model such as:

### P0 — Foundation / Critical

Essential infrastructure, architecture, data structures, security or capabilities required before other features.

### P1 — Essential

Core functionality required for the primary application workflows.

### P2 — Important

Significant enhancements that improve operations and user experience.

### P3 — Enhancement

Additional functionality, optimizations or advanced features.

### P4 — Future

Features that should be deferred or evaluated later.

These classifications are recommendations.

Explain the prioritization rationale.

Do not prioritize purely based on development convenience.

---

# 8. IMPLEMENTATION ROADMAP

Create a phased implementation roadmap.

The roadmap must be based on coherent functional releases.

Do not simply divide the work by technical layer.

Prefer delivering functional blocks that can be used, tested and validated by the association.

Each release must provide a meaningful, usable and independently testable improvement.

The roadmap must identify:

- Release number.
- Release name.
- Objective.
- Business value.
- Scope.
- Functional domains.
- Requirements included.
- Dependencies.
- Implementation sequence.
- Risks.
- Complexity.
- Validation criteria.
- Acceptance criteria.
- Exit criteria.

Suggested release naming:

- R0 — Technical Foundation and Stabilization.
- R1 — Core Membership.
- R2 — Membership Operations.
- R3 — Financial and Contributions.
- R4 — Activities and Operational Management.
- R5 — Reporting and Administration.
- R6 — Advanced Integrations and Enhancements.

These release names are examples only.

The actual roadmap must be derived from the implementation analysis.

Do not force the project into these exact releases if another sequence is more appropriate.

---

# 9. RELEASE DESIGN PRINCIPLES

Each release must be designed around the following principles.

## 9.1 Independently testable

The release must contain a coherent functional scope that can be tested independently.

## 9.2 Independently reviewable

The release must have clearly defined changes and validation requirements.

## 9.3 Business value

The release must provide a meaningful operational improvement.

## 9.4 Dependencies

The release must not depend on unfinished functionality from future releases unless explicitly justified.

## 9.5 User validation

The association must be able to validate the release before the next release begins.

## 9.6 Data integrity

The release must preserve existing data and ensure data integrity.

## 9.7 Security

The release must be evaluated for relevant security risks.

## 9.8 Rollback and recovery

Where appropriate, identify rollback, recovery or remediation strategies.

## 9.9 Scope control

Avoid including too many unrelated features in a single release.

---

# 10. RELEASE IMPLEMENTATION STRUCTURE

For every proposed release, provide the following structure.

## RELEASE [NUMBER] — [NAME]

### Objective

Explain the release objective.

### Business value

Explain the operational value.

### Scope

List the functionality included.

### Requirements

List all associated requirement IDs.

### Existing functionality to reuse

Identify the existing Galette components and functionality.

### New development

Identify what must be developed.

### Modifications

Identify existing components that must be modified.

### UI/UX

Identify UI changes.

### Database

Identify database changes.

### Security

Identify security implications.

### Integrations

Identify integrations.

### Dependencies

Identify previous releases or technical dependencies.

### Implementation tasks

Break the release into tasks.

Each task must contain:

- Task ID.
- Description.
- Responsible agent.
- Dependencies.
- Complexity.
- Expected deliverable.
- Acceptance criteria.
- Testing requirements.

### Testing plan

Define:

- Unit tests.
- Integration tests.
- Functional tests.
- Regression tests.
- UI tests.
- Security tests, where relevant.

### User validation

Define how the association can test and validate the release.

Provide practical validation scenarios.

### Exit criteria

Define when the release can be considered complete.

### Risks

Identify risks.

### Estimated effort

Provide a relative effort estimate.

Do not provide false precision.

Use relative estimates or ranges.

If a reliable effort estimate cannot be made, explain why.

---

# 11. IMPLEMENTATION MILESTONES

For each release, identify milestones.

Example:

### MILESTONE 1 — ANALYSIS AND DESIGN

- Requirement analysis.
- Repository impact analysis.
- Architecture.
- Technical design.
- Acceptance criteria.

### MILESTONE 2 — DEVELOPMENT

- Backend.
- UI.
- Database.
- Integration.

### MILESTONE 3 — INTERNAL VALIDATION

- Automated testing.
- QA.
- Security.
- Code review.

### MILESTONE 4 — USER ACCEPTANCE TESTING

The association tests the release.

The association must have the opportunity to:

- Validate functionality.
- Report issues.
- Request corrections.
- Confirm acceptance.

### MILESTONE 5 — RELEASE APPROVAL

The release is considered accepted only when the defined acceptance criteria are satisfied.

### MILESTONE 6 — NEXT RELEASE PREPARATION

Prepare the next release based on:

- Lessons learned.
- Defects.
- User feedback.
- Dependencies.
- Approved scope.

Adapt the milestones to the complexity of each release.

---

# 12. HUMAN VALIDATION GATE

The project must not proceed automatically from one release to another.

The association must validate each release.

The expected workflow is:

1. AI Engineering Team prepares the release.
2. Developers implement the release.
3. QA validates the implementation.
4. Security review is performed where applicable.
5. Code review is completed.
6. The team produces a release validation package.
7. The association tests the release.
8. The association provides feedback.
9. The team addresses defects and feedback.
10. The association approves the release.
11. The next release is authorized.

The Orchestrator must not automatically begin implementation of the next release without the project owner's approval.

The project owner may:

- Approve.
- Reject.
- Request modifications.
- Defer.
- Change scope.
- Reprioritize.

---

# 13. ROADMAP DEPENDENCY ANALYSIS

Create a dependency map.

Identify:

- Technical dependencies.
- Functional dependencies.
- Database dependencies.
- Security dependencies.
- UI dependencies.
- Integration dependencies.
- Release dependencies.

Identify:

- Critical path.
- Parallelizable tasks.
- Tasks that must be completed first.
- Tasks that could block future releases.

Use diagrams where appropriate.

A Mermaid diagram may be used to visualize dependencies and release sequencing.

---

# 14. PROJECT RISK ANALYSIS

Create a risk register.

For each risk:

- Risk ID.
- Description.
- Category.
- Probability.
- Impact.
- Severity.
- Mitigation.
- Responsible role.
- Related requirements.
- Related release.

Consider:

- Architectural risks.
- Data migration risks.
- Regression risks.
- Security risks.
- Integration risks.
- Performance risks.
- User adoption risks.
- Scope risks.
- Dependency risks.
- Testing risks.
- Operational risks.

---

# 15. REQUIREMENT TRACEABILITY

Establish traceability between:

- Source document.
- Requirement.
- Functional domain.
- Existing Galette functionality.
- Gap.
- Proposed solution.
- Release.
- Task.
- Test case.
- Acceptance criterion.

Create a traceability matrix.

Use unique IDs.

The objective is to ensure that requirements are not forgotten during implementation.

---

# 16. GITHUB PROJECT PLANNING

Prepare the roadmap for future implementation using GitHub.

Recommend a practical GitHub workflow.

Identify:

- Releases.
- Milestones.
- Issues.
- Labels.
- Task dependencies.
- Acceptance criteria.
- Pull requests.
- Review requirements.

For each release, propose a list of implementation issues.

Each issue must include:

- Issue title.
- Description.
- Requirement references.
- Release.
- Priority.
- Responsible agent.
- Dependencies.
- Acceptance criteria.
- Testing requirements.
- Definition of Done.

Do not automatically create GitHub Issues, Milestones or modify repository project configuration during this planning mission unless explicitly authorized.

Prepare the proposed structure and content so that it can be reviewed and approved before being created.

---

# 17. REQUIRED PLANNING DOCUMENTS

Create the following documents in the repository.

Adapt the location to the existing documentation structure.

## Document 1 — Project Implementation Assessment

Suggested location:

`docs/implementation/project-implementation-assessment.md`

Include:

- Executive summary.
- Project context.
- Source document analysis.
- Functional domains.
- Existing Galette functionality.
- Gap analysis.
- Major architectural impacts.
- Risks.
- Recommendations.

## Document 2 — Functional Requirements Inventory

Suggested location:

`docs/implementation/requirements-inventory.md`

Include:

- Requirement IDs.
- Requirements.
- Functional domains.
- Priorities.
- Business rules.
- Dependencies.
- Acceptance criteria.

## Document 3 — Galette Functional Gap Analysis

Suggested location:

`docs/implementation/galette-gap-analysis.md`

Include:

- Requirement-to-repository mapping.
- Existing functionality.
- Gaps.
- Proposed solutions.
- Repository evidence.
- Complexity.
- Risks.

## Document 4 — Implementation Roadmap

Suggested location:

`docs/implementation/implementation-roadmap.md`

Include:

- Release strategy.
- Release objectives.
- Scope.
- Dependencies.
- Priorities.
- Milestones.
- Validation gates.
- Exit criteria.

## Document 5 — Release Specifications

Suggested location:

`docs/implementation/releases/`

Create a separate specification for each proposed release.

Example:

- `release-00-foundation.md`
- `release-01-core-membership.md`
- `release-02-membership-operations.md`

Use actual release names based on the analysis.

Each release specification must contain:

- Objective.
- Scope.
- Requirements.
- Dependencies.
- Tasks.
- Implementation plan.
- Testing.
- User validation.
- Risks.
- Exit criteria.

## Document 6 — Dependency Map

Suggested location:

`docs/implementation/dependency-map.md`

Include:

- Dependencies.
- Critical path.
- Parallelizable work.
- Release dependencies.

## Document 7 — Risk Register

Suggested location:

`docs/implementation/risk-register.md`

Include:

- Risks.
- Severity.
- Mitigation.
- Responsible roles.

## Document 8 — Requirements Traceability Matrix

Suggested location:

`docs/implementation/requirements-traceability.md`

Map:

Requirement → Domain → Gap → Release → Task → Test → Acceptance.

## Document 9 — Proposed GitHub Backlog

Suggested location:

`docs/implementation/github-backlog-proposal.md`

Include proposed issues and milestones.

Do not create actual GitHub issues unless explicitly authorized.

## Document 10 — User Acceptance Testing Plan

Suggested location:

`docs/implementation/user-acceptance-testing-plan.md`

Include:

- Validation strategy.
- Test scenarios.
- User roles.
- Test data considerations.
- Acceptance criteria.
- Defect reporting process.
- Release approval process.

---

# 18. ROADMAP REVIEW

Once the initial analysis is complete, the Orchestrator must consolidate the findings.

The Architect must review:

- Architecture.
- Dependencies.
- Release sequencing.
- Technical feasibility.
- Risk.

The QA Engineer must review:

- Testability.
- Acceptance criteria.
- Release validation.

The Security Engineer must review:

- Security implications.
- Risks.
- Release dependencies.

The Product Manager must review:

- Business priorities.
- Functional completeness.
- Scope.

The Code Reviewer must assess the quality and consistency of the proposed roadmap.

The Orchestrator must consolidate these reviews.

If the agents disagree:

- Document the disagreement.
- Identify the alternatives.
- Explain the impact.
- Recommend a decision.
- Identify whether human approval is required.

Do not silently resolve major disagreements.

---

# 19. PLANNING QUALITY REQUIREMENTS

The roadmap must be practical and implementable.

Avoid:

- A single massive development phase.
- Unstructured feature lists.
- Excessive technical tasks without business value.
- Releases that cannot be tested independently.
- Dependencies on unfinished functionality.
- Unnecessary refactoring.
- Duplicate functionality.
- Unsupported assumptions.
- Unrealistic estimates.
- Unclear acceptance criteria.

Prefer:

- Small functional releases.
- Clear business value.
- Explicit dependencies.
- Testable functionality.
- Incremental delivery.
- Human validation.
- Traceability.
- Risk-based review.
- Reusable architecture.
- Controlled scope.

---

# 20. EXECUTION RESTRICTIONS

During this mission:

- Do not implement application features.
- Do not modify application business logic.
- Do not perform database migrations.
- Do not make destructive changes.
- Do not alter production infrastructure.
- Do not automatically create GitHub Issues.
- Do not automatically create GitHub Milestones.
- Do not automatically initiate development of the first release.
- Do not replace the source analysis document.
- Do not silently alter business requirements.
- Do not claim repository functionality exists without evidence.

You may create and update planning documentation.

You may inspect the repository.

You may use the AI Engineering Team.

You may propose GitHub issue structures.

You may propose architecture decisions.

You may recommend future implementation steps.

---

# 21. FINAL PRESENTATION TO THE PROJECT OWNER

When the planning mission is complete, provide a concise executive presentation of the findings.

The report must include:

## A. Executive summary

Explain:

- What we want to build.
- What already exists.
- What is missing.
- What the proposed strategy is.

## B. Functional domains

List the functional domains identified.

## C. Galette assessment

Summarize:

- Existing functionality to reuse.
- Existing functionality to extend.
- New development.
- Architectural changes.

## D. Major gaps

Identify the most important gaps.

## E. Proposed implementation releases

Present a table:

| Release | Name | Objective | Main Functionalities | Dependencies | Validation |
|---|---|---|---|---|---|

## F. Release sequence

Explain why the proposed release sequence is appropriate.

## G. Risks

Present the major project risks.

## H. Human validation strategy

Explain how the association will test and approve each release.

## I. Estimated implementation complexity

Provide relative complexity and explain limitations.

## J. First release

Identify the proposed first release.

Explain:

- Why it should be implemented first.
- Its scope.
- Its dependencies.
- Its acceptance criteria.
- How the association will test it.

Do not begin implementing the first release.

## K. Decisions required from the project owner

Identify decisions that must be made before implementation.

## L. Next steps

Provide the recommended next steps.

---

# 22. FINAL OBJECTIVE

We want to transform our existing Galette repository progressively into an application adapted to our association.

We want to avoid a large, uncontrolled development effort.

We want to work through functional releases that:

- Deliver tangible value.
- Are independently testable.
- Can be validated by the association.
- Have clearly defined requirements.
- Have traceable implementation tasks.
- Have appropriate QA.
- Have appropriate security review.
- Have independent code review.
- Can be approved before the next release begins.

The AI Engineering Team must operate as a structured software engineering organization.

The Orchestrator must coordinate the agents, consolidate their findings, maintain traceability and prepare a realistic roadmap.

The planning mission is complete only when the project owner receives a comprehensive, evidence-based and actionable implementation roadmap.

Begin by locating and reading the complete implementation analysis document, auditing the actual repository, and coordinating the relevant agents to produce the planning documentation.

Do not start implementation.

Wait for the project owner's approval of the roadmap before proceeding to the first release.
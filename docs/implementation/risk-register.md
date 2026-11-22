# Risk register

| ID | Risk | Severity | Mitigation | Owner |
|---|---|---|---|---|
| R-01 | A proposal is implemented as statute | High | Preserve source status labels and require owner approval | Product owner |
| R-02 | A broad role grants excessive access | High | Two-axis role plus organizational scope; deny by default | Security |
| R-03 | A youth or guardian model exposes unrelated minors | Critical | Data minimization, scope tests and safeguarding review | Privacy/safeguarding |
| R-04 | Export exposes sensitive/member fields | Critical | Domain export allowlist, logging and privileged approval | Security |
| R-05 | Duplicate or merge corrupts identity | High | Approved criteria, preview and reversible reconciliation | Product/data |
| R-06 | Migration loses or misinterprets history | High | Additive migrations, backup, repeat-run and dual-engine tests | Database |
| R-07 | Rigidity prevents special structures | Medium | Configurable typed model rather than fixed branches/states | Architect |
| R-08 | Guardian portal enables improper access | Critical | Defer until verified relationship, purpose and revocation exist | Privacy/safeguarding |
| R-09 | Frappe assumptions affect Galette design | Medium | Trace only to current Galette code and approved decisions | Repository analyst |
| R-10 | Unapproved minor incident or health data is collected | Critical | Exclude until explicit policy, access and retention are approved | Privacy/safeguarding |

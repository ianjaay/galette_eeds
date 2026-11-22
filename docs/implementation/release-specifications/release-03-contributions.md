# Release R3 - Contributions and receipts

## Objective

Manage approved annual membership contributions and receipts without creating
a general accounting module.

## Scope

- Contribution periods and approved amounts/exemptions
- Payment and reconciliation records
- Receipts
- Cancellation/refund audit
- Territorial financial and good-standing reporting
- Controlled exports

## Dependencies

- R1 member and structure model
- Approved fee, period, exemption and receipt rules
- Finance separation-of-duty and audit policy

## Exit criteria

- Approved rules determine status and good standing
- Duplicate payment is prevented or reconciled visibly
- Receipt, cancellation and refund evidence is complete
- Financial exports use an approved field allowlist
- Results reconcile on MySQL/MariaDB and PostgreSQL

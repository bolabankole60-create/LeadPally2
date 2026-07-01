# LeadPally Architecture Freeze

Foundation v3 and Phases 1–4 are the approved architecture.

## Rules

1. Architectural changes require a documented proposal with implementation evidence, not preference.
2. Every PR must implement the approved design, include tests, pass build/lint, and avoid unnecessary architecture changes.
3. New work focuses on implementation quality, testing, performance, and user value.

## Frozen Components

- **Foundation v3**: Domains, Actions, DTOs, Integrations, Enums, Resources, Jobs
- **Phase 1**: Lead Search, CRM, Tags, Search Pipeline, Credits
- **Phase 2**: Exports, Bulk Actions, WhatsApp Outreach, Templates
- **Phase 3**: Billing, Subscriptions, Paystack, Feature Gates, Admin
- **Phase 4**: Marketing, Blog/CMS, SEO, Referrals, Public API, AI

## Execution Roadmap

| Sprint | Focus | Exit Criteria |
|--------|-------|---------------|
| **1** | Scaffold Laravel 12 + Next.js, configure PostgreSQL/Redis/Sanctum/Queues/Docker | Working foundation on GitHub |
| **2** | Implement Phases 1–4 exactly as designed | Each module compiles, passes tests, merged |
| **3** | CI/CD, monitoring, load testing, security review, backups, staging | Staging environment operational |
| **4** | Closed beta with 20–50 Nigerian businesses | Stability, performance, conversion validated |
| **5** | Public launch | Platform proven stable |

## PR Checklist

Every pull request must satisfy:

1. ✅ Implements the approved design
2. ✅ Includes automated tests
3. ✅ Passes build and lint checks
4. ✅ Does not change architecture without documented justification

LeadPally is ready to build.

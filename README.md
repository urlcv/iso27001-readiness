# ISO 27001 Readiness

> Part of the [URLCV](https://urlcv.com) free tools suite.

**Live tool:** [urlcv.com/tools/iso27001-readiness](https://urlcv.com/tools/iso27001-readiness)

---

## What it does

A Vanta-style traffic-light self-assessment that helps organisations gauge how ready they are for ISO 27001:2022 certification. Rate each control as **Red** (not started), **Amber** (in progress), or **Green** (fully implemented) and see your overall readiness at a glance.

**Goal: get all lights green = ready for certification.**

### Features

- **Traffic-light dashboard** — circular progress ring showing % of controls at green, with per-section breakdown bars
- **ISMS clause coverage** — 20 checks across mandatory clauses 4–10 (context, leadership, planning, support, operation, performance evaluation, improvement)
- **Annex A control themes** — 25 checks across the four ISO 27001:2022 themes: organisational (A.5), people (A.6), physical (A.7), and technological (A.8)
- **Public claims gap analysis** — toggle what you claim publicly and see warnings where claims outstrip actual control status
- **Priority view** — red items listed first so you know where to focus
- **Markdown export** — copy a structured summary to clipboard for sharing with stakeholders
- **100% client-side** — no data leaves the browser, no signup required

### Assessment areas

| Area | Controls | Focus |
|------|----------|-------|
| Clause 4 – Context | 3 | Scope, interested parties, issues |
| Clause 5 – Leadership | 3 | Management commitment, policy, roles |
| Clause 6 – Planning | 3 | Risk assessment, treatment, objectives |
| Clause 7 – Support | 4 | Resources, competence, awareness, documentation |
| Clause 8 – Operation | 2 | Risk treatment implementation, change management |
| Clause 9 – Performance | 3 | Monitoring, internal audit, management review |
| Clause 10 – Improvement | 2 | Nonconformity, continual improvement |
| A.5 – Organisational | 10 | Policies, assets, access, suppliers, incidents, BCP, compliance |
| A.6 – People | 5 | Screening, contracts, training, discipline, termination |
| A.7 – Physical | 5 | Perimeters, entry, offices, clear desk, disposal |
| A.8 – Technological | 10 | Endpoints, PAM, encryption, vuln mgmt, logging, SDLC, backups, MFA |

---

## Technical details

- **Type:** Frontend-only (Alpine.js) — no server round-trip, no data stored
- **Framework integration:** Laravel package with Blade view
- **Namespace:** `URLCV\Iso27001Readiness`
- **Service provider:** `URLCV\Iso27001Readiness\Laravel\Iso27001ReadinessServiceProvider`

---

## Installation (via the main URLCV app)

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/urlcv/iso27001-readiness.git" }
],
"require": {
    "urlcv/iso27001-readiness": "dev-main"
}
```

```bash
composer update urlcv/iso27001-readiness
php artisan tools:sync
```

---

## Part of URLCV

[URLCV](https://urlcv.com) is a recruitment platform for agencies. It helps recruiters present candidates professionally with branded shortlists, structured CV parsing, and candidate tracking.

Explore all free tools at **[urlcv.com/tools](https://urlcv.com/tools)**.

---

## License

MIT

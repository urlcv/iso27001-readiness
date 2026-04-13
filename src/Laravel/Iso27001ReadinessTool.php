<?php

declare(strict_types=1);

namespace URLCV\Iso27001Readiness\Laravel;

use App\Tools\Contracts\ToolInterface;

class Iso27001ReadinessTool implements ToolInterface
{
    public function slug(): string
    {
        return 'iso27001-readiness';
    }

    public function name(): string
    {
        return 'ISO 27001 Readiness';
    }

    public function summary(): string
    {
        return 'Traffic-light self-assessment to gauge how ready your organisation is for ISO 27001:2022 certification.';
    }

    public function descriptionMd(): ?string
    {
        return <<<'MD'
## ISO 27001 Readiness Assessment

Assess your organisation's readiness for ISO 27001:2022 certification using a Vanta-style traffic-light system. Rate each control as **Red** (not started), **Amber** (in progress), or **Green** (fully implemented) and see your overall readiness at a glance.

### What you get

- **Traffic-light dashboard** — see exactly how many controls are green, amber, or red across every area
- **ISMS clause coverage** — assess mandatory requirements from clauses 4–10
- **Annex A control themes** — organisational, people, physical, and technological controls
- **Public claims gap analysis** — flag mismatches between what you claim publicly and what is actually in place
- **Priority list** — red items surfaced first so you know where to focus
- **Export** — copy a markdown summary to clipboard for sharing with stakeholders

### How it works

1. Toggle which security claims your organisation makes publicly
2. Set a Red / Amber / Green status for each control across ISMS clauses and Annex A themes
3. The dashboard updates live — your goal is **all green lights**

No data leaves your browser. Everything runs client-side.
MD;
    }

    public function categories(): array
    {
        return ['security'];
    }

    public function tags(): array
    {
        return ['iso27001', 'compliance', 'audit', 'infosec', 'governance'];
    }

    public function inputSchema(): array
    {
        return [];
    }

    public function run(array $input): array
    {
        return [];
    }

    public function mode(): string
    {
        return 'frontend';
    }

    public function isAsync(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function frontendView(): ?string
    {
        return 'iso27001-readiness::iso27001-readiness';
    }

    public function rateLimitPerMinute(): int
    {
        return 60;
    }

    public function cacheTtlSeconds(): int
    {
        return 0;
    }

    public function sortWeight(): int
    {
        return 80;
    }
}

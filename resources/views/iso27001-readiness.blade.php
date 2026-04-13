{{--
  ISO 27001 Readiness — fully client-side Alpine.js tool.
  Vanta-style traffic-light assessment. No server round-trip.
--}}
<div
    x-data="iso27001Readiness()"
    x-init="init()"
    class="space-y-6"
>
    {{-- Dashboard summary (always visible) --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row items-center gap-6">
            {{-- Progress ring --}}
            <div class="relative shrink-0">
                <svg class="w-28 h-28 -rotate-90" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                    <circle cx="60" cy="60" r="52" fill="none" stroke-width="10" stroke-linecap="round"
                        :stroke="readyPercent === 100 ? '#22c55e' : (readyPercent >= 60 ? '#f59e0b' : '#ef4444')"
                        :stroke-dasharray="(readyPercent / 100 * 326.7) + ' 326.7'"
                    />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold text-gray-900" x-text="readyPercent + '%'"></span>
                    <span class="text-xs text-gray-500">green</span>
                </div>
            </div>
            <div class="flex-1 min-w-0 text-center sm:text-left">
                <h2 class="text-lg font-semibold text-gray-900 mb-1">
                    <span x-show="readyPercent === 100" x-cloak>All green — you are ready!</span>
                    <span x-show="readyPercent >= 80 && readyPercent < 100">Nearly there</span>
                    <span x-show="readyPercent >= 40 && readyPercent < 80">Making progress</span>
                    <span x-show="readyPercent < 40">Getting started</span>
                </h2>
                <p class="text-sm text-gray-500 mb-3" x-text="greenCount + ' of ' + totalControls + ' controls are green'"></p>
                <div class="flex gap-3 justify-center sm:justify-start text-xs">
                    <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500"></span> <span x-text="greenCount"></span> Green</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-400"></span> <span x-text="amberCount"></span> Amber</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500"></span> <span x-text="redCount"></span> Red</span>
                </div>
            </div>
            {{-- Per-section mini bars --}}
            <div class="hidden md:flex flex-col gap-1.5 shrink-0 w-48">
                <template x-for="section in allSections" :key="section.id">
                    <div>
                        <div class="flex justify-between text-xs text-gray-500 mb-0.5">
                            <span x-text="section.shortName"></span>
                            <span x-text="sectionGreenPercent(section.id) + '%'"></span>
                        </div>
                        <div class="flex h-2 rounded-full overflow-hidden bg-gray-100">
                            <div class="bg-green-500 transition-all" :style="'width:' + sectionGreenPercent(section.id) + '%'"></div>
                            <div class="bg-amber-400 transition-all" :style="'width:' + sectionAmberPercent(section.id) + '%'"></div>
                            <div class="bg-red-500 transition-all" :style="'width:' + sectionRedPercent(section.id) + '%'"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Claim gaps --}}
    <template x-if="claimGaps.length > 0">
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <h3 class="text-sm font-semibold text-amber-800 mb-2">Claim gaps detected</h3>
            <p class="text-xs text-amber-700 mb-3">You publicly claim the following, but the mapped controls are not yet green.</p>
            <div class="space-y-1.5">
                <template x-for="gap in claimGaps" :key="gap.claim">
                    <div class="flex items-start gap-2 text-sm text-amber-800">
                        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.168 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 6a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                        <span><strong x-text="gap.claim"></strong> — <span class="text-amber-600" x-text="gap.reason"></span></span>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-4 -mb-px">
            <template x-for="tab in tabs" :key="tab.id">
                <button
                    @click="activeTab = tab.id"
                    :class="activeTab === tab.id
                        ? 'border-primary-500 text-primary-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap border-b-2 py-3 px-1 text-sm font-medium transition-colors"
                    x-text="tab.label"
                ></button>
            </template>
        </nav>
    </div>

    {{-- Tab: Public Claims --}}
    <div x-show="activeTab === 'claims'" x-cloak class="space-y-3">
        <p class="text-sm text-gray-500">Toggle the security claims your organisation makes publicly (on your website, trust centre, sales materials, etc.).</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <template x-for="claim in claims" :key="claim.id">
                <label class="flex items-start gap-3 p-3 rounded-lg border transition-colors cursor-pointer"
                    :class="claim.active ? 'border-primary-200 bg-primary-50' : 'border-gray-200 bg-white hover:bg-gray-50'">
                    <input type="checkbox" x-model="claim.active" class="mt-0.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-900" x-text="claim.label"></div>
                        <div class="text-xs text-gray-400 mt-0.5" x-text="'Maps to: ' + claim.mapLabels"></div>
                    </div>
                </label>
            </template>
        </div>
    </div>

    {{-- Tab: ISMS Clauses --}}
    <div x-show="activeTab === 'isms'" x-cloak class="space-y-3">
        <p class="text-sm text-gray-500">Assess each mandatory ISMS requirement (ISO 27001:2022 clauses 4–10).</p>
        <template x-for="section in ismsSections" :key="section.id">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <button @click="section.open = !section.open" class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-gray-900" x-text="section.name"></span>
                        <span class="text-xs text-gray-400" x-text="sectionSummaryText(section.id)"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex gap-0.5">
                            <template x-for="c in controlsForSection(section.id)" :key="c.id">
                                <span class="w-2.5 h-2.5 rounded-full" :class="statusDotClass(c.status)"></span>
                            </template>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="section.open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>
                <div x-show="section.open" x-collapse>
                    <div class="border-t border-gray-100 divide-y divide-gray-100">
                        <template x-for="control in controlsForSection(section.id)" :key="control.id">
                            <div class="px-4 py-3 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="text-sm text-gray-800" x-text="control.label"></div>
                                    <div class="text-xs text-gray-400 mt-0.5" x-text="control.hint" x-show="control.hint"></div>
                                </div>
                                <div class="flex gap-1 shrink-0">
                                    <button @click="control.status = 'red'" class="w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all"
                                        :class="control.status === 'red' ? 'border-red-500 bg-red-500' : 'border-gray-300 hover:border-red-300'">
                                        <span x-show="control.status === 'red'" class="text-white text-xs font-bold">R</span>
                                    </button>
                                    <button @click="control.status = 'amber'" class="w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all"
                                        :class="control.status === 'amber' ? 'border-amber-400 bg-amber-400' : 'border-gray-300 hover:border-amber-300'">
                                        <span x-show="control.status === 'amber'" class="text-white text-xs font-bold">A</span>
                                    </button>
                                    <button @click="control.status = 'green'" class="w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all"
                                        :class="control.status === 'green' ? 'border-green-500 bg-green-500' : 'border-gray-300 hover:border-green-300'">
                                        <span x-show="control.status === 'green'" class="text-white text-xs font-bold">G</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Tab: Annex A Controls --}}
    <div x-show="activeTab === 'annexa'" x-cloak class="space-y-3">
        <p class="text-sm text-gray-500">Assess your Annex A controls across the four ISO 27001:2022 themes.</p>
        <template x-for="section in annexASections" :key="section.id">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <button @click="section.open = !section.open" class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-gray-900" x-text="section.name"></span>
                        <span class="text-xs text-gray-400" x-text="sectionSummaryText(section.id)"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex gap-0.5">
                            <template x-for="c in controlsForSection(section.id)" :key="c.id">
                                <span class="w-2.5 h-2.5 rounded-full" :class="statusDotClass(c.status)"></span>
                            </template>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="section.open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>
                <div x-show="section.open" x-collapse>
                    <div class="border-t border-gray-100 divide-y divide-gray-100">
                        <template x-for="control in controlsForSection(section.id)" :key="control.id">
                            <div class="px-4 py-3 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="text-sm text-gray-800" x-text="control.label"></div>
                                    <div class="text-xs text-gray-400 mt-0.5" x-text="control.hint" x-show="control.hint"></div>
                                </div>
                                <div class="flex gap-1 shrink-0">
                                    <button @click="control.status = 'red'" class="w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all"
                                        :class="control.status === 'red' ? 'border-red-500 bg-red-500' : 'border-gray-300 hover:border-red-300'">
                                        <span x-show="control.status === 'red'" class="text-white text-xs font-bold">R</span>
                                    </button>
                                    <button @click="control.status = 'amber'" class="w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all"
                                        :class="control.status === 'amber' ? 'border-amber-400 bg-amber-400' : 'border-gray-300 hover:border-amber-300'">
                                        <span x-show="control.status === 'amber'" class="text-white text-xs font-bold">A</span>
                                    </button>
                                    <button @click="control.status = 'green'" class="w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all"
                                        :class="control.status === 'green' ? 'border-green-500 bg-green-500' : 'border-gray-300 hover:border-green-300'">
                                        <span x-show="control.status === 'green'" class="text-white text-xs font-bold">G</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Tab: Priorities --}}
    <div x-show="activeTab === 'priorities'" x-cloak class="space-y-4">
        <template x-if="redControls.length === 0 && amberControls.length === 0">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-green-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-lg font-semibold text-gray-900 mb-1">All green!</p>
                <p class="text-sm text-gray-500">Every control is fully implemented. You are ready for ISO 27001 certification.</p>
            </div>
        </template>

        <template x-if="redControls.length > 0">
            <div>
                <h3 class="text-sm font-semibold text-red-700 mb-2">Red — not started (<span x-text="redControls.length"></span>)</h3>
                <div class="space-y-1.5">
                    <template x-for="c in redControls" :key="c.id">
                        <div class="flex items-center gap-3 px-3 py-2 bg-red-50 border border-red-100 rounded-lg text-sm">
                            <span class="w-3 h-3 rounded-full bg-red-500 shrink-0"></span>
                            <span class="text-gray-800" x-text="c.label"></span>
                            <span class="ml-auto text-xs text-gray-400 shrink-0" x-text="c.sectionName"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <template x-if="amberControls.length > 0">
            <div>
                <h3 class="text-sm font-semibold text-amber-700 mb-2">Amber — in progress (<span x-text="amberControls.length"></span>)</h3>
                <div class="space-y-1.5">
                    <template x-for="c in amberControls" :key="c.id">
                        <div class="flex items-center gap-3 px-3 py-2 bg-amber-50 border border-amber-100 rounded-lg text-sm">
                            <span class="w-3 h-3 rounded-full bg-amber-400 shrink-0"></span>
                            <span class="text-gray-800" x-text="c.label"></span>
                            <span class="ml-auto text-xs text-gray-400 shrink-0" x-text="c.sectionName"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- Copy summary --}}
        <div class="pt-2">
            <button @click="copySummary()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                <span x-text="copied ? 'Copied!' : 'Copy summary as Markdown'"></span>
            </button>
        </div>
    </div>

    {{-- Tip --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
        <strong>Tip:</strong> This is a self-assessment tool based on ISO 27001:2022 requirements. For actual certification you will need a formal audit by an accredited certification body. Use this to identify gaps and track progress internally before engaging an auditor.
    </div>
</div>

@push('scripts')
<script>
function iso27001Readiness() {
    return {
        activeTab: 'claims',
        copied: false,

        tabs: [
            { id: 'claims', label: 'Public Claims' },
            { id: 'isms', label: 'ISMS Clauses' },
            { id: 'annexa', label: 'Annex A Controls' },
            { id: 'priorities', label: 'Priorities' },
        ],

        claims: [
            { id: 'c1',  active: false, label: 'We have a dedicated CISO or security team', mapLabels: 'Clause 5, A.5', maps: ['isms5', 'a5'] },
            { id: 'c2',  active: false, label: 'Data is encrypted at rest', mapLabels: 'A.8', maps: ['a8'] },
            { id: 'c3',  active: false, label: 'Data is encrypted in transit', mapLabels: 'A.8', maps: ['a8'] },
            { id: 'c4',  active: false, label: 'We conduct regular penetration tests', mapLabels: 'Clause 9, A.8', maps: ['isms9', 'a8'] },
            { id: 'c5',  active: false, label: 'We have an incident response plan', mapLabels: 'Clause 8, A.5', maps: ['isms8', 'a5'] },
            { id: 'c6',  active: false, label: 'All staff complete security awareness training', mapLabels: 'Clause 7, A.6', maps: ['isms7', 'a6'] },
            { id: 'c7',  active: false, label: 'We perform background checks on staff', mapLabels: 'A.6', maps: ['a6'] },
            { id: 'c8',  active: false, label: 'MFA is enforced for all users', mapLabels: 'A.8', maps: ['a8'] },
            { id: 'c9',  active: false, label: 'We perform regular access reviews', mapLabels: 'A.5, A.8', maps: ['a5', 'a8'] },
            { id: 'c10', active: false, label: 'We have a business continuity plan', mapLabels: 'A.5', maps: ['a5'] },
            { id: 'c11', active: false, label: 'We assess vendor/supplier security', mapLabels: 'A.5', maps: ['a5'] },
            { id: 'c12', active: false, label: 'We have a data classification policy', mapLabels: 'A.5, A.8', maps: ['a5', 'a8'] },
        ],

        controls: [
            // ISMS Clause 4 — Context
            { id: 'i4a', section: 'isms4', status: 'red', label: 'ISMS scope defined and documented', hint: 'Boundaries, applicability, interfaces' },
            { id: 'i4b', section: 'isms4', status: 'red', label: 'Interested parties identified', hint: 'Customers, regulators, partners, staff' },
            { id: 'i4c', section: 'isms4', status: 'red', label: 'Internal and external issues documented', hint: 'PESTLE/SWOT or equivalent analysis' },

            // ISMS Clause 5 — Leadership
            { id: 'i5a', section: 'isms5', status: 'red', label: 'Top management commitment demonstrated', hint: 'Board-level sponsorship, resource allocation' },
            { id: 'i5b', section: 'isms5', status: 'red', label: 'Information security policy approved and communicated', hint: '' },
            { id: 'i5c', section: 'isms5', status: 'red', label: 'Roles, responsibilities, and authorities assigned', hint: 'CISO, risk owners, asset owners' },

            // ISMS Clause 6 — Planning
            { id: 'i6a', section: 'isms6', status: 'red', label: 'Risk assessment process established', hint: 'Methodology, criteria, risk register' },
            { id: 'i6b', section: 'isms6', status: 'red', label: 'Risk treatment plan documented', hint: 'Controls selected, residual risk accepted' },
            { id: 'i6c', section: 'isms6', status: 'red', label: 'Information security objectives set', hint: 'Measurable, monitored, communicated' },

            // ISMS Clause 7 — Support
            { id: 'i7a', section: 'isms7', status: 'red', label: 'Resources allocated to ISMS', hint: 'Budget, people, tools' },
            { id: 'i7b', section: 'isms7', status: 'red', label: 'Staff competence assessed and recorded', hint: 'Training records, skill matrix' },
            { id: 'i7c', section: 'isms7', status: 'red', label: 'Security awareness programme in place', hint: 'Regular training, phishing simulations' },
            { id: 'i7d', section: 'isms7', status: 'red', label: 'Documented information procedures', hint: 'Document control, versioning, retention' },

            // ISMS Clause 8 — Operation
            { id: 'i8a', section: 'isms8', status: 'red', label: 'Risk treatment plan implemented', hint: 'Controls operational, evidence collected' },
            { id: 'i8b', section: 'isms8', status: 'red', label: 'Operational planning and control', hint: 'Change management, outsourced processes' },

            // ISMS Clause 9 — Performance evaluation
            { id: 'i9a', section: 'isms9', status: 'red', label: 'Monitoring and measurement in place', hint: 'KPIs, metrics, dashboards' },
            { id: 'i9b', section: 'isms9', status: 'red', label: 'Internal audit conducted', hint: 'Independent auditor, documented findings' },
            { id: 'i9c', section: 'isms9', status: 'red', label: 'Management review completed', hint: 'At least annually, minutes recorded' },

            // ISMS Clause 10 — Improvement
            { id: 'i10a', section: 'isms10', status: 'red', label: 'Nonconformity and corrective action process', hint: 'Root cause analysis, CAPA log' },
            { id: 'i10b', section: 'isms10', status: 'red', label: 'Continual improvement demonstrated', hint: 'Trend analysis, lessons learned' },

            // Annex A.5 — Organisational controls
            { id: 'a5a', section: 'a5', status: 'red', label: 'Information security policies published', hint: 'Approved, reviewed, accessible to staff' },
            { id: 'a5b', section: 'a5', status: 'red', label: 'Roles and responsibilities defined', hint: 'Segregation of duties where appropriate' },
            { id: 'a5c', section: 'a5', status: 'red', label: 'Threat intelligence process', hint: 'Monitoring threat feeds, CVE tracking' },
            { id: 'a5d', section: 'a5', status: 'red', label: 'Asset inventory and ownership', hint: 'Hardware, software, data, services catalogued' },
            { id: 'a5e', section: 'a5', status: 'red', label: 'Access control policy', hint: 'Least privilege, need-to-know basis' },
            { id: 'a5f', section: 'a5', status: 'red', label: 'Supplier security management', hint: 'Vendor risk assessments, contract clauses' },
            { id: 'a5g', section: 'a5', status: 'red', label: 'Incident management process', hint: 'Detection, reporting, response, recovery' },
            { id: 'a5h', section: 'a5', status: 'red', label: 'Business continuity planning', hint: 'BCP/DR plans tested, RPO/RTO defined' },
            { id: 'a5i', section: 'a5', status: 'red', label: 'Legal and regulatory compliance', hint: 'GDPR, contractual obligations, licences' },
            { id: 'a5j', section: 'a5', status: 'red', label: 'Data classification scheme', hint: 'Confidential, internal, public labelling' },

            // Annex A.6 — People controls
            { id: 'a6a', section: 'a6', status: 'red', label: 'Pre-employment screening', hint: 'Background checks, reference verification' },
            { id: 'a6b', section: 'a6', status: 'red', label: 'Employment terms and conditions', hint: 'Security clauses in contracts, NDAs' },
            { id: 'a6c', section: 'a6', status: 'red', label: 'Security awareness and training', hint: 'Onboarding + ongoing, role-based' },
            { id: 'a6d', section: 'a6', status: 'red', label: 'Disciplinary process', hint: 'For security policy violations' },
            { id: 'a6e', section: 'a6', status: 'red', label: 'Responsibilities after termination', hint: 'Access revocation, asset return' },

            // Annex A.7 — Physical controls
            { id: 'a7a', section: 'a7', status: 'red', label: 'Physical security perimeters', hint: 'Office, data centre, server room access' },
            { id: 'a7b', section: 'a7', status: 'red', label: 'Physical entry controls', hint: 'Badge access, visitor logs, CCTV' },
            { id: 'a7c', section: 'a7', status: 'red', label: 'Securing offices and facilities', hint: 'Lock policy, alarm systems' },
            { id: 'a7d', section: 'a7', status: 'red', label: 'Clear desk and clear screen', hint: 'Policy enforced, spot-checked' },
            { id: 'a7e', section: 'a7', status: 'red', label: 'Equipment maintenance and disposal', hint: 'Secure wiping, certified destruction' },

            // Annex A.8 — Technological controls
            { id: 'a8a', section: 'a8', status: 'red', label: 'Endpoint device management', hint: 'MDM, patching, disk encryption' },
            { id: 'a8b', section: 'a8', status: 'red', label: 'Privileged access management', hint: 'PAM tool, just-in-time access, MFA' },
            { id: 'a8c', section: 'a8', status: 'red', label: 'Data encryption at rest', hint: 'AES-256, key management' },
            { id: 'a8d', section: 'a8', status: 'red', label: 'Data encryption in transit', hint: 'TLS 1.2+, certificate management' },
            { id: 'a8e', section: 'a8', status: 'red', label: 'Vulnerability management', hint: 'Scanning, patching SLAs, pen testing' },
            { id: 'a8f', section: 'a8', status: 'red', label: 'Logging and monitoring', hint: 'SIEM, audit logs, alerting' },
            { id: 'a8g', section: 'a8', status: 'red', label: 'Network security', hint: 'Firewalls, segmentation, IDS/IPS' },
            { id: 'a8h', section: 'a8', status: 'red', label: 'Secure development lifecycle', hint: 'SAST/DAST, code review, change control' },
            { id: 'a8i', section: 'a8', status: 'red', label: 'Backup and recovery', hint: 'Automated backups, restore testing' },
            { id: 'a8j', section: 'a8', status: 'red', label: 'Multi-factor authentication', hint: 'Enforced for all users and admin access' },
        ],

        ismsSections: [
            { id: 'isms4',  name: 'Clause 4 — Context of the Organisation', shortName: 'Cl.4 Context', open: false },
            { id: 'isms5',  name: 'Clause 5 — Leadership', shortName: 'Cl.5 Leadership', open: false },
            { id: 'isms6',  name: 'Clause 6 — Planning', shortName: 'Cl.6 Planning', open: false },
            { id: 'isms7',  name: 'Clause 7 — Support', shortName: 'Cl.7 Support', open: false },
            { id: 'isms8',  name: 'Clause 8 — Operation', shortName: 'Cl.8 Operation', open: false },
            { id: 'isms9',  name: 'Clause 9 — Performance Evaluation', shortName: 'Cl.9 Evaluation', open: false },
            { id: 'isms10', name: 'Clause 10 — Improvement', shortName: 'Cl.10 Improvement', open: false },
        ],

        annexASections: [
            { id: 'a5', name: 'A.5 — Organisational Controls', shortName: 'A.5 Org', open: false },
            { id: 'a6', name: 'A.6 — People Controls', shortName: 'A.6 People', open: false },
            { id: 'a7', name: 'A.7 — Physical Controls', shortName: 'A.7 Physical', open: false },
            { id: 'a8', name: 'A.8 — Technological Controls', shortName: 'A.8 Tech', open: false },
        ],

        init() {},

        get allSections() {
            return [...this.ismsSections, ...this.annexASections];
        },

        get totalControls() { return this.controls.length; },
        get greenCount() { return this.controls.filter(c => c.status === 'green').length; },
        get amberCount() { return this.controls.filter(c => c.status === 'amber').length; },
        get redCount()   { return this.controls.filter(c => c.status === 'red').length; },
        get readyPercent() { return this.totalControls ? Math.round((this.greenCount / this.totalControls) * 100) : 0; },

        controlsForSection(sectionId) {
            return this.controls.filter(c => c.section === sectionId);
        },

        sectionGreenPercent(sectionId) {
            const s = this.controlsForSection(sectionId);
            return s.length ? Math.round((s.filter(c => c.status === 'green').length / s.length) * 100) : 0;
        },
        sectionAmberPercent(sectionId) {
            const s = this.controlsForSection(sectionId);
            return s.length ? Math.round((s.filter(c => c.status === 'amber').length / s.length) * 100) : 0;
        },
        sectionRedPercent(sectionId) {
            const s = this.controlsForSection(sectionId);
            return s.length ? Math.round((s.filter(c => c.status === 'red').length / s.length) * 100) : 0;
        },

        sectionSummaryText(sectionId) {
            const s = this.controlsForSection(sectionId);
            const g = s.filter(c => c.status === 'green').length;
            return g + '/' + s.length + ' green';
        },

        statusDotClass(status) {
            if (status === 'green') return 'bg-green-500';
            if (status === 'amber') return 'bg-amber-400';
            return 'bg-red-500';
        },

        get claimGaps() {
            const gaps = [];
            for (const claim of this.claims) {
                if (!claim.active) continue;
                const mapped = this.controls.filter(c => claim.maps.includes(c.section));
                const nonGreen = mapped.filter(c => c.status !== 'green');
                if (nonGreen.length > 0) {
                    const redCount = nonGreen.filter(c => c.status === 'red').length;
                    const amberCount = nonGreen.filter(c => c.status === 'amber').length;
                    const parts = [];
                    if (redCount) parts.push(redCount + ' red');
                    if (amberCount) parts.push(amberCount + ' amber');
                    gaps.push({
                        claim: claim.label,
                        reason: parts.join(', ') + ' control' + (nonGreen.length === 1 ? '' : 's') + ' in mapped areas'
                    });
                }
            }
            return gaps;
        },

        get redControls() {
            return this.controls.filter(c => c.status === 'red').map(c => ({
                ...c,
                sectionName: this.sectionLabel(c.section),
            }));
        },

        get amberControls() {
            return this.controls.filter(c => c.status === 'amber').map(c => ({
                ...c,
                sectionName: this.sectionLabel(c.section),
            }));
        },

        sectionLabel(sectionId) {
            const s = this.allSections.find(s => s.id === sectionId);
            return s ? s.shortName : sectionId;
        },

        copySummary() {
            const lines = [
                '# ISO 27001 Readiness Summary',
                '',
                `**Overall:** ${this.readyPercent}% green (${this.greenCount}/${this.totalControls} controls)`,
                '',
                '## Per section',
                '',
            ];

            for (const section of this.allSections) {
                const ctrls = this.controlsForSection(section.id);
                const g = ctrls.filter(c => c.status === 'green').length;
                const a = ctrls.filter(c => c.status === 'amber').length;
                const r = ctrls.filter(c => c.status === 'red').length;
                lines.push(`- **${section.shortName}:** ${g} green, ${a} amber, ${r} red`);
            }

            if (this.claimGaps.length) {
                lines.push('', '## Claim gaps', '');
                for (const gap of this.claimGaps) {
                    lines.push(`- **${gap.claim}** — ${gap.reason}`);
                }
            }

            if (this.redControls.length) {
                lines.push('', '## Red items (priority)', '');
                for (const c of this.redControls) {
                    lines.push(`- [ ] ${c.label} (${c.sectionName})`);
                }
            }

            if (this.amberControls.length) {
                lines.push('', '## Amber items (in progress)', '');
                for (const c of this.amberControls) {
                    lines.push(`- [ ] ${c.label} (${c.sectionName})`);
                }
            }

            lines.push('', '---', '*Generated by urlcv.com/tools/iso27001-readiness*');

            navigator.clipboard.writeText(lines.join('\n')).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        },
    };
}
</script>
@endpush

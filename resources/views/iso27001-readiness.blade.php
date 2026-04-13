{{--
  ISO 27001 Readiness — fully client-side Alpine.js tool.
  Improved for mobile usability, clearer status controls, and local persistence.
--}}
<div
    x-data="iso27001Readiness()"
    x-init="init()"
    class="space-y-6"
>
    <div class="grid gap-4 lg:grid-cols-2">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                <div class="relative shrink-0 mx-auto sm:mx-0">
                    <svg class="w-28 h-28 -rotate-90" viewBox="0 0 120 120" aria-hidden="true">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                        <circle
                            cx="60"
                            cy="60"
                            r="52"
                            fill="none"
                            stroke-width="10"
                            stroke-linecap="round"
                            :stroke="readyPercent === 100 ? '#16a34a' : (readyPercent >= 60 ? '#d97706' : '#dc2626')"
                            :stroke-dasharray="(readyPercent / 100 * 326.7) + ' 326.7'"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-gray-900" x-text="readyPercent + '%'"></span>
                        <span class="text-xs font-medium uppercase tracking-wide text-gray-500">ready</span>
                    </div>
                </div>

                <div class="flex-1 min-w-0 space-y-3 text-center sm:text-left">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900" x-text="progressHeadline"></h2>
                        <p class="mt-1 text-sm text-gray-600" x-text="greenCount + ' of ' + totalControls + ' controls are green'"></p>
                    </div>

                    <div class="grid gap-2 grid-cols-3">
                        <div class="rounded-lg border border-green-100 bg-green-50 px-3 py-2 text-left">
                            <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Green</p>
                            <p class="mt-0.5 text-xl font-semibold text-green-900" x-text="greenCount"></p>
                        </div>
                        <div class="rounded-lg border border-amber-100 bg-amber-50 px-3 py-2 text-left">
                            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Amber</p>
                            <p class="mt-0.5 text-xl font-semibold text-amber-900" x-text="amberCount"></p>
                        </div>
                        <div class="rounded-lg border border-red-100 bg-red-50 px-3 py-2 text-left">
                            <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Red</p>
                            <p class="mt-0.5 text-xl font-semibold text-red-900" x-text="redCount"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Section breakdown</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    <template x-for="section in allSections" :key="'bar-' + section.id">
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span x-text="section.shortName"></span>
                                <span x-text="sectionGreenPercent(section.id) + '%'"></span>
                            </div>
                            <div class="flex h-2 rounded-full overflow-hidden bg-gray-100">
                                <div class="bg-green-500 transition-all" :style="'width:' + sectionGreenPercent(section.id) + '%'"></div>
                                <div class="bg-amber-400 transition-all" :style="'width:' + sectionAmberPercent(section.id) + '%'"></div>
                                <div class="bg-red-400 transition-all" :style="'width:' + sectionRedPercent(section.id) + '%'"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-sky-100 bg-gradient-to-br from-sky-50 via-white to-emerald-50 p-6 shadow-sm">
            <div class="flex flex-col gap-5 h-full">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">How To Use It</p>
                    <div class="mt-3 space-y-3 text-sm text-slate-700">
                        <p><strong>1.</strong> Toggle the public security claims your organisation makes.</p>
                        <p><strong>2.</strong> Set each ISO control to Red, Amber, or Green using the status buttons below.</p>
                        <p><strong>3.</strong> Use the priorities tab to work through gaps and export the summary when you are done.</p>
                    </div>
                </div>

                <div class="rounded-xl border border-sky-100 bg-white/80 px-4 py-3 text-sm text-slate-700">
                    <p class="font-medium text-slate-900">Saved in this browser</p>
                    <p class="mt-1 text-slate-600" x-text="lastSavedAt ? 'Last updated ' + formatTimestamp(lastSavedAt) : 'No saved assessment yet.'"></p>
                </div>

                <div class="mt-auto flex flex-wrap gap-2">
                    <button
                        type="button"
                        @click="activeTab = 'priorities'"
                        class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-slate-800"
                    >
                        Review Priorities
                    </button>
                    <button
                        type="button"
                        @click="copySummary()"
                        class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50"
                    >
                        <span x-text="copied ? 'Copied summary' : 'Copy summary'"></span>
                    </button>
                    <button
                        type="button"
                        @click="resetAssessment()"
                        class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-700 transition-colors hover:bg-red-50"
                    >
                        Reset Assessment
                    </button>
                </div>

                <p x-show="copyError" x-cloak class="text-sm text-red-600" x-text="copyError"></p>
            </div>
        </div>
    </div>

    <template x-if="claimGaps.length > 0">
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-amber-900">Claim gaps detected</h3>
                    <p class="mt-1 text-sm text-amber-800">These claims are active, but some mapped controls are still red or amber.</p>
                </div>
                <button
                    type="button"
                    @click="activeTab = 'priorities'"
                    class="inline-flex items-center justify-center rounded-lg border border-amber-300 bg-white px-3 py-2 text-sm font-medium text-amber-900 transition-colors hover:bg-amber-100"
                >
                    Open priorities
                </button>
            </div>

            <div class="mt-4 space-y-3">
                <template x-for="gap in claimGaps" :key="gap.claim">
                    <div class="rounded-xl border border-amber-200 bg-white px-4 py-3">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-amber-950" x-text="gap.claim"></p>
                                <p class="mt-1 text-sm text-amber-800" x-text="gap.reason"></p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="sectionId in gap.sections" :key="gap.claim + '-' + sectionId">
                                    <button
                                        type="button"
                                        @click="focusSection(sectionId)"
                                        class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-900 transition-colors hover:bg-amber-100"
                                        x-text="'Open ' + sectionLabel(sectionId)"
                                    ></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <div class="overflow-x-auto pb-1">
        <nav class="flex min-w-max gap-2" aria-label="Assessment tabs">
            <template x-for="tab in tabs" :key="tab.id">
                <button
                    type="button"
                    @click="activeTab = tab.id"
                    :class="activeTab === tab.id
                        ? 'border-primary-600 bg-primary-600 text-white shadow-sm'
                        : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:text-gray-900'"
                    class="inline-flex items-center rounded-full border px-4 py-2.5 text-sm font-medium transition-colors"
                    x-text="tab.label"
                ></button>
            </template>
        </nav>
    </div>

    <div x-show="activeTab !== 'claims'" x-cloak class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="w-full xl:max-w-md">
                <label for="iso-control-search" class="block text-sm font-medium text-gray-700">Find a control</label>
                <div class="relative mt-2">
                    <svg class="pointer-events-none absolute left-3 top-3.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                    <input
                        id="iso-control-search"
                        x-model.trim="controlQuery"
                        type="search"
                        placeholder="Search controls, hints, or sections"
                        class="w-full rounded-xl border border-gray-300 py-3 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                    >
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <div class="flex flex-wrap gap-2">
                    <template x-for="filter in statusFilters" :key="filter.id">
                        <button
                            type="button"
                            @click="statusFilter = filter.id"
                            :class="statusFilter === filter.id
                                ? 'border-slate-900 bg-slate-900 text-white'
                                : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'"
                            class="inline-flex items-center rounded-full border px-3 py-2 text-sm font-medium transition-colors"
                        >
                            <span x-text="filter.label"></span>
                            <span class="ml-2 rounded-full bg-black/10 px-2 py-0.5 text-xs" x-text="filterCount(filter.id)"></span>
                        </button>
                    </template>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                    <span x-text="filteredControls.length + ' of ' + totalControls + ' controls shown'"></span>
                    <button
                        x-show="hasActiveFilters"
                        x-cloak
                        type="button"
                        @click="clearFilters()"
                        class="inline-flex items-center rounded-full border border-gray-200 px-3 py-1 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                    >
                        Clear filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="activeTab === 'claims'" x-cloak class="space-y-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-600">Toggle the security claims your organisation makes publicly on your website, trust centre, security docs, or sales materials.</p>
            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <template x-for="claim in claims" :key="claim.id">
                    <label
                        class="flex items-start gap-3 rounded-xl border p-4 transition-colors cursor-pointer"
                        :class="claim.active ? 'border-primary-200 bg-primary-50' : 'border-gray-200 bg-white hover:bg-gray-50'"
                    >
                        <input
                            type="checkbox"
                            x-model="claim.active"
                            @change="persistData()"
                            class="mt-0.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                        >
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-gray-900" x-text="claim.label"></div>
                            <div class="mt-1 text-xs text-gray-500" x-text="'Maps to: ' + claim.mapLabels"></div>
                        </div>
                    </label>
                </template>
            </div>
        </div>
    </div>

    <div x-show="activeTab === 'isms'" x-cloak class="space-y-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">ISMS Clauses</h3>
                    <p class="mt-1 text-sm text-gray-600">Assess the mandatory ISO 27001:2022 requirements from clauses 4–10.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="setAllSectionsOpen(ismsSections, true)" class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">Expand all</button>
                    <button type="button" @click="setAllSectionsOpen(ismsSections, false)" class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">Collapse all</button>
                </div>
            </div>
        </div>

        <template x-for="section in ismsSections" :key="section.id">
            <div :id="'section-' + section.id" class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
                <button
                    type="button"
                    @click="section.open = !section.open"
                    class="w-full px-4 py-4 text-left transition-colors hover:bg-gray-50"
                >
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-base font-semibold text-gray-900" x-text="section.name"></span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600" x-text="sectionSummaryText(section.id)"></span>
                            </div>
                            <p class="mt-2 text-sm text-gray-500" x-text="filteredSectionText(section.id)"></p>
                        </div>
                        <div class="flex items-center justify-between gap-3 lg:justify-end">
                            <div class="hidden sm:flex gap-1.5">
                                <template x-for="c in controlsForSection(section.id)" :key="c.id">
                                    <span class="h-2.5 w-2.5 rounded-full" :class="statusDotClass(c.status)"></span>
                                </template>
                            </div>
                            <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform" :class="section.open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </button>

                <div x-show="section.open" x-collapse>
                    <div class="border-t border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <p class="text-sm text-gray-600" x-text="filteredSectionText(section.id)"></p>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="setSectionStatus(section.id, 'red')" class="inline-flex items-center rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-700 transition-colors hover:bg-red-50">Set section red</button>
                                <button type="button" @click="setSectionStatus(section.id, 'amber')" class="inline-flex items-center rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm font-medium text-amber-700 transition-colors hover:bg-amber-50">Set section amber</button>
                                <button type="button" @click="setSectionStatus(section.id, 'green')" class="inline-flex items-center rounded-lg border border-green-200 bg-white px-3 py-2 text-sm font-medium text-green-700 transition-colors hover:bg-green-50">Set section green</button>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <template x-if="filteredControlsForSection(section.id).length === 0">
                            <div class="px-4 py-6 text-sm text-gray-500">No controls in this section match the current search or status filter.</div>
                        </template>

                        <template x-for="control in filteredControlsForSection(section.id)" :key="control.id">
                            <div class="px-4 py-4">
                                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-sm font-medium text-gray-900" x-text="control.label"></p>
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
                                                :class="statusBadgeClass(control.status)"
                                                x-text="statusLabel(control.status)"
                                            ></span>
                                        </div>
                                        <p x-show="control.hint" x-cloak class="mt-1 text-sm text-gray-500" x-text="control.hint"></p>
                                    </div>

                                    <div class="flex flex-wrap gap-2 xl:justify-end">
                                        <template x-for="option in statusOptions" :key="control.id + '-' + option.id">
                                            <button
                                                type="button"
                                                @click="setControlStatus(control, option.id)"
                                                :aria-pressed="control.status === option.id"
                                                :class="statusButtonClass(control.status, option.id)"
                                                class="inline-flex min-w-[5.5rem] items-center justify-center rounded-xl border px-3 py-2.5 text-sm font-medium transition-all focus:outline-none focus:ring-2 focus:ring-offset-2"
                                                x-text="option.label"
                                            ></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div x-show="activeTab === 'annexa'" x-cloak class="space-y-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Annex A Controls</h3>
                    <p class="mt-1 text-sm text-gray-600">Assess the organisational, people, physical, and technology control themes across Annex A.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="setAllSectionsOpen(annexASections, true)" class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">Expand all</button>
                    <button type="button" @click="setAllSectionsOpen(annexASections, false)" class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">Collapse all</button>
                </div>
            </div>
        </div>

        <template x-for="section in annexASections" :key="section.id">
            <div :id="'section-' + section.id" class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
                <button
                    type="button"
                    @click="section.open = !section.open"
                    class="w-full px-4 py-4 text-left transition-colors hover:bg-gray-50"
                >
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-base font-semibold text-gray-900" x-text="section.name"></span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600" x-text="sectionSummaryText(section.id)"></span>
                            </div>
                            <p class="mt-2 text-sm text-gray-500" x-text="filteredSectionText(section.id)"></p>
                        </div>
                        <div class="flex items-center justify-between gap-3 lg:justify-end">
                            <div class="hidden sm:flex gap-1.5">
                                <template x-for="c in controlsForSection(section.id)" :key="c.id">
                                    <span class="h-2.5 w-2.5 rounded-full" :class="statusDotClass(c.status)"></span>
                                </template>
                            </div>
                            <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform" :class="section.open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </button>

                <div x-show="section.open" x-collapse>
                    <div class="border-t border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <p class="text-sm text-gray-600" x-text="filteredSectionText(section.id)"></p>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="setSectionStatus(section.id, 'red')" class="inline-flex items-center rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-700 transition-colors hover:bg-red-50">Set section red</button>
                                <button type="button" @click="setSectionStatus(section.id, 'amber')" class="inline-flex items-center rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm font-medium text-amber-700 transition-colors hover:bg-amber-50">Set section amber</button>
                                <button type="button" @click="setSectionStatus(section.id, 'green')" class="inline-flex items-center rounded-lg border border-green-200 bg-white px-3 py-2 text-sm font-medium text-green-700 transition-colors hover:bg-green-50">Set section green</button>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <template x-if="filteredControlsForSection(section.id).length === 0">
                            <div class="px-4 py-6 text-sm text-gray-500">No controls in this section match the current search or status filter.</div>
                        </template>

                        <template x-for="control in filteredControlsForSection(section.id)" :key="control.id">
                            <div class="px-4 py-4">
                                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-sm font-medium text-gray-900" x-text="control.label"></p>
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
                                                :class="statusBadgeClass(control.status)"
                                                x-text="statusLabel(control.status)"
                                            ></span>
                                        </div>
                                        <p x-show="control.hint" x-cloak class="mt-1 text-sm text-gray-500" x-text="control.hint"></p>
                                    </div>

                                    <div class="flex flex-wrap gap-2 xl:justify-end">
                                        <template x-for="option in statusOptions" :key="control.id + '-' + option.id">
                                            <button
                                                type="button"
                                                @click="setControlStatus(control, option.id)"
                                                :aria-pressed="control.status === option.id"
                                                :class="statusButtonClass(control.status, option.id)"
                                                class="inline-flex min-w-[5.5rem] items-center justify-center rounded-xl border px-3 py-2.5 text-sm font-medium transition-all focus:outline-none focus:ring-2 focus:ring-offset-2"
                                                x-text="option.label"
                                            ></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div x-show="activeTab === 'priorities'" x-cloak class="space-y-4">
        <template x-if="redControls.length === 0 && amberControls.length === 0">
            <div class="rounded-2xl border border-green-200 bg-green-50 px-6 py-10 text-center shadow-sm">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-4 text-lg font-semibold text-green-900">Everything is green</p>
                <p class="mt-1 text-sm text-green-800">The self-assessment is fully complete. Export the summary and use it as an internal review checklist before formal certification work.</p>
            </div>
        </template>

        <template x-if="filteredRedControls.length > 0">
            <div class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-red-800">Red priorities</h3>
                        <p class="text-sm text-red-700">Controls that are not started yet. These are the fastest way to improve your readiness score.</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700" x-text="filteredRedControls.length + ' showing'"></span>
                </div>

                <div class="mt-4 space-y-3">
                    <template x-for="c in filteredRedControls" :key="c.id">
                        <button
                            type="button"
                            @click="focusSection(c.section)"
                            class="w-full rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-left transition-colors hover:bg-red-100"
                        >
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="h-3 w-3 rounded-full bg-red-500"></span>
                                        <span class="text-sm font-medium text-gray-900" x-text="c.label"></span>
                                    </div>
                                    <p x-show="c.hint" x-cloak class="mt-1 text-sm text-gray-600" x-text="c.hint"></p>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600" x-text="'Open ' + c.sectionName"></span>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <template x-if="filteredAmberControls.length > 0">
            <div class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-amber-800">Amber priorities</h3>
                        <p class="text-sm text-amber-700">Controls already in progress. Close these out to turn effort into visible readiness.</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700" x-text="filteredAmberControls.length + ' showing'"></span>
                </div>

                <div class="mt-4 space-y-3">
                    <template x-for="c in filteredAmberControls" :key="c.id">
                        <button
                            type="button"
                            @click="focusSection(c.section)"
                            class="w-full rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-left transition-colors hover:bg-amber-100"
                        >
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                                        <span class="text-sm font-medium text-gray-900" x-text="c.label"></span>
                                    </div>
                                    <p x-show="c.hint" x-cloak class="mt-1 text-sm text-gray-600" x-text="c.hint"></p>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600" x-text="'Open ' + c.sectionName"></span>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <template x-if="redControls.length > 0 || amberControls.length > 0">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Export summary</h3>
                        <p class="mt-1 text-sm text-gray-600">Copy a Markdown summary for internal reviews, audit prep, or a trust centre workstream.</p>
                    </div>
                    <button
                        type="button"
                        @click="copySummary()"
                        class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-800"
                    >
                        <span x-text="copied ? 'Copied summary' : 'Copy summary as Markdown'"></span>
                    </button>
                </div>

                <p x-show="copyError" x-cloak class="mt-3 text-sm text-red-600" x-text="copyError"></p>
            </div>
        </template>

        <template x-if="redControls.length > 0 || amberControls.length > 0">
            <div x-show="filteredRedControls.length === 0 && filteredAmberControls.length === 0" x-cloak class="rounded-2xl border border-gray-200 bg-gray-50 px-6 py-8 text-center text-sm text-gray-600 shadow-sm">
                No priority items match the current filters.
            </div>
        </template>
    </div>

    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900 shadow-sm">
        <strong>Note:</strong> This is a self-assessment tool based on ISO 27001:2022 requirements. Formal certification still requires an audit by an accredited certification body.
    </div>
</div>

@push('scripts')
<script>
function iso27001Readiness() {
    return {
        activeTab: 'claims',
        copied: false,
        copyError: '',
        controlQuery: '',
        statusFilter: 'all',
        lastSavedAt: '',
        storageKey: 'urlcv.iso27001.readiness.v2',

        tabs: [
            { id: 'claims', label: 'Public Claims' },
            { id: 'isms', label: 'ISMS Clauses' },
            { id: 'annexa', label: 'Annex A Controls' },
            { id: 'priorities', label: 'Priorities' },
        ],

        statusFilters: [
            { id: 'all', label: 'All' },
            { id: 'gaps', label: 'Needs work' },
            { id: 'red', label: 'Red' },
            { id: 'amber', label: 'Amber' },
            { id: 'green', label: 'Green' },
        ],

        statusOptions: [
            { id: 'red', label: 'Red' },
            { id: 'amber', label: 'Amber' },
            { id: 'green', label: 'Green' },
        ],

        claims: [
            { id: 'c1', active: false, label: 'We have a dedicated CISO or security team', mapLabels: 'Clause 5, A.5', maps: ['isms5', 'a5'] },
            { id: 'c2', active: false, label: 'Data is encrypted at rest', mapLabels: 'A.8', maps: ['a8'] },
            { id: 'c3', active: false, label: 'Data is encrypted in transit', mapLabels: 'A.8', maps: ['a8'] },
            { id: 'c4', active: false, label: 'We conduct regular penetration tests', mapLabels: 'Clause 9, A.8', maps: ['isms9', 'a8'] },
            { id: 'c5', active: false, label: 'We have an incident response plan', mapLabels: 'Clause 8, A.5', maps: ['isms8', 'a5'] },
            { id: 'c6', active: false, label: 'All staff complete security awareness training', mapLabels: 'Clause 7, A.6', maps: ['isms7', 'a6'] },
            { id: 'c7', active: false, label: 'We perform background checks on staff', mapLabels: 'A.6', maps: ['a6'] },
            { id: 'c8', active: false, label: 'MFA is enforced for all users', mapLabels: 'A.8', maps: ['a8'] },
            { id: 'c9', active: false, label: 'We perform regular access reviews', mapLabels: 'A.5, A.8', maps: ['a5', 'a8'] },
            { id: 'c10', active: false, label: 'We have a business continuity plan', mapLabels: 'A.5', maps: ['a5'] },
            { id: 'c11', active: false, label: 'We assess vendor/supplier security', mapLabels: 'A.5', maps: ['a5'] },
            { id: 'c12', active: false, label: 'We have a data classification policy', mapLabels: 'A.5, A.8', maps: ['a5', 'a8'] },
        ],

        controls: [
            { id: 'i4a', section: 'isms4', status: 'red', label: 'ISMS scope defined and documented', hint: 'Boundaries, applicability, interfaces' },
            { id: 'i4b', section: 'isms4', status: 'red', label: 'Interested parties identified', hint: 'Customers, regulators, partners, staff' },
            { id: 'i4c', section: 'isms4', status: 'red', label: 'Internal and external issues documented', hint: 'PESTLE/SWOT or equivalent analysis' },

            { id: 'i5a', section: 'isms5', status: 'red', label: 'Top management commitment demonstrated', hint: 'Board-level sponsorship, resource allocation' },
            { id: 'i5b', section: 'isms5', status: 'red', label: 'Information security policy approved and communicated', hint: '' },
            { id: 'i5c', section: 'isms5', status: 'red', label: 'Roles, responsibilities, and authorities assigned', hint: 'CISO, risk owners, asset owners' },

            { id: 'i6a', section: 'isms6', status: 'red', label: 'Risk assessment process established', hint: 'Methodology, criteria, risk register' },
            { id: 'i6b', section: 'isms6', status: 'red', label: 'Risk treatment plan documented', hint: 'Controls selected, residual risk accepted' },
            { id: 'i6c', section: 'isms6', status: 'red', label: 'Information security objectives set', hint: 'Measurable, monitored, communicated' },

            { id: 'i7a', section: 'isms7', status: 'red', label: 'Resources allocated to ISMS', hint: 'Budget, people, tools' },
            { id: 'i7b', section: 'isms7', status: 'red', label: 'Staff competence assessed and recorded', hint: 'Training records, skill matrix' },
            { id: 'i7c', section: 'isms7', status: 'red', label: 'Security awareness programme in place', hint: 'Regular training, phishing simulations' },
            { id: 'i7d', section: 'isms7', status: 'red', label: 'Documented information procedures', hint: 'Document control, versioning, retention' },

            { id: 'i8a', section: 'isms8', status: 'red', label: 'Risk treatment plan implemented', hint: 'Controls operational, evidence collected' },
            { id: 'i8b', section: 'isms8', status: 'red', label: 'Operational planning and control', hint: 'Change management, outsourced processes' },

            { id: 'i9a', section: 'isms9', status: 'red', label: 'Monitoring and measurement in place', hint: 'KPIs, metrics, dashboards' },
            { id: 'i9b', section: 'isms9', status: 'red', label: 'Internal audit conducted', hint: 'Independent auditor, documented findings' },
            { id: 'i9c', section: 'isms9', status: 'red', label: 'Management review completed', hint: 'At least annually, minutes recorded' },

            { id: 'i10a', section: 'isms10', status: 'red', label: 'Nonconformity and corrective action process', hint: 'Root cause analysis, CAPA log' },
            { id: 'i10b', section: 'isms10', status: 'red', label: 'Continual improvement demonstrated', hint: 'Trend analysis, lessons learned' },

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

            { id: 'a6a', section: 'a6', status: 'red', label: 'Pre-employment screening', hint: 'Background checks, reference verification' },
            { id: 'a6b', section: 'a6', status: 'red', label: 'Employment terms and conditions', hint: 'Security clauses in contracts, NDAs' },
            { id: 'a6c', section: 'a6', status: 'red', label: 'Security awareness and training', hint: 'Onboarding + ongoing, role-based' },
            { id: 'a6d', section: 'a6', status: 'red', label: 'Disciplinary process', hint: 'For security policy violations' },
            { id: 'a6e', section: 'a6', status: 'red', label: 'Responsibilities after termination', hint: 'Access revocation, asset return' },

            { id: 'a7a', section: 'a7', status: 'red', label: 'Physical security perimeters', hint: 'Office, data centre, server room access' },
            { id: 'a7b', section: 'a7', status: 'red', label: 'Physical entry controls', hint: 'Badge access, visitor logs, CCTV' },
            { id: 'a7c', section: 'a7', status: 'red', label: 'Securing offices and facilities', hint: 'Lock policy, alarm systems' },
            { id: 'a7d', section: 'a7', status: 'red', label: 'Clear desk and clear screen', hint: 'Policy enforced, spot-checked' },
            { id: 'a7e', section: 'a7', status: 'red', label: 'Equipment maintenance and disposal', hint: 'Secure wiping, certified destruction' },

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
            { id: 'isms4', name: 'Clause 4 — Context of the Organisation', shortName: 'Cl.4 Context', open: false },
            { id: 'isms5', name: 'Clause 5 — Leadership', shortName: 'Cl.5 Leadership', open: false },
            { id: 'isms6', name: 'Clause 6 — Planning', shortName: 'Cl.6 Planning', open: false },
            { id: 'isms7', name: 'Clause 7 — Support', shortName: 'Cl.7 Support', open: false },
            { id: 'isms8', name: 'Clause 8 — Operation', shortName: 'Cl.8 Operation', open: false },
            { id: 'isms9', name: 'Clause 9 — Performance Evaluation', shortName: 'Cl.9 Evaluation', open: false },
            { id: 'isms10', name: 'Clause 10 — Improvement', shortName: 'Cl.10 Improvement', open: false },
        ],

        annexASections: [
            { id: 'a5', name: 'A.5 — Organisational Controls', shortName: 'A.5 Org', open: false },
            { id: 'a6', name: 'A.6 — People Controls', shortName: 'A.6 People', open: false },
            { id: 'a7', name: 'A.7 — Physical Controls', shortName: 'A.7 Physical', open: false },
            { id: 'a8', name: 'A.8 — Technological Controls', shortName: 'A.8 Tech', open: false },
        ],

        init() {
            this.loadSavedState();
        },

        get allSections() {
            return [...this.ismsSections, ...this.annexASections];
        },

        get totalControls() {
            return this.controls.length;
        },

        get greenCount() {
            return this.controls.filter((control) => control.status === 'green').length;
        },

        get amberCount() {
            return this.controls.filter((control) => control.status === 'amber').length;
        },

        get redCount() {
            return this.controls.filter((control) => control.status === 'red').length;
        },

        get readyPercent() {
            return this.totalControls ? Math.round((this.greenCount / this.totalControls) * 100) : 0;
        },

        get filteredControls() {
            return this.controls.filter((control) => this.matchesControlFilter(control));
        },

        get filteredRedControls() {
            return this.filteredControls
                .filter((control) => control.status === 'red')
                .map((control) => ({ ...control, sectionName: this.sectionLabel(control.section) }));
        },

        get filteredAmberControls() {
            return this.filteredControls
                .filter((control) => control.status === 'amber')
                .map((control) => ({ ...control, sectionName: this.sectionLabel(control.section) }));
        },

        get redControls() {
            return this.controls
                .filter((control) => control.status === 'red')
                .map((control) => ({ ...control, sectionName: this.sectionLabel(control.section) }));
        },

        get amberControls() {
            return this.controls
                .filter((control) => control.status === 'amber')
                .map((control) => ({ ...control, sectionName: this.sectionLabel(control.section) }));
        },

        get hasActiveFilters() {
            return this.controlQuery.trim() !== '' || this.statusFilter !== 'all';
        },

        get progressHeadline() {
            if (this.readyPercent === 100) return 'All controls are green';
            if (this.readyPercent >= 80) return 'You are close to audit-ready';
            if (this.readyPercent >= 40) return 'The programme is moving';
            return 'You are still in gap-closing mode';
        },

        get progressMessage() {
            const bottleneck = this.highestPrioritySection();

            if (this.redCount === 0 && this.amberCount === 0) {
                return 'Everything in the self-assessment is complete. Export the summary and prepare supporting evidence for external review.';
            }

            if (this.redCount > 0 && bottleneck) {
                return `${bottleneck.name} currently has the biggest concentration of gaps with ${bottleneck.red} red and ${bottleneck.amber} amber controls.`;
            }

            return `${this.amberCount} controls are still marked in progress. Push those through to green to improve the overall picture quickly.`;
        },

        controlsForSection(sectionId) {
            return this.controls.filter((control) => control.section === sectionId);
        },

        filteredControlsForSection(sectionId) {
            return this.controlsForSection(sectionId).filter((control) => this.matchesControlFilter(control));
        },

        matchesControlFilter(control) {
            const query = this.controlQuery.trim().toLowerCase();
            const searchableText = [
                control.label,
                control.hint,
                this.sectionLabel(control.section),
            ].join(' ').toLowerCase();

            if (query && !searchableText.includes(query)) {
                return false;
            }

            if (this.statusFilter === 'all') return true;
            if (this.statusFilter === 'gaps') return control.status !== 'green';

            return control.status === this.statusFilter;
        },

        sectionCounts(sectionId) {
            const controls = this.controlsForSection(sectionId);

            return {
                total: controls.length,
                green: controls.filter((control) => control.status === 'green').length,
                amber: controls.filter((control) => control.status === 'amber').length,
                red: controls.filter((control) => control.status === 'red').length,
            };
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
            const counts = this.sectionCounts(sectionId);

            return `${counts.green}/${counts.total} green · ${counts.amber} amber · ${counts.red} red`;
        },

        filteredSectionText(sectionId) {
            const visible = this.filteredControlsForSection(sectionId).length;
            const total = this.controlsForSection(sectionId).length;

            if (!this.hasActiveFilters) {
                return `${total} control${total === 1 ? '' : 's'} in this section`;
            }

            return `${visible} of ${total} control${total === 1 ? '' : 's'} match the current filters`;
        },

        highestPrioritySection() {
            const ranked = this.allSections
                .map((section) => {
                    const counts = this.sectionCounts(section.id);

                    return {
                        id: section.id,
                        name: section.shortName,
                        red: counts.red,
                        amber: counts.amber,
                        score: counts.red * 3 + counts.amber,
                    };
                })
                .filter((section) => section.score > 0)
                .sort((a, b) => b.score - a.score);

            return ranked[0] || null;
        },

        statusLabel(status) {
            if (status === 'green') return 'Implemented';
            if (status === 'amber') return 'In progress';
            return 'Not started';
        },

        statusDotClass(status) {
            if (status === 'green') return 'bg-green-500';
            if (status === 'amber') return 'bg-amber-400';
            return 'bg-red-500';
        },

        statusBadgeClass(status) {
            if (status === 'green') return 'bg-green-50 text-green-700 border border-green-100';
            if (status === 'amber') return 'bg-amber-50 text-amber-700 border border-amber-100';
            return 'bg-red-50 text-red-700 border border-red-100';
        },

        statusButtonClass(currentStatus, buttonStatus) {
            const baseMap = {
                red: currentStatus === 'red'
                    ? 'border-red-500 bg-red-500 text-white shadow-sm focus:ring-red-500'
                    : 'border-red-200 bg-white text-red-700 hover:bg-red-50 focus:ring-red-500',
                amber: currentStatus === 'amber'
                    ? 'border-amber-400 bg-amber-400 text-white shadow-sm focus:ring-amber-400'
                    : 'border-amber-200 bg-white text-amber-700 hover:bg-amber-50 focus:ring-amber-400',
                green: currentStatus === 'green'
                    ? 'border-green-500 bg-green-500 text-white shadow-sm focus:ring-green-500'
                    : 'border-green-200 bg-white text-green-700 hover:bg-green-50 focus:ring-green-500',
            };

            return baseMap[buttonStatus];
        },

        sectionLabel(sectionId) {
            const section = this.allSections.find((item) => item.id === sectionId);
            return section ? section.shortName : sectionId;
        },

        filterCount(filterId) {
            if (filterId === 'all') return this.controls.length;
            if (filterId === 'gaps') return this.controls.filter((control) => control.status !== 'green').length;
            return this.controls.filter((control) => control.status === filterId).length;
        },

        clearFilters() {
            this.controlQuery = '';
            this.statusFilter = 'all';
        },

        setControlStatus(control, status) {
            control.status = status;
            this.persistData();
        },

        setSectionStatus(sectionId, status) {
            this.controls
                .filter((control) => control.section === sectionId)
                .forEach((control) => {
                    control.status = status;
                });
            this.persistData();
        },

        setAllSectionsOpen(sections, open) {
            sections.forEach((section) => {
                section.open = open;
            });
        },

        focusSection(sectionId) {
            const ismsSection = this.ismsSections.find((section) => section.id === sectionId);
            const annexSection = this.annexASections.find((section) => section.id === sectionId);

            this.clearFilters();

            if (ismsSection) {
                this.activeTab = 'isms';
                this.ismsSections.forEach((section) => {
                    section.open = section.id === sectionId;
                });
            }

            if (annexSection) {
                this.activeTab = 'annexa';
                this.annexASections.forEach((section) => {
                    section.open = section.id === sectionId;
                });
            }

            this.$nextTick(() => {
                const target = document.getElementById(`section-${sectionId}`);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        },

        get claimGaps() {
            const gaps = [];

            for (const claim of this.claims) {
                if (!claim.active) continue;

                const mappedControls = this.controls.filter((control) => claim.maps.includes(control.section));
                const nonGreenControls = mappedControls.filter((control) => control.status !== 'green');

                if (nonGreenControls.length === 0) continue;

                const red = nonGreenControls.filter((control) => control.status === 'red').length;
                const amber = nonGreenControls.filter((control) => control.status === 'amber').length;
                const parts = [];

                if (red) parts.push(`${red} red`);
                if (amber) parts.push(`${amber} amber`);

                gaps.push({
                    claim: claim.label,
                    reason: `${parts.join(', ')} mapped control${nonGreenControls.length === 1 ? '' : 's'} still need work`,
                    sections: claim.maps,
                });
            }

            return gaps;
        },

        payload() {
            return {
                claims: this.claims.map((claim) => ({ id: claim.id, active: claim.active })),
                controls: this.controls.map((control) => ({ id: control.id, status: control.status })),
                lastSavedAt: new Date().toISOString(),
            };
        },

        persistData() {
            const payload = this.payload();

            try {
                localStorage.setItem(this.storageKey, JSON.stringify(payload));
                this.lastSavedAt = payload.lastSavedAt;
                this.copyError = '';
            } catch (error) {
                this.copyError = 'Saving in the browser was blocked.';
            }
        },

        loadSavedState() {
            try {
                const saved = localStorage.getItem(this.storageKey);
                if (!saved) return;

                const parsed = JSON.parse(saved);

                if (Array.isArray(parsed.claims)) {
                    for (const savedClaim of parsed.claims) {
                        const claim = this.claims.find((item) => item.id === savedClaim.id);
                        if (claim) claim.active = Boolean(savedClaim.active);
                    }
                }

                if (Array.isArray(parsed.controls)) {
                    for (const savedControl of parsed.controls) {
                        const control = this.controls.find((item) => item.id === savedControl.id);
                        if (control && ['red', 'amber', 'green'].includes(savedControl.status)) {
                            control.status = savedControl.status;
                        }
                    }
                }

                this.lastSavedAt = parsed.lastSavedAt || '';
            } catch (error) {
                this.lastSavedAt = '';
            }
        },

        resetAssessment() {
            if (!window.confirm('Reset the assessment and clear the saved browser state?')) {
                return;
            }

            this.claims.forEach((claim) => {
                claim.active = false;
            });

            this.controls.forEach((control) => {
                control.status = 'red';
            });

            this.controlQuery = '';
            this.statusFilter = 'all';
            this.copyError = '';
            this.copied = false;
            this.lastSavedAt = '';
            this.allSections.forEach((section) => {
                section.open = false;
            });

            try {
                localStorage.removeItem(this.storageKey);
            } catch (error) {
                this.copyError = 'Could not clear the saved browser state.';
            }
        },

        formatTimestamp(value) {
            if (!value) return '';

            try {
                return new Date(value).toLocaleString(undefined, {
                    dateStyle: 'medium',
                    timeStyle: 'short',
                });
            } catch (error) {
                return value;
            }
        },

        summaryMarkdown() {
            const lines = [
                '# ISO 27001 Readiness Summary',
                '',
                `**Overall:** ${this.readyPercent}% ready (${this.greenCount}/${this.totalControls} controls green)`,
                '',
                '## Per section',
                '',
            ];

            for (const section of this.allSections) {
                const counts = this.sectionCounts(section.id);
                lines.push(`- **${section.shortName}:** ${counts.green} green, ${counts.amber} amber, ${counts.red} red`);
            }

            if (this.claimGaps.length) {
                lines.push('', '## Claim gaps', '');
                for (const gap of this.claimGaps) {
                    lines.push(`- **${gap.claim}** — ${gap.reason}`);
                }
            }

            if (this.redControls.length) {
                lines.push('', '## Red priorities', '');
                for (const control of this.redControls) {
                    lines.push(`- [ ] ${control.label} (${control.sectionName})`);
                }
            }

            if (this.amberControls.length) {
                lines.push('', '## Amber priorities', '');
                for (const control of this.amberControls) {
                    lines.push(`- [ ] ${control.label} (${control.sectionName})`);
                }
            }

            lines.push('', '---', '*Generated by urlcv.com/tools/iso27001-readiness*');

            return lines.join('\n');
        },

        async writeToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
                return;
            }

            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'absolute';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.select();

            const copied = document.execCommand('copy');
            document.body.removeChild(textarea);

            if (!copied) {
                throw new Error('Clipboard unavailable');
            }
        },

        async copySummary() {
            this.copyError = '';

            try {
                await this.writeToClipboard(this.summaryMarkdown());
                this.copied = true;
                setTimeout(() => {
                    this.copied = false;
                }, 2000);
            } catch (error) {
                this.copyError = 'Clipboard access was blocked. You can still use the browser copy command after selecting the page content.';
            }
        },
    };
}
</script>
@endpush

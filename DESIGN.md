# Design Direction & Specification (DESIGN.md)

**Project:** Sistem Informasi Akademik & Keuangan Yayasan (SIAKAD DIGITAL)  
**Audience:** Guru, Staf Tata Usaha, Petugas Keuangan, Kepala Sekolah, dan Koordinator Yayasan  
**Dial:** `ENERGY 1 / RHYTHM 2 / MOTION 1`  
**Design Read:** Enterprise Academic & Financial Management System (SIAKAD) for Islamic Foundation Schools (SMP/SMA/Pondok Pesantren), serving teachers, administrative staff, finance officers, and principals with a calm, high-contrast, distraction-free visual language.

---

## 1. Liveliness Dials

| Dial | Level | Implementation Strategy |
| :--- | :---: | :--- |
| **ENERGY** | **1 (Calm)** | Distraction-free, restrained, high-legibility interface built for daily repetitive clerical and pedagogical tasks. Zero flashy gradients, zero glowing borders, and zero neon accents. |
| **RHYTHM** | **2 (Consistent with Purposeful Breaks)** | Standardized container grids and data tables with distinct visual breaks for summary banners, stat cards, and interactive modal drawers. |
| **MOTION** | **1 (Subtle & Guided)** | Restrained micro-interactions: standard button active states, clean dropdown fade-ins, and guided step-by-step spotlight tours (Driver.js) for initial user onboarding. |

---

## 2. Brand Identity & Palette Tokens

The visual identity is rooted in an Islamic foundation ethos: clean, disciplined, trustworthy, and dignified.

### Core Neutral Foundation
- **Canvas / Background:** `#fafaf9` (`stone-50`)
- **Card Surfaces:** `#ffffff` (`white`)
- **Borders & Dividers:** `#e7e5e4` (`stone-200`)
- **Primary Headings & Body:** `#1c1917` (`stone-900`)
- **Secondary Body Text:** `#44403c` (`stone-700`)
- **Muted Hints & Captions:** `#57534e` (`stone-600`) — minimum 4.5:1 WCAG AA contrast against white

### Brand Primary Accent (Islamic Emerald)
- **Primary Action (Default):** `#047857` (`emerald-700`)
- **Primary Hover:** `#065f46` (`emerald-800`)
- **Primary Light Tint (Icon / Chip):** `#ecfdf5` (`emerald-50`) / `#065f46` (`emerald-800` text)

### Functional Semantic Accents (Used Only for Operational State)
- **Warning / Pending State:** Amber (`#b45309` / `bg-amber-50`)
- **Danger / Arrears / Void:** Rose (`#be123c` / `bg-rose-50`)
- **Informative / Secondary:** Sky / Stone (`#0369a1` / `bg-sky-50`)

---

## 3. Typography & Spacing Hierarchy

- **Font Family:** System font stack (`Instrument Sans`, `ui-sans-serif`, `system-ui`, `-apple-system`, `BlinkMacSystemFont`, `sans-serif`).
- **Heading 1:** `text-2xl font-extrabold text-stone-900 tracking-tight`
- **Heading 2 / Card Title:** `text-base font-bold text-stone-900 tracking-tight`
- **Body Text:** `text-sm text-stone-700 leading-relaxed`
- **Captions & Table Cells:** `text-xs text-stone-600 font-medium`
- **No Em Dash Policy:** Never use the em dash (`—`) in UI copy or labels. Use colons (`:`), pipes (`|`), commas (`,`), or parentheses `()` instead.

---

## 4. Component Structure & Elevation Standards

### Border Radius Discipline
- **Interactive Controls (Buttons, Inputs, Selects, Badges):** `rounded-xl` (12px)
- **Primary Cards & Data Table Containers:** `rounded-2xl` (16px)
- **Modal Dialogs & Floating Drawers:** `rounded-2xl` (16px)
- **Prohibited:** Arbitrary `rounded-3xl` on small widget containers.

### Shadows & Elevation
- **Standard Surface:** `shadow-xs` (`border border-stone-200`)
- **Hover Lift (Interactive Cards):** `hover:shadow-sm hover:border-stone-300`
- **Modal & Drawers:** `shadow-xl` with backdrop overlay (`bg-stone-950/60 backdrop-blur-xs`)

### Mobile Touch Targets
- All buttons, interactive table actions, and form inputs must meet the minimum 44px touch target requirement on mobile devices (`min-h-[44px] sm:min-h-0`).

---

## 5. Craftsmanship & Anti-Slop Checkpoints

1. **C-1 Intentionality:** Every visual component and copy string serves an articulated academic workflow purpose.
2. **C-2 Functional Completeness:** No dead anchors (`href="#"`). All actions connect to real Livewire handlers or named routes.
3. **C-3 Content-Driven Composition:** Layout structures fit the curriculum workflows (Kurikulum Merdeka, Capaian Pembelajaran, Presensi, Penilaian Sumatif/Formatif).
4. **C-4 Resilience:** Full coverage of empty states (`<x-table.empty>`), loading indicators (`wire:loading`), and validation alerts.
5. **C-5 Evidence Over Claims:** Dashboards present authentic database calculations; zero fictional metrics or promotional fluff.

# Mobile-First Frontend Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refactor the entire frontend of Sistem Klinik into a strict Mobile-First responsive application adhering strictly to `DESIGN.md` and Anti-Slop UI standards.

**Architecture:** Adopt a mobile-first responsive architecture where base styles target narrow viewports (360px+) with 44px+ touch targets, single-column stacks, isolated horizontal table scrolling, and off-canvas drawer navigation, progressing to an icon-rail layout for tablets (768px-1023px) and a full 240px sidebar for desktop (1024px+). Standardize all screens onto three strict layout templates (Template A for Lists/Dashboards, Template B for Forms, Template C for Detail/Clinical Examination) powered by design tokens, matte surfaces, and zero-slop clinical safety components.

**Tech Stack:** Laravel 12 (Blade templates, View components), Tailwind CSS v4 (`@theme` tokens, `@media (pointer: coarse)` utilities), Alpine.js (reactive drawers, tabs, modals), Flatpickr (Teal themed), DataTables (mobile-contained responsive wrapper), Pest PHP (feature & view contract tests).

**Spec:** `DESIGN.md`

## Global Constraints

- **Color Palette (R-01, R-29):** Core Neutrals: `--color-bg: #F1F5F9`, `--color-surface: #FFFFFF`, `--color-text: #0F172A` (Slate-900, pure black `#000000` is strictly banned), `--color-text-muted: #475569` (Slate-600), `--color-text-subtle: #64748B`, `--color-border: #CBD5E1`, `--color-border-subtle: #E2E8F0`.
- **Single Accent Teal (R-01):** `--primary-50: #F0FDFA`, `--primary-100: #CCFBF1`, `--primary-600: #0D9488` (focus ring only), `--primary-700: #0F766E` (primary buttons, active tabs/menu), `--primary-800: #115E59` (hover), `--primary-900: #134E4A` (active/pressed). No AI purple/blue gradients.
- **Semantic Status Tokens (R-31):** Success (`#15803D` / `#F0FDF4` / `#BBF7D0`), Warning (`#B45309` / `#FFFBEB` / `#FDE68A`), Danger (`#B91C1C` / `#FEF2F2` / `#FECACA`), Info (`#1D4ED8` / `#EFF6FF` / `#BFDBFE`), Neutral (`#334155` / `#F8FAFC` / `#CBD5E1`). Always accompany color with text label (never convey status with color alone).
- **Elevation & Radii (R-10, R-11, R-12, R-13):** Flat matte containers with 1px border. Zero glassmorphism (`backdrop-filter: blur` banned), zero glow/ambient shadows. Radii: Small 6px (`rounded`), Medium 8px (`rounded-md`), Large 12px (`rounded-lg`), Pill (`rounded-full` ONLY for status pills and allergy markers, never for buttons/inputs).
- **Spacing Scale (Bagian 6.2):** Strictly 4px scale: `0.5` (2px), `1` (4px), `2` (8px), `3` (12px), `4` (16px), `6` (24px), `8` (32px), `9` (36px), `11` (44px), `12` (48px), `14` (56px), `24` (96px), `60` (240px). Arbitrary values (`mt-[13px]`, `p-[18px]`, `h-10`, `p-5`) strictly banned.
- **Controls & Tap Targets (Bagian 6.3, R-03, antislop-layoutmobile):** Desktop default `h-9 px-3/4` (36px). Touch/mobile targets (`@media (pointer: coarse)` and viewport < 768px) MUST be at least 44px (`h-11 px-4`). Inter-button/action spacing minimum 8px (`gap-2`).
- **Typography & Formats (Bagian 5, R-06):** Sans (`Inter`), Mono (`JetBrains Mono` for No. RM, No. Antrean, NIK, Rupiah, Dosis, Stok, timestamps). Tabular numbers. Minimum `text-sm font-semibold` (14px) for critical clinical data (allergies, dosages).
- **Anti-Slop Prohibitions (Bagian 16):** Zero emojis in UI (R-04). Zero decorative arrows on buttons (R-08). Zero pulsing dots or looping animations (R-19). Zero floating labels (R-10). Zero fake delta percentages on metric cards (R-17, R-38). Zero toast autohide for errors or clinical warnings (R-27). Field labels MUST have `(wajib)` in text, not bare asterisks.
- **Patient Safety (Bagian 9):** Sticky Patient Header on clinical screens with 2 identifiers (Name + No. RM). 3-state Allergy Banner (Ada alergi tercatat [Danger] / Dipastikan tidak ada [Neutral] / Belum pernah ditanyakan [Warning]). Inline prescription allergy warning blocking save without recorded clinical reason. Confirmation dialog naming object for risky actions.

## Review Focus

1. **Horizontal Scroll Leak on Mobile Viewports (< 768px):** Wide DataTables or unbroken identifiers causing the `<body>` to scroll horizontally. Verified by wrapping all tables in self-contained `overflow-x-auto` wrappers and asserting `overflow-x-hidden` on main canvas.
2. **Touch Target Sizing & Tap Collisions on Mobile:** Table action links (`Lihat`, `Edit`, `Hapus`), tab items, and pagination buttons falling below 44px tap target height on mobile screens. Verified by mobile touch classes (`min-h-11`, `h-11` or padded click regions with `gap-2`).
3. **Fixed Chrome Viewport Overlap on Small Screens:** Sticky Patient Header (top) and Sticky Action Footer (bottom) eating too much visible screen space on mobile. Verified by compact mobile headers (`py-2.5 px-4`) and mandatory bottom container padding (`pb-28`) preventing inputs from hiding under the footer.
4. **Clinical Safety Warning Visibility Collapse:** Allergy warnings or interaction alerts being hidden off-screen or rendered below the fold. Verified by persistent inline danger banners placed directly at the decision point before submission.
5. **Form Input Usability on Touch Devices:** Form fields crammed into multi-column grids on narrow screens. Verified by mobile-first single column `grid-cols-1 md:grid-cols-2` with `(wajib)` labels and visible error states below inputs.

---

### Task 1: CSS Architecture, Tailwind Tokens, Spacing Scale, & Mobile Touch Utilities

**Files:**
- Modify: `resources/css/app.css`
- Modify: `tailwind.config.js`
- Create: `tests/Feature/Frontend/DesignTokenContractTest.php`

**Interfaces:**
- Consumes: `DESIGN.md` sections 3, 4, 5, 6
- Produces: CSS custom properties (`--color-bg`, `--color-surface`, `--color-text`, `--color-text-muted`, `--color-text-subtle`, `--color-border`, `--color-border-subtle`, `--primary-*`, `--success-*`, `--warning-*`, `--danger-*`, `--info-*`, `--neutral-*`), strictly constrained spacing scale, mobile touch tap target classes (`@media (pointer: coarse)` -> `h-11`).

- [ ] **Step 1: Write the failing contract test**

```php
// tests/Feature/Frontend/DesignTokenContractTest.php
test('css contains strict tokens and zero banned arbitrary values', function () {
    $css = file_get_contents(resource_path('css/app.css'));

    // Check Core Neutrals
    expect($css)->toContain('--color-bg: #F1F5F9')
        ->toContain('--color-surface: #FFFFFF')
        ->toContain('--color-text: #0F172A')
        ->toContain('--color-text-muted: #475569')
        ->toContain('--color-text-subtle: #64748B')
        ->toContain('--color-border: #CBD5E1')
        ->toContain('--color-border-subtle: #E2E8F0');

    // Check Single Accent Teal
    expect($css)->toContain('--color-primary-700: #0F766E')
        ->toContain('--color-primary-800: #115E59')
        ->toContain('--color-primary-900: #134E4A')
        ->toContain('--color-primary-600: #0D9488');

    // Check Semantic Status Tokens
    expect($css)->toContain('--color-success: #15803D')
        ->toContain('--color-warning: #B45309')
        ->toContain('--color-danger: #B91C1C')
        ->toContain('--color-info: #1D4ED8')
        ->toContain('--color-neutral: #334155');

    // Check Touch Target media query
    expect($css)->toContain('@media (pointer: coarse)');

    // Ensure pure black #000000 is not present
    expect($css)->not->toContain('#000000');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Frontend/DesignTokenContractTest.php`
Expected: FAIL due to missing file or tokens.

- [ ] **Step 3: Implement tokens and utilities in `resources/css/app.css` and `tailwind.config.js`**

Update `resources/css/app.css`:
- Add full CSS variables for Neutral, Primary 800/900, Status backgrounds and borders (`--success-bg: #F0FDF4`, `--success-bd: #BBF7D0`, etc.).
- Add `@media (pointer: coarse)` rule ensuring form controls and buttons expand to `h-11` (44px) on touch devices.
- Style Flatpickr using Medical Teal `#0F766E` header and selected day.
- Set global typography: `font-family: var(--font-sans)`, `font-variant-ligatures: common-ligatures`.
- Set global focus ring: `*:focus-visible { outline: 2px solid var(--color-primary-600); outline-offset: 2px; }`.

Update `tailwind.config.js`:
- Configure fonts (`Inter`, `"JetBrains Mono"`).
- Extend colors mapping to CSS variables.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/Frontend/DesignTokenContractTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add resources/css/app.css tailwind.config.js tests/Feature/Frontend/DesignTokenContractTest.php
git commit -m "feat(ui): add mobile-first design tokens and touch utilities per DESIGN.md"
```

---

### Task 2: Core Form & Interactive Components Refactor

**Files:**
- Modify: `resources/views/components/primary-button.blade.php`
- Modify: `resources/views/components/secondary-button.blade.php`
- Modify: `resources/views/components/danger-button.blade.php`
- Modify: `resources/views/components/text-input.blade.php`
- Modify: `resources/views/components/select-input.blade.php`
- Modify: `resources/views/components/input-label.blade.php`
- Modify: `resources/views/components/input-error.blade.php`
- Modify: `resources/views/components/modal.blade.php`
- Create: `resources/views/components/status-pill.blade.php`
- Create: `resources/views/components/confirm-dialog.blade.php`
- Create: `tests/Feature/Frontend/CoreComponentTest.php`

**Interfaces:**
- Consumes: CSS tokens from Task 1, `DESIGN.md` Bagian 4, 6.3, 12, 16
- Produces: Accessible Blade components with `h-9` desktop / `h-11` touch heights, no outer margins, static labels with `(wajib)` support, no decorative arrows or emojis, accessible dialog without blur.

- [ ] **Step 1: Write the failing test**

```php
// tests/Feature/Frontend/CoreComponentTest.php
test('core components render with correct classes and without banned patterns', function () {
    $primaryBtn = (string) $this->blade('<x-primary-button>Simpan Data</x-primary-button>');
    expect($primaryBtn)->toContain('bg-primary-700')
        ->toContain('hover:bg-primary-800')
        ->toContain('active:bg-primary-900')
        ->toContain('h-9')
        ->not->toContain('->')
        ->not->toContain('&rarr;');

    $secondaryBtn = (string) $this->blade('<x-secondary-button>Batal</x-secondary-button>');
    expect($secondaryBtn)->toContain('border-primary-700')
        ->toContain('text-primary-700')
        ->toContain('hover:bg-primary-50');

    $label = (string) $this->blade('<x-input-label value="Nama Pasien" :required="true" />');
    expect($label)->toContain('(wajib)')
        ->toContain('text-text-muted')
        ->not->toContain('*');

    $pill = (string) $this->blade('<x-status-pill status="success" label="Selesai" />');
    expect($pill)->toContain('rounded-full')
        ->toContain('Selesai')
        ->not->toContain('animate-pulse');

    $modal = (string) $this->blade('<x-modal name="test-modal">Modal Content</x-modal>');
    expect($modal)->not->toContain('backdrop-blur')
        ->toContain('bg-slate-900/50');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Frontend/CoreComponentTest.php`
Expected: FAIL due to missing props or classes.

- [ ] **Step 3: Implement components**

- `primary-button.blade.php`: `h-9 px-4 gap-2 rounded-md font-semibold text-sm bg-primary-700 hover:bg-primary-800 active:bg-primary-900 text-white focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 transition-colors duration-150 inline-flex items-center justify-center pointer-coarse:h-11`.
- `secondary-button.blade.php`: `h-9 px-4 gap-2 rounded-md font-medium text-sm bg-surface border border-primary-700 text-primary-700 hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 transition-colors duration-150 inline-flex items-center justify-center pointer-coarse:h-11`.
- `danger-button.blade.php`: `h-9 px-4 gap-2 rounded-md font-semibold text-sm bg-danger hover:bg-red-800 active:bg-red-900 text-white focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 transition-colors duration-150 inline-flex items-center justify-center pointer-coarse:h-11`.
- `text-input.blade.php`: `h-9 px-3 rounded-md border border-border bg-surface text-text text-sm placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none transition-colors duration-150 pointer-coarse:h-11 disabled:bg-bg disabled:text-text-subtle disabled:cursor-not-allowed`.
- `select-input.blade.php`: `h-9 px-3 rounded-md border border-border bg-surface text-text text-sm focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none transition-colors duration-150 pointer-coarse:h-11 disabled:bg-bg disabled:text-text-subtle disabled:cursor-not-allowed`.
- `input-label.blade.php`: `@props(['value', 'required' => false])`. Renders `<label class="block text-xs font-medium text-text-muted mb-1">{{ $value ?? $slot }} @if($required)<span class="text-danger font-normal ml-0.5">(wajib)</span>@endif</label>`.
- `input-error.blade.php`: renders `text-xs text-danger mt-1 space-y-0.5`.
- `status-pill.blade.php`: `@props(['status' => 'neutral', 'label' => ''])`. Maps status to tokens:
  - success: `bg-green-50 text-success border-green-200`
  - warning: `bg-amber-50 text-warning border-amber-200`
  - danger: `bg-red-50 text-danger border-red-200`
  - info: `bg-blue-50 text-info border-blue-200`
  - neutral: `bg-slate-50 text-neutral border-border`
  Renders `inline-flex items-center px-2 py-0.5 rounded-full border text-xs font-medium font-sans`.
- `modal.blade.php`: Solid matte backdrop `bg-slate-900/50` without blur. Card `rounded-lg shadow-lg bg-surface border border-border p-6`.
- `confirm-dialog.blade.php`: Specialized modal for risky actions (`Batalkan kunjungan`, `Nonaktifkan pengguna`) requiring object name, primary danger action, and secondary cancel action.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/Frontend/CoreComponentTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add resources/views/components/ tests/Feature/Frontend/CoreComponentTest.php
git commit -m "feat(ui): refactor core interactive components for mobile-first accessibility"
```

---

### Task 3: Mobile-First App Shell, Navigation Drawer, & Layout Templates

**Files:**
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `resources/views/layouts/navigation.blade.php`
- Modify: `resources/views/layouts/guest.blade.php`
- Create: `resources/views/components/app-topbar.blade.php`
- Create: `resources/views/components/app-sidebar.blade.php`
- Create: `tests/Feature/Frontend/AppShellTest.php`

**Interfaces:**
- Consumes: `DESIGN.md` Bagian 6, 7, 10, 13 (`antislop-layoutmobile`)
- Produces: Responsive App Shell with 3 view states:
  1. Mobile (< 768px): Topbar 56px with Hamburger button (44x44px), Off-canvas slide-over drawer, content padding `px-4 py-4 space-y-6`.
  2. Tablet (768px - 1023px): 64px icon rail sidebar with tooltip labels, Topbar 56px, content padding `px-6 py-6`.
  3. Desktop (>= 1024px): 240px solid white sidebar with logo, grouped role navigation, bottom user block, Topbar 56px with global search (`/`), content max-w-1440px `px-6 py-6 space-y-6`.
  Skip link "Lewati ke konten utama" included.

- [ ] **Step 1: Write the failing test**

```php
// tests/Feature/Frontend/AppShellTest.php
use App\Models\User;
use Database\Seeders\KlinikSeeder;

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('app shell renders mobile drawer, skip link, and role navigation', function () {
    $resepsionis = User::where('role', 'resepsionis')->first();

    $response = $this->actingAs($resepsionis)->get(route('dashboard'));
    $response->assertOk();

    // Check Skip link
    $response->assertSee('Lewati ke konten utama');

    // Check Topbar 56px height class and global search
    $response->assertSee('h-14');
    $response->assertSee('Cari nama / No. RM / NIK');

    // Check Role Menu Items for Resepsionis
    $response->assertSee('Dashboard');
    $response->assertSee('Pasien');
    $response->assertSee('Kunjungan');
    $response->assertSee('Tagihan');
    $response->assertSee('Obat');

    // Check mobile drawer markup
    $response->assertSee('x-data="{ sidebarOpen: false');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Frontend/AppShellTest.php`
Expected: FAIL due to missing layout markup.

- [ ] **Step 3: Implement App Shell & Navigation**

- `resources/views/layouts/app.blade.php`:
  - Add Skip link at top: `<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary-700 focus:text-white focus:rounded-md">Lewati ke konten utama</a>`.
  - Root container with Alpine.js state `x-data="{ sidebarOpen: false }"`.
  - Include `<x-app-sidebar />` and `<x-app-topbar />`.
  - `<main id="main-content" class="flex-1 w-full max-w-[1440px] mx-auto px-4 py-4 md:px-6 md:py-6 space-y-6">`.
- `resources/views/components/app-sidebar.blade.php`:
  - Mobile: Off-canvas slide-over overlay with backdrop `bg-slate-900/50`, drawer width `w-60` (240px), close button with min 44px tap target.
  - Tablet (md:): Collapsed rail width `w-16` (64px) with accessible tooltips/aria-labels.
  - Desktop (lg:): Fixed width `w-60` (240px), solid surface, border-r 1px solid border.
  - Logo at top (`h-14 px-4 border-b border-border`).
  - Navigation grouped by role using Outline SVG icons (20px) + text labels. Active state: `bg-primary-50 text-primary-700 border-l-[3px] border-primary-700 font-semibold`.
  - User block pushed to bottom (`mt-auto p-3 border-t border-border`): Name, Role badge, Logout button.
- `resources/views/components/app-topbar.blade.php`:
  - Height `h-14` (56px), solid white, border-b 1px.
  - Mobile: hamburger menu button (`size-11 flex items-center justify-center`), clinic logo / page title.
  - Desktop: Page Title, global search input with `/` shortcut listener, current date-time display.
- `resources/views/layouts/guest.blade.php`:
  - Mobile-first clean canvas: solid `#F1F5F9` background, `#FFFFFF` card, logo, no gradients or slop.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/Frontend/AppShellTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add resources/views/layouts/ resources/views/components/app-* tests/Feature/Frontend/AppShellTest.php
git commit -m "feat(ui): implement mobile-first responsive app shell and drawer navigation"
```

---

### Task 4: Clinical Safety & Stat Card Components

**Files:**
- Create: `resources/views/components/patient-header.blade.php`
- Create: `resources/views/components/allergy-banner.blade.php`
- Create: `resources/views/components/stat-card.blade.php`
- Create: `tests/Feature/Frontend/ClinicalSafetyComponentTest.php`

**Interfaces:**
- Consumes: `DESIGN.md` Bagian 9, 10, 11
- Produces:
  - `<x-patient-header :kunjungan="$kunjungan" />`: Sticky patient header below topbar, displaying 2 patient identifiers (Nama + No. RM mono, usia, JK, penjamin) and integrated 3-state Allergy Banner. Compact on mobile viewports (`px-4 py-2.5`).
  - `<x-allergy-banner :alergi="$pasien->alergi" />`: 3 states:
    1. Danger pill list when allergy is recorded
    2. Neutral box when verified no allergy
    3. Warning alert when allergy has not yet been asked
  - `<x-stat-card label="..." :value="..." status="default|warning|success" />`: Honest metric card with mono number, no fake trend percentages or sparklines.

- [ ] **Step 1: Write the failing test**

```php
// tests/Feature/Frontend/ClinicalSafetyComponentTest.php
test('allergy banner renders 3 states correctly', function () {
    // State 1: Ada alergi
    $view1 = (string) $this->blade('<x-allergy-banner alergi="Penisilin, Sulfa" />');
    expect($view1)->toContain('Penisilin')
        ->toContain('Sulfa')
        ->toContain('bg-red-50')
        ->toContain('text-danger');

    // State 2: Dipastikan tidak ada
    $view2 = (string) $this->blade('<x-allergy-banner :alergi="\'\'" />');
    expect($view2)->toContain('Tidak ada alergi tercatat')
        ->toContain('text-text-muted');

    // State 3: Belum ditanyakan
    $view3 = (string) $this->blade('<x-allergy-banner :alergi="null" />');
    expect($view3)->toContain('Alergi belum ditanyakan')
        ->toContain('text-warning')
        ->toContain('bg-amber-50');
});

test('stat card renders honest metric without fake trend or delta', function () {
    $card = (string) $this->blade('<x-stat-card label="Total Hari Ini" :value="42" />');
    expect($card)->toContain('Total Hari Ini')
        ->toContain('42')
        ->toContain('font-mono')
        ->not->toContain('%')
        ->not->toContain('minggu ini');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Frontend/ClinicalSafetyComponentTest.php`
Expected: FAIL due to missing components.

- [ ] **Step 3: Implement components**

- `resources/views/components/allergy-banner.blade.php`:
  Implement the 3 discrete states from `DESIGN.md:460-468`:
  - `count($allergies) > 0`: Danger alert container with individual allergy pills (`rounded-full bg-surface border border-red-300 text-danger text-xs font-semibold px-2 py-0.5`).
  - `empty($alergi) && $alergi !== null`: Neutral container (`bg-bg border border-border text-text-muted text-xs p-2 rounded-md`).
  - `$alergi === null`: Warning container (`bg-amber-50 border border-amber-200 text-warning text-xs p-2.5 rounded-md font-medium`).
- `resources/views/components/patient-header.blade.php`:
  Sticky header container (`sticky top-14 z-10 bg-surface border border-border shadow-sm rounded-lg p-3 sm:p-4 space-y-2.5`):
  - Row 1: Antrean badge (mono), Patient Full Name (`text-base font-semibold`), No. RM (`font-mono text-sm`), Usia + JK, Penjamin.
  - Row 2: `<x-allergy-banner :alergi="$kunjungan->pasien->alergi" />`.
  - Row 3: Initial complaint if present.
- `resources/views/components/stat-card.blade.php`:
  Container `bg-surface border border-border shadow-sm rounded-lg p-4 space-y-1`.
  - Label: `text-xs font-medium text-text-muted`.
  - Metric: `text-2xl font-semibold font-mono text-text` (or warning/success semantic color if specified).
  - Explicitly no fake trend percentages or arrow badges.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/Frontend/ClinicalSafetyComponentTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add resources/views/components/patient-header.blade.php resources/views/components/allergy-banner.blade.php resources/views/components/stat-card.blade.php tests/Feature/Frontend/ClinicalSafetyComponentTest.php
git commit -m "feat(ui): add patient header, 3-state allergy banner, and honest stat card"
```

---

### Task 5: Mobile-First DataTables & Responsive Table Wrapper

**Files:**
- Modify: `resources/views/components/datatable.blade.php`
- Create: `resources/views/components/table-container.blade.php`
- Create: `tests/Feature/Frontend/DataTableContractTest.php`

**Interfaces:**
- Consumes: `DESIGN.md` Bagian 6.4, 6.7, 11
- Produces: Table wrapper and DataTables initialization that:
  - Isolates table horizontal scroll inside an `overflow-x-auto` wrapper with `-webkit-overflow-scrolling: touch`.
  - Strictly prevents full page body horizontal scrolling on mobile viewports.
  - Formats headers `px-3 py-2 text-xs font-medium text-text-muted` and cells `px-3 py-3 text-sm`.
  - Aligns numbers and currency to the right with `font-mono`.
  - Uses 44px minimum tap targets for pagination buttons on touch devices.

- [ ] **Step 1: Write the failing test**

```php
// tests/Feature/Frontend/DataTableContractTest.php
test('datatable component styles adhere to design system rhythm', function () {
    $datatable = (string) $this->blade('<x-datatable />');

    // Check padding rules from Bagian 6.4 & 11
    expect($datatable)->toContain('padding: 12px 16px;') // cells
        ->toContain('padding: 8px 12px;') // header
        ->toContain('border-bottom: 1px solid #E2E8F0;')
        ->toContain('overflow-x: auto;')
        ->toContain('#0F766E'); // Medical Teal paging highlight
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Frontend/DataTableContractTest.php`
Expected: FAIL.

- [ ] **Step 3: Implement Mobile-First DataTable Component**

Update `resources/views/components/datatable.blade.php`:
- CSS adjustments matching `DESIGN.md` table rhythm:
  - Header: `px-3 py-2 text-xs font-medium text-text-muted bg-bg border-b border-border-subtle`.
  - Rows: `px-3 py-3 text-sm border-t border-border-subtle hover:bg-primary-50`.
  - Layout toolbar: `px-4 py-3 gap-3 border-b border-border`.
  - Pagination: `px-4 py-3 border-t border-border`. Paging buttons styled with `h-9 min-w-9` (and `h-11 min-w-11` on touch) with active page in `bg-primary-700 text-white rounded-md`.
  - Responsive wrapper: `.dt-container { overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch; }`.
- Create `resources/views/components/table-container.blade.php`:
  Wrapper for Template A: `bg-surface border border-border shadow-sm rounded-lg p-0 overflow-hidden`.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/Frontend/DataTableContractTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add resources/views/components/datatable.blade.php resources/views/components/table-container.blade.php tests/Feature/Frontend/DataTableContractTest.php
git commit -m "feat(ui): implement mobile-first responsive datatable wrapper"
```

---

### Task 6: Resepsionis Module Views Refactoring (Template A & B)

**Files:**
- Modify: `resources/views/resepsionis/dashboard.blade.php`
- Modify: `resources/views/resepsionis/pasien/index.blade.php`
- Modify: `resources/views/resepsionis/pasien/form.blade.php`
- Modify: `resources/views/resepsionis/pasien/show.blade.php`
- Modify: `resources/views/resepsionis/kunjungan/index.blade.php`
- Modify: `resources/views/resepsionis/kunjungan/form.blade.php`
- Modify: `resources/views/resepsionis/kunjungan/show.blade.php`
- Create: `tests/Feature/Frontend/ResepsionisViewsTest.php`

**Interfaces:**
- Consumes: Template A (List/Dashboard), Template B (Form), Components from Tasks 1-5
- Produces: Resepsionis views refactored strictly for mobile-first:
  - Dashboard: 4 honest stat cards in `grid grid-cols-2 lg:grid-cols-4 gap-4`, quick actions (primary `Buat Kunjungan`, secondary `Daftar Pasien Baru`), Antrean Hari Ini table.
  - Pasien Index: Search before register banner, responsive table with No. RM (mono) and allergy pills.
  - Pasien Form: Template B with 3 distinct groups (`Data Pribadi`, `Kontak`, `Alergi & Penjamin`), `(wajib)` labels, 3-state allergy input (tidak ada vs belum ditanyakan vs input alergen), `Footer aksi: border-t pt-6 gap-3 justify-end [Batal] [Simpan]`.
  - Kunjungan Form: Search patient, select poli & doctor, visit date, visit complaint.

- [ ] **Step 1: Write the failing view test**

```php
// tests/Feature/Frontend/ResepsionisViewsTest.php
use App\Models\User;
use Database\Seeders\KlinikSeeder;

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('resepsionis dashboard adheres to Template A and mobile-first grid', function () {
    $user = User::where('role', 'resepsionis')->first();
    $response = $this->actingAs($user)->get(route('resepsionis.dashboard'));

    $response->assertOk();
    $response->assertSee('Total Hari Ini');
    $response->assertSee('Pendapatan Hari Ini');
    $response->assertSee('grid grid-cols-2 lg:grid-cols-4 gap-4', false);
    $response->assertSee('Buat Kunjungan');
    $response->assertDontSee('->');
});

test('pasien form adheres to Template B with 3-state allergy selector and explicit wajib labels', function () {
    $user = User::where('role', 'resepsionis')->first();
    $response = $this->actingAs($user)->get(route('pasien.create'));

    $response->assertOk();
    $response->assertSee('(wajib)');
    $response->assertDontSee('Nama Lengkap *');
    $response->assertSee('Data Pribadi');
    $response->assertSee('Alergi & Penjamin');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Frontend/ResepsionisViewsTest.php`
Expected: FAIL.

- [ ] **Step 3: Refactor Resepsionis views**

- `resepsionis/dashboard.blade.php`:
  Implement Template A:
  - Header: H1 `Dashboard Resepsionis` + quick actions `[Daftar Pasien Baru (secondary)] [Buat Kunjungan (primary)]`.
  - Stat cards: `<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">` using `<x-stat-card>`.
  - Table Card: `<x-table-container>`, Antrean table with `px-3 py-3` cells, mono queue numbers, status pills.
- `resepsionis/pasien/form.blade.php`:
  Implement Template B:
  - Header: `Pasien Baru` / `Edit Pasien`.
  - Form card `bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-6 space-y-6`.
  - Group 1: `Data Pribadi` (Nama, NIK mono, Tanggal Lahir with Flatpickr, Jenis Kelamin, Golongan Darah).
  - Group 2: `Kontak & Alamat` (No. HP, Alamat).
  - Group 3: `Alergi & Penjamin` (Penjamin, Alergi radio options: [1. Ada Alergi -> input teks], [2. Dipastikan Tidak Ada], [3. Belum Ditanyakan]).
  - Footer: `border-t border-border pt-6 flex justify-end gap-3 [Batal (secondary)] [Simpan Pasien (primary)]`.
- `resepsionis/pasien/index.blade.php` & `show.blade.php`:
  Template A with responsive columns and patient details with allergy card.
- `resepsionis/kunjungan/form.blade.php`:
  Template B layout with patient selection, poli, doctor, date, complaint.
- `resepsionis/kunjungan/index.blade.php` & `show.blade.php`:
  Template A with filter toolbar and status pills.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/Frontend/ResepsionisViewsTest.php`
Expected: PASS

- [ ] **Step 5: Run full existing feature tests to ensure zero regression**

Run: `php artisan test tests/Feature/ResepsionisTest.php`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add resources/views/resepsionis/ tests/Feature/Frontend/ResepsionisViewsTest.php
git commit -m "feat(ui): refactor resepsionis views to mobile-first Templates A & B"
```

---

### Task 7: Kasir & Farmasi Views Refactoring (Template A, C, & Print)

**Files:**
- Modify: `resources/views/resepsionis/tagihan/index.blade.php`
- Modify: `resources/views/resepsionis/tagihan/show.blade.php`
- Modify: `resources/views/resepsionis/tagihan/kuitansi.blade.php`
- Modify: `resources/views/resepsionis/obat/index.blade.php`
- Modify: `resources/views/resepsionis/obat/show.blade.php`
- Create: `tests/Feature/Frontend/TagihanObatViewsTest.php`

**Interfaces:**
- Consumes: `DESIGN.md` Bagian 10.3, 10.5, 12 (Print document specs)
- Produces:
  - `tagihan/index.blade.php`: Template A, No. Tagihan mono, Patient name, right-aligned Rupiah total, status pill.
  - `tagihan/show.blade.php`: Template C with Patient Header, itemized service and medication table, payments summary, payment form with exact change calculation and `Terima Pembayaran` primary button.
  - `tagihan/kuitansi.blade.php`: Clean monochrome print layout (`@media print` hides buttons/nav, 100% contrast, mono currency).
  - `obat/index.blade.php`: Template A with stock status pills (`Menipis`, `Habis`).
  - `obat/show.blade.php`: Medicine detail and batch tracking.

- [ ] **Step 1: Write the failing test**

```php
// tests/Feature/Frontend/TagihanObatViewsTest.php
use App\Models\Tagihan;
use App\Models\User;
use Database\Seeders\KlinikSeeder;

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('tagihan show view renders patient header and mono total', function () {
    $user = User::where('role', 'resepsionis')->first();
    $tagihan = Tagihan::first();

    $response = $this->actingAs($user)->get(route('tagihan.show', $tagihan));
    $response->assertOk();
    $response->assertSee('RM:');
    $response->assertSee('font-mono', false);
    $response->assertSee('Terima Pembayaran');
});

test('kuitansi view adheres to print specifications', function () {
    $user = User::where('role', 'resepsionis')->first();
    $tagihan = Tagihan::first();

    $response = $this->actingAs($user)->get(route('tagihan.kuitansi', $tagihan));
    $response->assertOk();
    $response->assertSee('@media print', false);
    $response->assertSee('font-mono', false);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Frontend/TagihanObatViewsTest.php`
Expected: FAIL.

- [ ] **Step 3: Refactor Tagihan & Obat views**

- `resepsionis/tagihan/index.blade.php`:
  Template A with filter toolbar, status pills (`Belum Bayar`, `Lunas`), right-aligned mono totals.
- `resepsionis/tagihan/show.blade.php`:
  Template C:
  - Header: Patient Header with 2 identifiers.
  - Panel: Itemized service & medicine table, mono amounts.
  - Payment Form: Methods (`tunai`, `qris`, `debit`), amount input with change display.
  - Action button: `<x-primary-button>Terima Pembayaran</x-primary-button>`.
- `resepsionis/tagihan/kuitansi.blade.php`:
  Strict `@media print` layout without headers, footers, or buttons during printing. Clean high-contrast monochrome, tabular monospace currency amounts.
- `resepsionis/obat/index.blade.php`:
  Template A with stock status pills (Menipis [Warning], Habis [Danger], Tersedia [normal number]).
- `resepsionis/obat/show.blade.php`:
  Template C detail view.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/Frontend/TagihanObatViewsTest.php`
Expected: PASS

- [ ] **Step 5: Run full existing feature tests to ensure zero regression**

Run: `php artisan test tests/Feature/ResepsionisTest.php`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add resources/views/resepsionis/tagihan/ resources/views/resepsionis/obat/ tests/Feature/Frontend/TagihanObatViewsTest.php
git commit -m "feat(ui): refactor tagihan, kuitansi print, and obat views to mobile-first standards"
```

---

### Task 8: Dokter Module Views Refactoring (Decision-First & Template C)

**Files:**
- Modify: `resources/views/dokter/dashboard.blade.php`
- Modify: `resources/views/dokter/pemeriksaan.blade.php`
- Modify: `resources/views/dokter/pasien.blade.php`
- Create: `tests/Feature/Frontend/DokterViewsTest.php`

**Interfaces:**
- Consumes: `DESIGN.md` Bagian 9, 10.2, Template C
- Produces:
  - `dokter/dashboard.blade.php`: Decision-First "Pasien Berikutnya" card at the top (Nama, No. RM mono, usia, keluhan, allergy status) with single primary action `Panggil Pasien` / `Mulai Pemeriksaan`. Queue table below.
  - `dokter/pemeriksaan.blade.php`: Template C with:
    1. Sticky Patient Header with 2 identifiers & 3-state Allergy Banner.
    2. Horizontal-scrolling tab bar on mobile (`SOAP`, `Resep`, `Riwayat`).
    3. SOAP: Mono vital signs inputs with fixed units on the right (`mmHg`, `x/mnt`, `°C`, `%`, `kg`, `cm`). IMT auto-calculated.
    4. ICD-10 diagnosis row inputs.
    5. Prescription items with inline allergy warning blocking completion without written reason.
    6. Sticky bottom action bar (`Simpan Draf` secondary, `Selesaikan Pemeriksaan` primary) with `pb-28` container clearance on mobile so it never obscures form inputs.
  - `dokter/pasien.blade.php`: Timeline of past visits, latest on top.

- [ ] **Step 1: Write the failing test**

```php
// tests/Feature/Frontend/DokterViewsTest.php
use App\Models\Kunjungan;
use App\Models\User;
use Database\Seeders\KlinikSeeder;

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('dokter dashboard renders Pasien Berikutnya decision card with no slop arrows', function () {
    $dokter = User::where('role', 'dokter')->first();
    $response = $this->actingAs($dokter)->get(route('dokter.dashboard'));

    $response->assertOk();
    $response->assertSee('Antrean');
    $response->assertDontSee('&rarr;');
    $response->assertDontSee('->');
});

test('dokter pemeriksaan view adheres to Template C with sticky patient header and sticky action footer', function () {
    $dokter = User::where('role', 'dokter')->first();
    $kunjungan = Kunjungan::where('status', 'menunggu')->first();

    $response = $this->actingAs($dokter)->get(route('dokter.pemeriksaan.edit', $kunjungan));
    $response->assertOk();
    $response->assertSee('sticky top-14', false); // patient header
    $response->assertSee('sticky bottom-0', false); // action footer
    $response->assertSee('Selesaikan Pemeriksaan');
    $response->assertSee('Simpan Draf');
    $response->assertDontSee('&rarr;');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Frontend/DokterViewsTest.php`
Expected: FAIL.

- [ ] **Step 3: Refactor Dokter views**

- `dokter/dashboard.blade.php`:
  - Decision-First layout: Top card `Pasien Berikutnya` (`bg-surface border border-border rounded-lg p-4 sm:p-6 space-y-4`). Shows patient name, RM (mono), age, complaints, allergy banner, and `Panggil Pasien` / `Mulai Pemeriksaan`.
  - Antrean Saya table in `<x-table-container>` with responsive columns and status pills.
  - Remove any `&rarr;` decorative arrows.
- `dokter/pemeriksaan.blade.php`:
  - Template C architecture:
    - Sticky `<x-patient-header :kunjungan="$kunjungan" />` at top.
    - Mobile-friendly tab navigation (horizontal swipeable on small screens with `overflow-x-auto whitespace-nowrap`, active tab `border-b-2 border-primary-700 text-primary-700 font-semibold px-4 py-3 min-h-11`).
    - Tab 1: `Pemeriksaan (SOAP) & Diagnosis`:
      - Vital signs 1-col on mobile, 4-col on desktop, mono inputs, fixed right units (`mmHg`, `x/mnt`, `°C`, `%`, `kg`, `cm`).
      - SOAP textareas `min-h-24 px-3 py-2`.
      - ICD-10 diagnosis items with primary checkbox.
    - Tab 2: `Resep Obat`:
      - Autocomplete/select medicine, inline danger allergy warning, mandatory clinical reason textarea if prescribed to allergic patient.
    - Tab 3: `Riwayat`:
      - Timeline of previous visits.
    - Sticky action footer at bottom (`sticky bottom-0 z-10 bg-surface/95 border-t border-border p-3 sm:px-6 sm:py-3 flex justify-end gap-3`):
      - `[Simpan Draf (secondary)]` and `[Selesaikan Pemeriksaan (primary)]`.
      - Ensure main container has `pb-28` so content can scroll fully above the sticky footer on mobile.
- `dokter/pasien.blade.php`:
  - Timeline view with patient history, formatted dates, and diagnoses.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/Frontend/DokterViewsTest.php`
Expected: PASS

- [ ] **Step 5: Run full existing feature tests to ensure zero regression**

Run: `php artisan test tests/Feature/DokterTest.php`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add resources/views/dokter/ tests/Feature/Frontend/DokterViewsTest.php
git commit -m "feat(ui): refactor dokter dashboard and examination to mobile-first Template C"
```

---

### Task 9: Admin, Profile, & Auth Views Refactoring (Template A, B, & Clean Auth)

**Files:**
- Modify: `resources/views/admin/user/index.blade.php`
- Modify: `resources/views/admin/user/form.blade.php`
- Modify: `resources/views/admin/log/index.blade.php`
- Modify: `resources/views/auth/login.blade.php`
- Modify: `resources/views/auth/forgot-password.blade.php`
- Modify: `resources/views/auth/reset-password.blade.php`
- Modify: `resources/views/profile/edit.blade.php`
- Modify: `resources/views/profile/partials/*.blade.php`
- Create: `tests/Feature/Frontend/AdminAuthViewsTest.php`

**Interfaces:**
- Consumes: `DESIGN.md` Bagian 10.4, 11, 16
- Produces:
  - `admin/user/index.blade.php`: Template A, user list, confirmation modal for deactivation/delete instead of browser alert.
  - `admin/user/form.blade.php`: Template B, user form with explicit `(wajib)` labels and role selection.
  - `admin/log/index.blade.php`: Template A, Audit Log read-only table with mono timestamps, user, method, path, status.
  - `auth/login.blade.php`: Clean mobile-first login card, high contrast, accessible labels.
  - `profile/edit.blade.php`: Template B grouped forms.

- [ ] **Step 1: Write the failing test**

```php
// tests/Feature/Frontend/AdminAuthViewsTest.php
use App\Models\User;
use Database\Seeders\KlinikSeeder;

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('admin user view uses confirmation modal instead of browser native alert', function () {
    $admin = User::where('role', 'admin')->first();
    $response = $this->actingAs($admin)->get(route('admin.user.index'));

    $response->assertOk();
    $response->assertDontSee("onsubmit=\"return confirm");
    $response->assertSee('x-modal');
});

test('login screen is clean without slop or emoji', function () {
    $response = $this->get(route('login'));
    $response->assertOk();
    $response->assertSee('Email');
    $response->assertSee('Password');
    $response->assertSee('h-9');
    $response->assertDontSee('✨');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Frontend/AdminAuthViewsTest.php`
Expected: FAIL.

- [ ] **Step 3: Refactor Admin, Auth, and Profile views**

- `admin/user/index.blade.php`:
  Template A with `<x-table-container>`. Replace native inline `onsubmit="return confirm(...)"` with `<x-confirm-dialog>` component with explicit object name (`Hapus pengguna [nama]?`).
- `admin/user/form.blade.php`:
  Template B with `(wajib)` labels, role select, password input, and right-aligned actions.
- `admin/log/index.blade.php`:
  Template A with filter toolbar and read-only audit log table. Timestamps formatted in mono.
- `auth/login.blade.php` & guest layouts:
  Mobile-first card, inputs with `h-9` desktop / `h-11` touch targets, high contrast labels, zero emojis.
- `profile/edit.blade.php` and partials:
  Template B layout with grouped profile update and password change cards.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/Frontend/AdminAuthViewsTest.php`
Expected: PASS

- [ ] **Step 5: Run full existing feature tests to ensure zero regression**

Run: `php artisan test tests/Feature/AdminTest.php tests/Feature/Auth/`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/ resources/views/auth/ resources/views/profile/ tests/Feature/Frontend/AdminAuthViewsTest.php
git commit -m "feat(ui): refactor admin, auth, and profile views to mobile-first standards"
```

---

### Task 10: End-to-End Verification & Design Gate Audit

**Files:**
- Create: `tests/Feature/Frontend/DesignGateAuditTest.php`
- Modify: Any view failing the audit

**Interfaces:**
- Consumes: `DESIGN.md` Bagian 16 (Banned Patterns) & Bagian 17 (Delivery Gate Checklist), `antislop-layoutmobile` checklist
- Produces: Comprehensive automated regression test asserting 100% compliance across all views in `resources/views/`.

- [ ] **Step 1: Write the automated Design Gate Audit test**

```php
// tests/Feature/Frontend/DesignGateAuditTest.php
test('all blade view files are completely free from banned slop patterns', function () {
    $views = glob(resource_path('views/**/*.blade.php'));
    $views = array_merge($views, glob(resource_path('views/*.blade.php')));

    $bannedPatterns = [
        'backdrop-blur' => 'Glassmorphism backdrop-blur is banned (R-10)',
        'animate-pulse' => 'Pulsing animations are banned (R-19, R-31)',
        '&rarr;' => 'Decorative button arrows are banned (R-08)',
        '->' => 'Decorative button arrows are banned (R-08)',
        '#000000' => 'Pure black is banned, use #0F172A (DESIGN.md section 3)',
        'John Doe' => 'Generic placeholder John Doe is banned (R-23, R-38)',
        'Acme' => 'Generic company name Acme is banned (R-23)',
    ];

    $violations = [];

    foreach ($views as $viewPath) {
        $content = file_get_contents($viewPath);
        $relativePath = str_replace(resource_path('views/'), '', $viewPath);

        foreach ($bannedPatterns as $pattern => $message) {
            if (str_contains($content, $pattern)) {
                $violations[] = "{$relativePath}: {$message} [Found: '{$pattern}']";
            }
        }

        // Emoji check (Unicode range for emojis)
        if (preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $content)) {
            $violations[] = "{$relativePath}: Emoji found in UI (R-04)";
        }
    }

    expect($violations)->toBeEmpty(implode("\n", $violations));
});
```

- [ ] **Step 2: Run test to identify any remaining violations across the entire codebase**

Run: `php artisan test tests/Feature/Frontend/DesignGateAuditTest.php`
Expected: Any existing violations reported.

- [ ] **Step 3: Fix any reported violations in all views**

Surgically remove any offending classes, emojis, or decorative arrows.

- [ ] **Step 4: Run the full test suite**

Run: `php artisan test --compact`
Expected: 100% PASS across all existing tests and new design contract tests.

- [ ] **Step 5: Run asset build to verify clean bundling**

Run: `npm run build`
Expected: Clean Vite production build with zero errors.

- [ ] **Step 6: Commit**

```bash
git add resources/views/ tests/Feature/Frontend/DesignGateAuditTest.php
git commit -m "test(audit): enforce automated anti-slop and delivery gate audit across all views"
```

---

## Execution Handoff

Plan complete and saved to `docs/superpowers/plans/2026-11-05-mobile-first-frontend-redesign.md`. Please review the plan. Which execution approach would you prefer?

- **Subagent-driven** - A fresh subagent implements each task and a fresh reviewer checks it before the next one starts, then a whole-branch review at the end. Most thorough; costs a fresh context per task and per review.
- **Native** - I implement every task myself in this session, the way this harness runs work, then one fresh reviewer on the most capable model checks the whole branch. Cheapest and fastest; no independent review until the end. Runs well with a mid-tier session model, since the plan carries the design.

For this plan I recommend **Native**, because the tasks build incrementally on the same Tailwind v4 styling and Blade component hierarchy, with full Pest automated contract tests proving each step without context switching. Does the plan capture what you want, and which approach should we use?

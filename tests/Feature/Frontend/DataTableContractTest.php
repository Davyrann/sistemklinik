<?php

use Illuminate\Support\Facades\File;

test('table container component renders card styling and overflow container', function () {
    $view = $this->blade(
        '<x-table-container><table id="test-table"><tbody><tr><td>Data</td></tr></tbody></table></x-table-container>'
    );

    // Card container classes
    $view->assertSee('bg-surface');
    $view->assertSee('border');
    $view->assertSee('border-border');
    $view->assertSee('shadow-sm');
    $view->assertSee('rounded-lg');
    $view->assertSee('p-0');
    $view->assertSee('overflow-hidden');

    // Overflow-x container for mobile horizontal scrolling
    $view->assertSee('overflow-x-auto');
    $view->assertSee('w-full');
    $view->assertSee('-webkit-overflow-scrolling: touch');

    // Slot content preserved
    $view->assertSee('test-table');
    $view->assertSee('Data');
});

test('table container allows attribute merging and unscrollable override', function () {
    $viewCustom = $this->blade(
        '<x-table-container class="my-custom-class"><p>Custom</p></x-table-container>'
    );

    $viewCustom->assertSee('my-custom-class');
    $viewCustom->assertSee('bg-surface');
    $viewCustom->assertSee('overflow-x-auto');

    $viewNonScrollable = $this->blade(
        '<x-table-container :scrollable="false"><div>Direct child</div></x-table-container>'
    );

    $viewNonScrollable->assertSee('bg-surface');
    $viewNonScrollable->assertDontSee('overflow-x-auto');
    $viewNonScrollable->assertSee('Direct child');
});

test('datatable component styles contain overflow-x container and touch scrolling', function () {
    $view = $this->blade('<x-datatable />@stack("styles")');

    // Container overflow-x auto, width 100%, touch scrolling
    $view->assertSee('div.dt-container', false);
    $view->assertSee('overflow-x: auto', false);
    $view->assertSee('width: 100%', false);
    $view->assertSee('-webkit-overflow-scrolling: touch', false);
});

test('datatable component specifies header rhythm', function () {
    $view = $this->blade('<x-datatable />@stack("styles")');

    // Header rhythm: px-3 py-2 (8px 12px), text-xs (12px), font-medium (500), text-text-muted, bg-bg, border-b border-border-subtle
    $view->assertSee('table.dataTable > thead > tr > th', false);
    $view->assertSee('padding: 8px 12px', false);
    $view->assertSee('font-size: 12px', false);
    $view->assertSee('font-weight: 500', false);
    $view->assertSee('--color-text-muted', false);
    $view->assertSee('--color-bg', false);
    $view->assertSee('--color-border-subtle', false);
});

test('datatable component specifies cell rhythm', function () {
    $view = $this->blade('<x-datatable />@stack("styles")');

    // Cell rhythm: px-3 py-3 (12px 12px), text-sm (14px), border-t border-border-subtle, hover:bg-primary-50
    $view->assertSee('table.dataTable > tbody > tr > td', false);
    $view->assertSee('padding: 12px 12px', false);
    $view->assertSee('font-size: 14px', false);
    $view->assertSee('border-top: 1px solid var(--color-border-subtle', false);
    $view->assertSee('--color-primary-50', false);
});

test('datatable component specifies toolbar rhythm', function () {
    $view = $this->blade('<x-datatable />@stack("styles")');

    // Toolbar: px-4 py-3 (12px 16px), gap-3 (12px), border-b border-border
    $view->assertSee('padding: 12px 16px', false);
    $view->assertSee('gap: 12px', false);
    $view->assertSee('border-bottom: 1px solid var(--color-border', false);
});

test('datatable component specifies pagination and touch targets', function () {
    $view = $this->blade('<x-datatable />@stack("styles")');

    // Pagination: px-4 py-3 (12px 16px), border-t border-border
    $view->assertSee('border-top: 1px solid var(--color-border', false);

    // Active page button: bg-primary-700, text white, rounded-md (8px), h-9 min-w-9 (36px)
    $view->assertSee('--color-primary-700', false);
    $view->assertSee('#FFFFFF', false);
    $view->assertSee('--radius-md', false);
    $view->assertSee('height: 36px', false);
    $view->assertSee('min-width: 36px', false);

    // Touch coarse: h-11 min-w-11 (44px)
    $view->assertSee('@media (pointer: coarse)', false);
    $view->assertSee('height: 44px', false);
    $view->assertSee('min-width: 44px', false);
});

test('datatable component specifies right-aligned tabular numbers for currency and counts', function () {
    $view = $this->blade('<x-datatable />@stack("styles")');

    // Right-aligned tabular numbers
    $view->assertSee('table.dataTable th.dt-type-numeric', false);
    $view->assertSee('table.dataTable td.dt-type-numeric', false);
    $view->assertSee('text-align: right', false);
    $view->assertSee('font-variant-numeric: tabular-nums', false);
    $view->assertSee('JetBrains Mono', false);
});

test('datatable source contains zero banned emojis', function () {
    $content = File::get(resource_path('views/components/datatable.blade.php'));
    $tableContainerContent = File::get(resource_path('views/components/table-container.blade.php'));

    // Check for common emoji ranges
    $emojiPattern = '/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u';
    expect(preg_match($emojiPattern, $content))->toBe(0);
    expect(preg_match($emojiPattern, $tableContainerContent))->toBe(0);
});

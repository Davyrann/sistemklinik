<?php

use Illuminate\Support\Facades\File;

test('css tokens are defined and match design contract', function () {
    $cssPath = resource_path('css/app.css');
    expect(File::exists($cssPath))->toBeTrue();

    $css = File::get($cssPath);

    // Core Neutrals
    expect($css)->toContain('--color-bg: #F1F5F9;');
    expect($css)->toContain('--color-surface: #FFFFFF;');
    expect($css)->toContain('--color-text: #0F172A;');
    expect($css)->toContain('--color-text-muted: #475569;');
    expect($css)->toContain('--color-text-subtle: #64748B;');
    expect($css)->toContain('--color-border: #CBD5E1;');
    expect($css)->toContain('--color-border-subtle: #E2E8F0;');

    // Primary Teal
    expect($css)->toContain('--color-primary-50: #F0FDFA;');
    expect($css)->toContain('--color-primary-100: #CCFBF1;');
    expect($css)->toContain('--color-primary-600: #0D9488;');
    expect($css)->toContain('--color-primary-700: #0F766E;');
    expect($css)->toContain('--color-primary-800: #115E59;');
    expect($css)->toContain('--color-primary-900: #134E4A;');

    // Status Tokens
    expect($css)->toContain('--color-success: #15803D;');
    expect($css)->toContain('--color-success-bg: #F0FDF4;');
    expect($css)->toContain('--color-success-bd: #BBF7D0;');

    expect($css)->toContain('--color-warning: #B45309;');
    expect($css)->toContain('--color-warning-bg: #FFFBEB;');
    expect($css)->toContain('--color-warning-bd: #FDE68A;');

    expect($css)->toContain('--color-danger: #B91C1C;');
    expect($css)->toContain('--color-danger-bg: #FEF2F2;');
    expect($css)->toContain('--color-danger-bd: #FECACA;');

    expect($css)->toContain('--color-info: #1D4ED8;');
    expect($css)->toContain('--color-info-bg: #EFF6FF;');
    expect($css)->toContain('--color-info-bd: #BFDBFE;');

    expect($css)->toContain('--color-neutral: #334155;');
    expect($css)->toContain('--color-neutral-bg: #F8FAFC;');
    expect($css)->toContain('--color-neutral-bd: #CBD5E1;');

    // Radii & Flat Shadows
    expect($css)->toContain('--radius-sm: 6px;');
    expect($css)->toContain('--radius-md: 8px;');
    expect($css)->toContain('--radius-lg: 12px;');
    expect($css)->toContain('--shadow-sm: 0 1px 3px rgba(15, 23, 42, .10);');
    expect($css)->toContain('--shadow-md: 0 4px 12px rgba(15, 23, 42, .12);');
    expect($css)->toContain('--shadow-lg: 0 12px 32px rgba(15, 23, 42, .18);');

    // Fonts
    expect($css)->toContain('--font-sans: Inter');
    expect($css)->toContain('--font-mono: "JetBrains Mono"');
});

test('css contains touch target media query and min 44px rule', function () {
    $css = File::get(resource_path('css/app.css'));

    expect($css)->toContain('@media (pointer: coarse)');
    expect($css)->toContain('min-height: 44px;');
    expect($css)->toContain('.touch-target');
});

test('css strictly bans pure black #000000 and #000 in color tokens', function () {
    $css = File::get(resource_path('css/app.css'));

    // Check that pure black hex is not used
    expect($css)->not->toMatch('/#(?:000000|000)\b/i');
});

test('tailwind config includes Inter and JetBrains Mono', function () {
    $config = File::get(base_path('tailwind.config.js'));

    expect($config)->toContain("'Inter'");
    expect($config)->toContain('JetBrains Mono');
});

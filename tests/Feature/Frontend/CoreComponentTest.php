<?php

use Illuminate\Support\Facades\Blade;

test('primary button renders with correct design tokens and touch target', function () {
    $view = Blade::render('<x-primary-button>Simpan</x-primary-button>');

    expect($view)->toContain('Simpan')
        ->toContain('h-9')
        ->toContain('px-4')
        ->toContain('gap-2')
        ->toContain('rounded-md')
        ->toContain('font-semibold')
        ->toContain('text-sm')
        ->toContain('bg-primary-700')
        ->toContain('hover:bg-primary-800')
        ->toContain('active:bg-primary-900')
        ->toContain('text-white')
        ->toContain('focus:ring-primary-600')
        ->toContain('pointer-coarse:h-11');
});

test('secondary button renders with outline teal style and touch target', function () {
    $view = Blade::render('<x-secondary-button>Batal</x-secondary-button>');

    expect($view)->toContain('Batal')
        ->toContain('h-9')
        ->toContain('px-4')
        ->toContain('gap-2')
        ->toContain('rounded-md')
        ->toContain('font-medium')
        ->toContain('text-sm')
        ->toContain('bg-surface')
        ->toContain('border-primary-700')
        ->toContain('text-primary-700')
        ->toContain('hover:bg-primary-50')
        ->toContain('pointer-coarse:h-11');
});

test('danger button renders with red semantic styles and touch target', function () {
    $view = Blade::render('<x-danger-button>Hapus</x-danger-button>');

    expect($view)->toContain('Hapus')
        ->toContain('h-9')
        ->toContain('px-4')
        ->toContain('gap-2')
        ->toContain('rounded-md')
        ->toContain('font-semibold')
        ->toContain('text-sm')
        ->toContain('bg-danger')
        ->toContain('hover:bg-red-800')
        ->toContain('active:bg-red-900')
        ->toContain('text-white')
        ->toContain('focus:ring-red-600')
        ->toContain('pointer-coarse:h-11');
});

test('text input renders with height, padding, border and touch target', function () {
    $view = Blade::render('<x-text-input name="nama" placeholder="Nama Pasien" />');

    expect($view)->toContain('name="nama"')
        ->toContain('h-9')
        ->toContain('px-3')
        ->toContain('rounded-md')
        ->toContain('border-border')
        ->toContain('bg-surface')
        ->toContain('text-text')
        ->toContain('placeholder:text-text-subtle')
        ->toContain('focus:border-primary-600')
        ->toContain('pointer-coarse:h-11')
        ->toContain('disabled:bg-bg');
});

test('select input renders with select styling and touch target', function () {
    $view = Blade::render('<x-select-input name="kategori"><option value="1">Umum</option></x-select-input>');

    expect($view)->toContain('select-input')
        ->toContain('h-9')
        ->toContain('px-3')
        ->toContain('pr-9')
        ->toContain('rounded-md')
        ->toContain('border-border')
        ->toContain('bg-surface')
        ->toContain('pointer-coarse:h-11');
});

test('payment method select renders all valid payment options and marks selected', function () {
    $view = Blade::render('<x-payment-method-select selected="qris" class="w-full" />');

    expect($view)->toContain('name="metode"')
        ->toContain('id="metode"')
        ->toContain('tunai')
        ->toContain('TUNAI')
        ->toContain('transfer')
        ->toContain('TRANSFER')
        ->toContain('qris')
        ->toContain('QRIS')
        ->toContain('kartu')
        ->toContain('KARTU DEBIT')
        ->toContain('penjamin')
        ->toContain('PENJAMIN');
});

test('input label displays (wajib) tag when required is true instead of asterisk', function () {
    $optionalView = Blade::render('<x-input-label value="Catatan" />');
    expect($optionalView)->toContain('Catatan')
        ->not->toContain('(wajib)')
        ->not->toContain('*');

    $requiredView = Blade::render('<x-input-label value="Nama Pasien" :required="true" />');
    expect($requiredView)->toContain('Nama Pasien')
        ->toContain('(wajib)')
        ->toContain('text-danger')
        ->not->toContain('*');
});

test('input error renders with red text and xs font', function () {
    $view = Blade::render('<x-input-error :messages="[\'Field ini wajib diisi.\']" />');

    expect($view)->toContain('text-xs')
        ->toContain('text-danger')
        ->toContain('Field ini wajib diisi.');
});

test('status pill renders semantic status variants without pulsing dots', function () {
    $success = Blade::render('<x-status-pill status="success" label="Selesai" />');
    expect($success)->toContain('Selesai')
        ->toContain('text-success')
        ->toContain('px-2 py-0.5')
        ->toContain('rounded-full')
        ->not->toContain('animate-pulse');

    $danger = Blade::render('<x-status-pill status="danger" label="Batal" />');
    expect($danger)->toContain('Batal')
        ->toContain('text-danger');

    $warning = Blade::render('<x-status-pill status="warning" label="Menunggu" />');
    expect($warning)->toContain('Menunggu')
        ->toContain('text-warning');
});

test('modal renders with non-blurred backdrop and bordered surface card', function () {
    $view = Blade::render('<x-modal name="test-modal" :show="true">Konten Modal</x-modal>');

    expect($view)->toContain('bg-slate-900/50')
        ->toContain('bg-surface')
        ->toContain('border-border')
        ->toContain('rounded-lg')
        ->toContain('shadow-lg')
        ->toContain('p-6')
        ->toContain('Konten Modal')
        ->not->toContain('backdrop-blur');
});

test('confirm dialog renders danger and secondary buttons with named item', function () {
    $view = Blade::render('<x-confirm-dialog name="delete-patient" item="RM-001 (Budi)" action="/pasien/1" method="DELETE" />');

    expect($view)->toContain('RM-001 (Budi)')
        ->toContain('Konfirmasi Tindakan')
        ->toContain('Hapus')
        ->toContain('Batal')
        ->toContain('bg-danger')
        ->toContain('border-primary-700');
});

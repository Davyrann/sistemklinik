<x-app-layout>
    <x-slot name="title">Pengaturan Profil</x-slot>
    <x-slot name="header">
        <div class="space-y-1">
            <h1 class="text-xl font-semibold text-text leading-tight">Pengaturan Profil</h1>
            <p class="text-xs text-text-muted">Kelola informasi akun pengguna, kata sandi, dan keamanan sistem Anda.</p>
        </div>
    </x-slot>

    <div class="px-4 py-4 sm:px-6 sm:py-6 space-y-6 max-w-4xl mx-auto">
        <div class="p-4 sm:p-6 bg-surface border border-border shadow-sm rounded-lg">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="p-4 sm:p-6 bg-surface border border-border shadow-sm rounded-lg">
            @include('profile.partials.update-password-form')
        </div>

        <div class="p-4 sm:p-6 bg-surface border border-border shadow-sm rounded-lg">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="title">Pengguna</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-text leading-tight">Kelola Pengguna</h2>
            <a href="{{ route('admin.user.create') }}"><x-primary-button>Pengguna Baru</x-primary-button></a>
        </div>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="datatable w-full text-sm">
                        <thead class="bg-bg border-b border-border">
                            <tr class="text-left text-text-muted">
                                <th class="px-6 py-3 font-medium">Nama</th>
                                <th class="px-6 py-3 font-medium">Email</th>
                                <th class="px-6 py-3 font-medium">Role</th>
                                <th class="px-6 py-3 font-medium" data-orderable="false">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($users as $u)
                                <tr class="hover:bg-primary-50/40">
                                    <td class="px-6 py-3 font-medium text-text">{{ $u->name }}</td>
                                    <td class="px-6 py-3 text-text-muted">{{ $u->email }}</td>
                                    <td class="px-6 py-3">
                                        <span class="text-xs px-2 py-1 rounded-full border font-medium {{ $u->role === 'admin' ? 'bg-primary-50 text-primary-700 border-primary-200' : ($u->role === 'dokter' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-50 text-slate-600 border-slate-200') }}">{{ ucfirst($u->role) }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex gap-3">
                                            <a href="{{ route('admin.user.edit', $u) }}" class="text-primary-700 underline text-sm">Edit</a>
                                            @if ($u->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.user.destroy', $u) }}" onsubmit="return confirm('Hapus pengguna {{ $u->name }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 underline text-sm">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

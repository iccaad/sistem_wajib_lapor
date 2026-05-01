@extends('layouts.admin')

@section('title', 'Jenis Pelanggaran')
@section('page-title', 'Jenis Pelanggaran')
@section('breadcrumb', 'Manajemen data jenis pelanggaran')

@section('content')

<div class="mb-5 flex justify-end">
    <a href="{{ route('admin.violation-types.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-accent hover:bg-brand-accent/80 text-white text-sm font-bold rounded-xl shadow-lg shadow-black/10 transition transform active:scale-95">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Jenis
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-brand-light/10">
                <tr>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-brand-secondary uppercase tracking-[0.1em]">#</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-brand-secondary uppercase tracking-[0.1em]">Nama Jenis Pelanggaran</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-brand-secondary uppercase tracking-[0.1em]">Deskripsi</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black text-brand-secondary uppercase tracking-[0.1em]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($violationTypes as $vt)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-xs font-medium text-gray-400">
                            {{ $loop->iteration + $violationTypes->firstItem() - 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900">{{ $vt->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ Str::limit($vt->description, 50) ?: '—' }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form id="delete-form-{{ $vt->id }}" method="POST" action="{{ route('admin.violation-types.destroy', $vt) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                            
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.violation-types.edit', $vt) }}" 
                                   class="p-2 rounded-lg text-orange-500 hover:bg-orange-50 transition-all duration-200"
                                   title="Edit Jenis">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </a>
                                <button type="button" 
                                        onclick="if(confirm('Apakah Anda yakin ingin menghapus jenis pelanggaran ini?')) document.getElementById('delete-form-{{ $vt->id }}').submit();"
                                        class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200"
                                        title="Hapus">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 text-sm">
                            Belum ada data jenis pelanggaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($violationTypes->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $violationTypes->links() }}
        </div>
    @endif
</div>

@endsection

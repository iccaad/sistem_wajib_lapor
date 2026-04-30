{{--
    Breadcrumb Component
    
    Usage in any admin view:
    @section('breadcrumb')
        <x-breadcrumb :items="[
            ['label' => 'Peserta', 'url' => route('admin.participants.index')],
            ['label' => 'Budi Santoso', 'url' => route('admin.participants.show', $participant)],
            ['label' => 'Detail'],
        ]" />
    @endsection
    
    Or simple string (existing admin layout still works):
    @section('breadcrumb', 'Kelola data peserta')
--}}

@props(['items' => []])

@if (count($items))
    <nav class="flex items-center gap-1.5 text-xs text-gray-400" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400 transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
        </a>

        @foreach ($items as $item)
            <svg class="h-3 w-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>

            @if (isset($item['url']) && !$loop->last)
                <a href="{{ $item['url'] }}" class="hover:text-indigo-400 transition-colors truncate max-w-[120px]">
                    {{ $item['label'] }}
                </a>
            @else
                <span class="text-gray-400 font-medium truncate max-w-[120px]">{{ $item['label'] }}</span>
            @endif
        @endforeach
    </nav>
@endif



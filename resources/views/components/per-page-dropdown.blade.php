{{--
    Reusable per-page dropdown partial.
    @param string $route  – Named route for the form action
    @param int    $current – Current per-page value ($items->perPage())
--}}
<div class="flex items-center justify-end mb-4">
    <form method="GET" action="{{ route($route) }}" class="inline-flex items-center gap-2 text-sm text-gray-500">
        @foreach(request()->except(['per_page', 'page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <span class="font-medium text-gray-600">Tampilkan</span>
        <select name="per_page"
                onchange="this.form.submit()"
                class="appearance-none px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:border-gray-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 cursor-pointer">
            @foreach([5, 10, 15, 20] as $size)
                <option value="{{ $size }}" {{ $current == $size ? 'selected' : '' }}>{{ $size }}</option>
            @endforeach
        </select>
        <span>data per halaman</span>
    </form>
</div>

@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-md border-t-2 border-indigo-500 shadow-gray-950/50']) }}>



@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-brand-light bg-brand-light/10 focus:border-brand-accent focus:ring-brand-accent rounded-lg shadow-sm transition-all duration-200']) }}>

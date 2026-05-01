<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-brand-accent border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-brand-secondary active:bg-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-accent focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-brand-accent/20']) }}>
    {{ $slot }}
</button>

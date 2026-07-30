<script lang="ts">
  import ThiingsIcon from './ThiingsIcon.svelte';

  let {
    icon = 'tooth',
    label = '',
    value = '' as string | number,
    tone = 'neutral',
    hint = '',
    empty = false,
    delay = 0,
    onclick = undefined as (() => void) | undefined,
    class: extra = '',
  } = $props();

  // hero = tarjeta de color saturado; los tonos "soft" comparten el mismo
  // lenguaje visual (glass + acento) para que no parezcan otra seccion.
  const tones: Record<string, { shell: string; iconBox: string; label: string; value: string; ring: string }> = {
    primary: {
      shell: 'bg-gradient-to-br from-primary-600 via-primary-600 to-primary-800 text-white border-white/20',
      iconBox: 'bg-white/20 border border-white/25',
      label: 'text-blue-50/90',
      value: 'text-white',
      ring: 'hover:shadow-[var(--shadow-glow-primary)]',
    },
    health: {
      shell: 'bg-gradient-to-br from-health-500 via-health-600 to-health-700 text-white border-white/20',
      iconBox: 'bg-white/20 border border-white/25',
      label: 'text-emerald-50/90',
      value: 'text-white',
      ring: 'hover:shadow-[var(--shadow-glow-health)]',
    },
    accent: {
      shell: 'bg-gradient-to-br from-accent-500 via-accent-600 to-accent-700 text-white border-white/20',
      iconBox: 'bg-white/20 border border-white/25',
      label: 'text-violet-50/90',
      value: 'text-white',
      ring: 'hover:shadow-[var(--shadow-glow-accent)]',
    },
    indigo: {
      shell: 'glass-panel',
      iconBox: 'bg-indigo-500/12 border border-indigo-500/20',
      label: 'text-slate-500',
      value: 'text-slate-900',
      ring: 'hover:shadow-lift',
    },
    emerald: {
      shell: 'glass-panel',
      iconBox: 'bg-emerald-500/12 border border-emerald-500/20',
      label: 'text-slate-500',
      value: 'text-slate-900',
      ring: 'hover:shadow-lift',
    },
    teal: {
      shell: 'glass-panel',
      iconBox: 'bg-teal-500/12 border border-teal-500/20',
      label: 'text-slate-500',
      value: 'text-slate-900',
      ring: 'hover:shadow-lift',
    },
    neutral: {
      shell: 'glass-panel',
      iconBox: 'bg-slate-500/10 border border-slate-500/15',
      label: 'text-slate-500',
      value: 'text-slate-900',
      ring: 'hover:shadow-lift',
    },
  };

  const t = $derived(tones[tone] || tones.neutral);
  const isHero = $derived(tone === 'primary' || tone === 'health' || tone === 'accent');
  const interactive = $derived(typeof onclick === 'function');
</script>

<svelte:element
  this={interactive ? 'button' : 'div'}
  role={interactive ? 'button' : undefined}
  {onclick}
  class="group relative w-full text-left rounded-2xl border p-4 sm:p-5 overflow-hidden animate-rise focus-ring
    transition-[transform,box-shadow] duration-200 ease-out
    {isHero ? 'shadow-float' : ''} {t.shell} {t.ring}
    {interactive ? 'hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99] cursor-pointer' : ''} {extra}"
  style="--i: {delay}"
>
  {#if isHero}
    <!-- Brillo especular sutil, refuerza la sensacion de capa de vidrio -->
    <span
      class="pointer-events-none absolute -top-16 -right-10 w-40 h-40 rounded-full bg-white/15 blur-2xl
        transition-opacity duration-300 opacity-70 group-hover:opacity-100"
    ></span>
  {/if}

  <div class="relative flex items-start justify-between gap-3">
    <div
      class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden
        backdrop-blur-sm transition-transform duration-200 ease-out group-hover:scale-105 {t.iconBox}"
    >
      <ThiingsIcon name={icon} size={isHero ? 26 : 22} alt="" />
    </div>

    {#if empty}
      <!-- Estado en cero: el valor se atenua y se acompana de texto, para que
           no se lea como un fallo de carga -->
      <div class="text-right">
        <!-- slate-500 (no slate-300): 4.7:1 sobre la superficie glass, cumple AA -->
        <span class="num block text-3xl sm:text-4xl font-bold {isHero ? 'text-white/55' : 'text-slate-500'}">
          {value}
        </span>
      </div>
    {:else}
      <span class="num text-3xl sm:text-4xl font-bold leading-none {t.value}">{value}</span>
    {/if}
  </div>

  <div class="relative mt-3">
    <p class="text-sm font-semibold tracking-tight {t.label}">{label}</p>
    {#if empty || hint}
      <p class="mt-1 text-xs {isHero ? 'text-white/75' : 'text-slate-500'}">
        {hint}
      </p>
    {/if}
  </div>
</svelte:element>

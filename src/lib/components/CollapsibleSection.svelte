<script lang="ts">
  import type { Snippet } from 'svelte';
  import FieldIcon from './FieldIcon.svelte';

  // `iconName` + `tone` identifican la seccion por color (identidad, familia,
  // clinico, dinero...) ademas del titulo.
  let {
    title = '',
    open = false,
    ontoggle = () => {},
    iconName = '',
    tone = 'primary',
    children,
  }: {
    title?: string;
    open?: boolean;
    ontoggle?: () => void;
    iconName?: string;
    tone?: string;
    children?: Snippet;
  } = $props();

  const tones: Record<string, string> = {
    primary: 'bg-primary-600/12 border-primary-600/20 text-primary-600',
    indigo: 'bg-indigo-500/12 border-indigo-500/20 text-indigo-600',
    violet: 'bg-violet-500/12 border-violet-500/20 text-violet-600',
    sky: 'bg-sky-500/12 border-sky-500/20 text-sky-600',
    emerald: 'bg-emerald-500/12 border-emerald-500/20 text-emerald-600',
    teal: 'bg-teal-500/12 border-teal-500/20 text-teal-600',
    amber: 'bg-amber-500/15 border-amber-500/25 text-amber-600',
    rose: 'bg-rose-500/12 border-rose-500/20 text-rose-600',
    slate: 'bg-slate-500/10 border-slate-500/20 text-slate-500',
  };

  const chip = $derived(tones[tone] || tones.primary);
</script>

<section class="rounded-2xl surface-subtle overflow-hidden">
  <button
    type="button"
    class="group w-full flex items-center gap-3 px-4 py-3 text-left focus-ring
      transition-colors duration-200 ease-out hover:bg-white/70"
    aria-expanded={open}
    onclick={ontoggle}
  >
    {#if iconName}
      <span
        class="w-8 h-8 rounded-xl border flex items-center justify-center flex-shrink-0
          [&_svg]:w-4.5 [&_svg]:h-4.5 transition-transform duration-200 ease-out
          group-hover:scale-105 {chip}"
        aria-hidden="true"
      >
        <FieldIcon name={iconName} />
      </span>
    {/if}
    <span class="flex-1 text-sm font-semibold tracking-tight text-slate-800">{title}</span>
    <span
      class="w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0
        transition-colors duration-200 ease-out
        {open ? 'bg-primary-600/12 text-primary-600' : 'bg-slate-500/10 text-slate-500 group-hover:text-primary-600'}"
      aria-hidden="true"
    >
      <svg
        class="w-4 h-4 transition-transform duration-200 ease-out {open ? 'rotate-90' : ''}"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
      </svg>
    </span>
  </button>

  {#if open}
    <div class="px-4 pb-4 animate-fade-in-up">
      {@render children?.()}
    </div>
  {/if}
</section>

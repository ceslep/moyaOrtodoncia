<script lang="ts">
  import type { Snippet } from 'svelte';
  import FieldIcon from './FieldIcon.svelte';

  // Par etiqueta/valor con alineacion consistente (label arriba, valor abajo).
  // `tone` colorea el chip del icono segun el tipo de dato (contacto, dinero,
  // fecha, clinico...) para que el ojo lo ubique sin leer la etiqueta.
  // `iconName` usa el mapa de FieldIcon; `icon` permite un snippet propio.
  let {
    label = '',
    value = '' as string | number | null | undefined,
    span = '',
    strong = false,
    tone = 'slate',
    iconName = '',
    icon,
    children,
  }: {
    label?: string;
    value?: string | number | null | undefined;
    span?: string;
    strong?: boolean;
    tone?: string;
    iconName?: string;
    icon?: Snippet;
    children?: Snippet;
  } = $props();

  const tones: Record<string, string> = {
    slate: 'bg-slate-500/10 border-slate-500/20 text-slate-500',
    blue: 'bg-blue-500/12 border-blue-500/20 text-blue-600',
    indigo: 'bg-indigo-500/12 border-indigo-500/20 text-indigo-600',
    violet: 'bg-violet-500/12 border-violet-500/20 text-violet-600',
    sky: 'bg-sky-500/12 border-sky-500/20 text-sky-600',
    teal: 'bg-teal-500/12 border-teal-500/20 text-teal-600',
    emerald: 'bg-emerald-500/12 border-emerald-500/20 text-emerald-600',
    amber: 'bg-amber-500/15 border-amber-500/25 text-amber-600',
    orange: 'bg-orange-500/12 border-orange-500/20 text-orange-600',
    rose: 'bg-rose-500/12 border-rose-500/20 text-rose-600',
    pink: 'bg-pink-500/12 border-pink-500/20 text-pink-600',
    red: 'bg-red-500/12 border-red-500/20 text-red-600',
  };

  const chip = $derived(tones[tone] || tones.slate);
</script>

<div class="flex items-start gap-2.5 rounded-xl bg-white/70 border border-white/80 px-3 py-2.5 {span}">
  {#if icon || iconName}
    <span
      class="mt-0.5 w-7 h-7 rounded-lg border flex items-center justify-center flex-shrink-0
        [&_svg]:w-4 [&_svg]:h-4 {chip}"
      aria-hidden="true"
    >
      {#if icon}
        {@render icon()}
      {:else}
        <FieldIcon name={iconName} />
      {/if}
    </span>
  {/if}
  <div class="min-w-0 flex-1">
    <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">{label}</dt>
    <dd class="mt-0.5 text-sm break-words {strong ? 'font-semibold text-slate-900' : 'text-slate-800'}">
      {#if children}
        {@render children()}
      {:else}
        {value === null || value === undefined || value === '' ? '—' : value}
      {/if}
    </dd>
  </div>
</div>

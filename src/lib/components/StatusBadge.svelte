<script lang="ts">
  // Sistema unico de pills semanticas. Mismo API que Badge ({text, color}).
  let { text = '', color = 'gray', dot = false, onLight = true } = $props();

  const tones: Record<string, string> = {
    green: 'bg-emerald-500/12 text-emerald-700 border-emerald-600/25',
    red: 'bg-red-500/12 text-red-700 border-red-600/25',
    yellow: 'bg-amber-500/15 text-amber-800 border-amber-600/25',
    blue: 'bg-blue-500/12 text-blue-700 border-blue-600/25',
    gray: 'bg-slate-500/10 text-slate-700 border-slate-500/25',
    purple: 'bg-violet-500/12 text-violet-700 border-violet-600/25',
    orange: 'bg-orange-500/12 text-orange-800 border-orange-600/25',
  };

  // Variante para fondos con gradiente de color (header de ficha)
  const onDark: Record<string, string> = {
    green: 'bg-white/25 text-white border-white/40',
    red: 'bg-white/25 text-white border-white/40',
    yellow: 'bg-white/25 text-white border-white/40',
    blue: 'bg-white/25 text-white border-white/40',
    gray: 'bg-white/20 text-white border-white/35',
    purple: 'bg-white/25 text-white border-white/40',
    orange: 'bg-white/25 text-white border-white/40',
  };

  const dots: Record<string, string> = {
    green: 'bg-emerald-500',
    red: 'bg-red-500',
    yellow: 'bg-amber-500',
    blue: 'bg-blue-500',
    gray: 'bg-slate-400',
    purple: 'bg-violet-500',
    orange: 'bg-orange-500',
  };

  const palette = $derived(onLight ? tones : onDark);
</script>

<span
  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[11px] font-semibold leading-none tracking-wide whitespace-nowrap {palette[
    color
  ] || palette.gray}"
>
  {#if dot}
    <span class="w-1.5 h-1.5 rounded-full {onLight ? dots[color] || dots.gray : 'bg-white'}"></span>
  {/if}
  {text}
</span>

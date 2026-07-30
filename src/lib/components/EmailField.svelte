<script lang="ts">
  import FieldIcon from './FieldIcon.svelte';

  let {
    label = '',
    value = null as string | null,
    iconName = 'mail',
    tone = 'sky',
  }: {
    label?: string;
    value?: string | null;
    iconName?: string;
    tone?: string;
  } = $props();

  const tones: Record<string, string> = {
    sky: 'bg-sky-500/12 border-sky-500/20 text-sky-600',
  };

  const chip = $derived(tones[tone] || tones.sky);

  function isValidEmail(raw: string | null): boolean {
    if (!raw) return false;
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(raw.trim());
  }

  const valid = $derived(isValidEmail(value));
  const mailtoHref = $derived(valid ? `mailto:${value?.trim()}` : '');
</script>

<div class="flex items-start gap-2.5 rounded-xl bg-white/70 border border-white/80 px-3 py-2.5 sm:col-span-2">
  <span
    class="mt-0.5 w-7 h-7 rounded-lg border flex items-center justify-center flex-shrink-0
      [&_svg]:w-4 [&_svg]:h-4 {chip}"
    aria-hidden="true"
  >
    <FieldIcon name={iconName} />
  </span>
  <div class="min-w-0 flex-1">
    <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">{label}</dt>
    <dd class="mt-0.5 text-sm">
      {#if !value || value.trim() === ''}
        <span class="text-slate-400">—</span>
      {:else if valid}
        <a
          href={mailtoHref}
          class="inline-flex items-center gap-1.5 font-semibold text-slate-900
            hover:text-sky-600 transition-colors duration-150
            rounded-lg px-1.5 py-0.5 -ml-1.5 hover:bg-sky-50"
          title="Enviar correo a {value}"
        >
          <svg class="w-3.5 h-3.5 text-sky-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
          </svg>
          <span class="truncate">{value}</span>
        </a>
      {:else}
        <span class="text-slate-700 break-words">{value}</span>
        <span class="ml-1.5 inline-flex items-center text-[10px] font-medium text-slate-400 bg-slate-100 rounded px-1.5 py-0.5">
          invalido
        </span>
      {/if}
    </dd>
  </div>
</div>

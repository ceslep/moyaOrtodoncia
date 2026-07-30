<script lang="ts">
  let { odontograma = {} as Record<string, { marcado: boolean; texto?: string }> } = $props();

  const dientes = {
    sup: [
      { num: 18, x: 18, y: 30 }, { num: 17, x: 42, y: 30 }, { num: 16, x: 66, y: 30 },
      { num: 15, x: 90, y: 30 }, { num: 14, x: 114, y: 30 }, { num: 13, x: 138, y: 30 },
      { num: 12, x: 162, y: 30 }, { num: 11, x: 186, y: 30 },
      { num: 21, x: 222, y: 30 }, { num: 22, x: 246, y: 30 }, { num: 23, x: 270, y: 30 },
      { num: 24, x: 294, y: 30 }, { num: 25, x: 318, y: 30 }, { num: 26, x: 342, y: 30 },
      { num: 27, x: 366, y: 30 }, { num: 28, x: 390, y: 30 },
    ],
    inf: [
      { num: 48, x: 18, y: 90 }, { num: 47, x: 42, y: 90 }, { num: 46, x: 66, y: 90 },
      { num: 45, x: 90, y: 90 }, { num: 44, x: 114, y: 90 }, { num: 43, x: 138, y: 90 },
      { num: 42, x: 162, y: 90 }, { num: 41, x: 186, y: 90 },
      { num: 31, x: 222, y: 90 }, { num: 32, x: 246, y: 90 }, { num: 33, x: 270, y: 90 },
      { num: 34, x: 294, y: 90 }, { num: 35, x: 318, y: 90 }, { num: 36, x: 342, y: 90 },
      { num: 37, x: 366, y: 90 }, { num: 38, x: 390, y: 90 },
    ],
  };

  function getD(key: string): string {
    const k = `d${key}`;
    const val = odontograma[k];
    if (!val) return '';
    if (typeof val === 'object') return val.texto || (val.marcado ? 'X' : '');
    return String(val);
  }

  function isMarked(key: string): boolean {
    const k = `d${key}`;
    const val = odontograma[k];
    if (!val) return false;
    if (typeof val === 'object') return val.marcado;
    return String(val).trim() !== '';
  }
</script>

<div class="overflow-x-auto">
  <svg viewBox="0 0 420 140" class="w-full max-w-lg mx-auto">
    <text x="210" y="14" text-anchor="middle" class="text-[10px] fill-gray-500 font-medium">SUPERIOR</text>
    <line x1="0" y1="60" x2="420" y2="60" stroke="#e5e7eb" stroke-width="0.5" stroke-dasharray="4" />

    {#each dientes.sup as d}
      <g transform="translate({d.x}, {d.y})">
        <rect width="20" height="28" rx="2"
          class={isMarked(String(d.num)) ? 'fill-red-200 stroke-red-400' : 'fill-white stroke-gray-300'}
          stroke-width="1" />
        <text x="10" y="11" text-anchor="middle" class="text-[7px] fill-gray-600 font-medium">{d.num}</text>
        {#if getD(String(d.num))}
          <text x="10" y="22" text-anchor="middle" class="text-[6px] fill-red-600 font-bold">{getD(String(d.num))}</text>
        {/if}
      </g>
    {/each}

    {#each dientes.inf as d}
      <g transform="translate({d.x}, {d.y})">
        <rect width="20" height="28" rx="2"
          class={isMarked(String(d.num)) ? 'fill-red-200 stroke-red-400' : 'fill-white stroke-gray-300'}
          stroke-width="1" />
        <text x="10" y="11" text-anchor="middle" class="text-[7px] fill-gray-600 font-medium">{d.num}</text>
        {#if getD(String(d.num))}
          <text x="10" y="22" text-anchor="middle" class="text-[6px] fill-red-600 font-bold">{getD(String(d.num))}</text>
        {/if}
      </g>
    {/each}

    <text x="210" y="136" text-anchor="middle" class="text-[10px] fill-gray-500 font-medium">INFERIOR</text>
  </svg>
</div>

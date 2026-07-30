<script lang="ts">
  let { text = '', color = 'gray' } = $props();

  const colors: Record<string, string> = {
    green: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    red: 'bg-red-50 text-red-700 ring-red-600/20',
    yellow: 'bg-amber-50 text-amber-700 ring-amber-600/20',
    blue: 'bg-blue-50 text-blue-700 ring-blue-600/20',
    gray: 'bg-gray-50 text-gray-600 ring-gray-500/20',
    purple: 'bg-purple-50 text-purple-700 ring-purple-600/20',
    orange: 'bg-orange-50 text-orange-700 ring-orange-600/20',
  };
</script>

<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold ring-1 ring-inset {colors[color] || colors.gray}">
  {text}
</span>

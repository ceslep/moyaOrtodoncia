<script lang="ts">
  import type { Snippet } from 'svelte';

  let {
    padding = 'p-4 sm:p-5',
    hover = false,
    delay = 0,
    class: extra = '',
    children,
  }: {
    padding?: string;
    hover?: boolean;
    delay?: number;
    class?: string;
    children?: Snippet;
  } = $props();
</script>

<div
  class="glass-panel rounded-2xl animate-rise {padding} {hover
    ? 'transition-[transform,box-shadow] duration-200 ease-out hover:-translate-y-0.5 hover:shadow-lift'
    : ''} {extra}"
  style="--i: {delay}"
>
  {@render children?.()}
</div>

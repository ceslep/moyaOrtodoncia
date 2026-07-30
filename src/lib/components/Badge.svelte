<script lang="ts">
  // Wrapper de compatibilidad: mismo API ({text, color}) sobre el sistema
  // unico de pills semanticas de StatusBadge.
  import StatusBadge from './StatusBadge.svelte';

  let { text = '', color = 'gray', dot = false, onLight = true } = $props();
</script>

<StatusBadge {text} {color} {dot} {onLight} />

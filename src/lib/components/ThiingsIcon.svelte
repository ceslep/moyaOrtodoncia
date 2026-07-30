<script lang="ts">
  let { name = 'tooth', size = 24, alt = '' } = $props();

  const icons: Record<string, string> = {
    'tooth': 'CO4KOn0XgL7vM3OGalhMPXtGIlprSl',
    'teeth': 'ZeYYce1tWi91YU7Z0rerVmNRCWyCkX',
    'smile': 'vqI4ZSUJYtDHPi70OXWgvJhR1GaxkQ',
    'dentist': 'tA9azjG6zk84T4fnIw0TzGruodB5HP',
    'stethoscope': 'YM427q0rp7oBdrNFxZLbdSyYBZNTjA',
    'doctor': 'ViY6rawiI2uiV9tsy3QGZXdyngtXrC',
    'patient': '04f0eoy9DeUzhiKzP4GuDz026f5nSm',
    'calendar': 'ij7GEZhfmzLNlLgDJVhkST8FIm5rJV',
    'calendar-plus': 'JDRRmMHhYVAkfx593m52YMvKWp7qFg',
    'wallet': 'r5QxxRu1dvIND6CpaRBYLTnJmCfzNM',
    'money': 'IeKGYKeLd25OWlrmd5WrSsfbD7OIPA',
    'coins': 'n9dmFV9wD0hKFc4BFPY2Mc7nByhtNY',
    'finances': '1EVaHvpmReiwwINGWc3M2wwiz9fTrp',
    'dashboard': 'Qep2rmAXunWu8R2o1FCgLLDrtiD1q2',
    'charts': 'JuGWDxW5tzUzQbZacA8V2CW0TRBQvL',
    'folder': '6TJq3TblGrgJLKvMLFj3N2YcDEXG2A',
    'profile': 'EhKPPiNizhoSfoAbU0fYut9R1LMKyZ',
    'group': 'WaLIdwjgmqnfE0GndCGu1R9ReZOal5',
    'hospital': 'rW3KN717rl4aTnOGRIuS82n0gSwwLQ',
    'medical-report': 'aMOjvnTYrZkuOnSwsXjN0cbwf59S7G',
    'health-journal': 'rqbkXC57PxSNmsUh03O6RfoKbD9R1j',
    'dental-implant': 'gwlQjwkaOz0nZ5J01PLg6L3P5OdPJs',
    'orthodontic': 'gFkL10tBikaK1PMNgT4a0DqSbUwF7X',
  };

  const CDN = 'https://lftz25oez4aqbxpq.public.blob.vercel-storage.com/image-';
  const fileId = $derived(icons[name] || icons['tooth']);
  const url = $derived(`${CDN}${fileId}.png`);
</script>

<img
  src={url}
  alt={alt || name}
  width={size}
  height={size}
  class="thiings-icon"
  style="width: {size}px; height: {size}px"
  loading="lazy"
/>

<style>
  .thiings-icon {
    object-fit: contain;
    display: inline-block;
    vertical-align: middle;
  }
</style>

<script lang="ts">
  let { size = 48, state = 'working', label = '' } = $props();

  const labels: Record<string, string> = {
    working: 'Cargando',
    searching: 'Buscando',
    solving: 'Procesando',
    listening: 'Escuchando',
    composing: 'Generando',
    shaping: 'Formando',
  };

  const displayLabel = $derived(label || labels[state] || 'Cargando');
</script>

<div class="orb-container" style="--orb-size: {size}px">
  <div class="orb {state}">
    <div class="orb-core"></div>
    <div class="orb-ring ring-1"></div>
    <div class="orb-ring ring-2"></div>
    <div class="orb-ring ring-3"></div>
    <div class="orb-dot dot-1"></div>
    <div class="orb-dot dot-2"></div>
    <div class="orb-dot dot-3"></div>
    <div class="orb-dot dot-4"></div>
    <div class="orb-dot dot-5"></div>
    <div class="orb-dot dot-6"></div>
  </div>
  {#if size >= 32}
    <p class="orb-label">{displayLabel}</p>
  {/if}
</div>

<style>
  .orb-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
  }

  .orb {
    position: relative;
    width: var(--orb-size);
    height: var(--orb-size);
  }

  .orb-core {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 40%;
    height: 40%;
    transform: translate(-50%, -50%);
    border-radius: 50%;
    background: radial-gradient(circle, #3b82f6 0%, #1e40af 100%);
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
    animation: core-pulse 2s ease-in-out infinite;
  }

  .orb-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    border-radius: 50%;
    border: 1.5px solid rgba(59, 130, 246, 0.15);
    transform: translate(-50%, -50%);
  }

  .ring-1 {
    width: 60%;
    height: 60%;
    animation: ring-rotate 3s linear infinite;
  }

  .ring-2 {
    width: 78%;
    height: 78%;
    animation: ring-rotate 4s linear infinite reverse;
  }

  .ring-3 {
    width: 96%;
    height: 96%;
    animation: ring-rotate 5s linear infinite;
  }

  .orb-dot {
    position: absolute;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #3b82f6;
  }

  .dot-1 { top: 5%; left: 50%; transform: translateX(-50%); animation: dot-orbit-1 2s linear infinite; }
  .dot-2 { top: 50%; right: 5%; transform: translateY(-50%); animation: dot-orbit-2 2.5s linear infinite; }
  .dot-3 { bottom: 5%; left: 50%; transform: translateX(-50%); animation: dot-orbit-3 3s linear infinite; }
  .dot-4 { top: 50%; left: 5%; transform: translateY(-50%); animation: dot-orbit-4 2.2s linear infinite; }
  .dot-5 { top: 15%; right: 15%; animation: dot-orbit-5 2.8s linear infinite; }
  .dot-6 { bottom: 15%; left: 15%; animation: dot-orbit-6 3.2s linear infinite; }

  .orb.searching .orb-core { background: radial-gradient(circle, #10b981 0%, #059669 100%); box-shadow: 0 0 20px rgba(16, 185, 129, 0.4); }
  .orb.searching .orb-ring { border-color: rgba(16, 185, 129, 0.15); }
  .orb.searching .orb-dot { background: #10b981; }

  .orb.solving .orb-core { background: radial-gradient(circle, #8b5cf6 0%, #7c3aed 100%); box-shadow: 0 0 20px rgba(139, 92, 246, 0.4); }
  .orb.solving .orb-ring { border-color: rgba(139, 92, 246, 0.15); }
  .orb.solving .orb-dot { background: #8b5cf6; }

  .orb.listening .orb-core { background: radial-gradient(circle, #f59e0b 0%, #d97706 100%); box-shadow: 0 0 20px rgba(245, 158, 11, 0.4); }
  .orb.listening .orb-ring { border-color: rgba(245, 158, 11, 0.15); }
  .orb.listening .orb-dot { background: #f59e0b; }

  .orb.composing .orb-core { background: radial-gradient(circle, #ec4899 0%, #db2777 100%); box-shadow: 0 0 20px rgba(236, 72, 153, 0.4); }
  .orb.composing .orb-ring { border-color: rgba(236, 72, 153, 0.15); }
  .orb.composing .orb-dot { background: #ec4899; }

  .orb.shaping .orb-core { background: radial-gradient(circle, #06b6d4 0%, #0891b2 100%); box-shadow: 0 0 20px rgba(6, 182, 212, 0.4); }
  .orb.shaping .orb-ring { border-color: rgba(6, 182, 212, 0.15); }
  .orb.shaping .orb-dot { background: #06b6d4; }

  .orb-label {
    font-size: 0.8125rem;
    font-weight: 500;
    color: #64748b;
    margin: 0;
    animation: label-fade 1.5s ease-in-out infinite;
  }

  @keyframes core-pulse {
    0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    50% { transform: translate(-50%, -50%) scale(1.15); opacity: 0.8; }
  }

  @keyframes ring-rotate {
    from { transform: translate(-50%, -50%) rotate(0deg); }
    to { transform: translate(-50%, -50%) rotate(360deg); }
  }

  @keyframes dot-orbit-1 {
    0% { top: 5%; left: 50%; opacity: 1; }
    25% { top: 50%; left: 95%; opacity: 0.6; }
    50% { top: 95%; left: 50%; opacity: 1; }
    75% { top: 50%; left: 5%; opacity: 0.6; }
    100% { top: 5%; left: 50%; opacity: 1; }
  }

  @keyframes dot-orbit-2 {
    0% { top: 50%; right: 5%; opacity: 0.6; }
    25% { top: 5%; right: 50%; opacity: 1; }
    50% { top: 50%; right: 95%; opacity: 0.6; }
    75% { top: 95%; right: 50%; opacity: 1; }
    100% { top: 50%; right: 5%; opacity: 0.6; }
  }

  @keyframes dot-orbit-3 {
    0% { bottom: 5%; left: 50%; opacity: 1; }
    25% { bottom: 50%; left: 5%; opacity: 0.6; }
    50% { bottom: 95%; left: 50%; opacity: 1; }
    75% { bottom: 50%; left: 95%; opacity: 0.6; }
    100% { bottom: 5%; left: 50%; opacity: 1; }
  }

  @keyframes dot-orbit-4 {
    0% { top: 50%; left: 5%; opacity: 0.6; }
    25% { top: 95%; left: 50%; opacity: 1; }
    50% { top: 50%; left: 95%; opacity: 0.6; }
    75% { top: 5%; left: 50%; opacity: 1; }
    100% { top: 50%; left: 5%; opacity: 0.6; }
  }

  @keyframes dot-orbit-5 {
    0% { top: 15%; right: 15%; transform: scale(0.8); }
    50% { top: 85%; right: 85%; transform: scale(1.2); }
    100% { top: 15%; right: 15%; transform: scale(0.8); }
  }

  @keyframes dot-orbit-6 {
    0% { bottom: 15%; left: 15%; transform: scale(1.2); }
    50% { bottom: 85%; left: 85%; transform: scale(0.8); }
    100% { bottom: 15%; left: 15%; transform: scale(1.2); }
  }

  @keyframes label-fade {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }
</style>

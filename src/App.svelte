<script lang="ts">
  import Sidebar from '$lib/components/Sidebar.svelte';
  import Toast from '$lib/components/Toast.svelte';
  import Dashboard from './routes/Dashboard.svelte';
  import Pacientes from './routes/Pacientes.svelte';
  import FichaPaciente from './routes/FichaPaciente.svelte';
  import Agenda from './routes/Agenda.svelte';
  import Financiero from './routes/Financiero.svelte';
  import Catalogos from './routes/Catalogos.svelte';
  import Personal from './routes/Personal.svelte';

  let currentView = $state('dashboard');
  let params = $state<Record<string, unknown>>({});

  function navigate(view: string, p?: Record<string, unknown>) {
    currentView = view;
    params = p || {};
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
</script>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50">
  <Sidebar bind:currentView bind:params />

  <main class="lg:ml-72 min-h-screen">
    <div class="max-w-[1400px] mx-auto p-4 md:p-6 lg:p-8 pt-20 lg:pt-8">
      {#if currentView === 'dashboard'}
        <Dashboard onNavigate={navigate} />
      {:else if currentView === 'pacientes'}
        <Pacientes onNavigate={navigate} />
      {:else if currentView === 'ficha'}
        <FichaPaciente ind={params.ind as number} onNavigate={navigate} />
      {:else if currentView === 'agenda'}
        <Agenda onNavigate={navigate} />
      {:else if currentView === 'financiero'}
        <Financiero onNavigate={navigate} />
      {:else if currentView === 'catalogos'}
        <Catalogos />
      {:else if currentView === 'personal'}
        <Personal onNavigate={navigate} />
      {/if}
    </div>
  </main>
</div>

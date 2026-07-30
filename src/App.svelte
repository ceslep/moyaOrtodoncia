<script lang="ts">
  import { onMount } from 'svelte';
  import Sidebar from '$lib/components/Sidebar.svelte';
  import Toast from '$lib/components/Toast.svelte';
  import Login from './routes/Login.svelte';
  import Dashboard from './routes/Dashboard.svelte';
  import Pacientes from './routes/Pacientes.svelte';
  import FichaPaciente from './routes/FichaPaciente.svelte';
  import Agenda from './routes/Agenda.svelte';
  import Financiero from './routes/Financiero.svelte';
  import Catalogos from './routes/Catalogos.svelte';
  import Personal from './routes/Personal.svelte';
  import EstadisticasPacientes from './routes/EstadisticasPacientes.svelte';
  import { verifyToken, logoutUser, setAuthToken } from '$lib/api';

  let isAuthenticated = $state(false);
  let authChecked = $state(false);
  let user = $state<{ id: number; usuario: string } | null>(null);
  let authToken = $state('');

  let currentView = $state('dashboard');
  let params = $state<Record<string, unknown>>({});

  function navigate(view: string, p?: Record<string, unknown>) {
    currentView = view;
    params = p || {};
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function handleLogin(token: string, userData: { id: number; usuario: string }) {
    authToken = token;
    user = userData;
    isAuthenticated = true;
    setAuthToken(token);
    localStorage.setItem('auth_token', token);
    localStorage.setItem('auth_user', JSON.stringify(userData));
  }

  async function handleLogout() {
    try {
      if (authToken) await logoutUser(authToken);
    } catch { /* ignore */ }
    authToken = '';
    user = null;
    isAuthenticated = false;
    setAuthToken(null);
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    currentView = 'dashboard';
  }

  onMount(async () => {
    const savedToken = localStorage.getItem('auth_token');
    const savedUser = localStorage.getItem('auth_user');
    if (savedToken && savedUser) {
      try {
        const valid = await verifyToken(savedToken);
        if (valid) {
          authToken = savedToken;
          user = JSON.parse(savedUser);
          isAuthenticated = true;
          setAuthToken(savedToken);
        } else {
          localStorage.removeItem('auth_token');
          localStorage.removeItem('auth_user');
        }
      } catch {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
      }
    }
    authChecked = true;
  });
</script>

{#if !authChecked}
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50 flex items-center justify-center">
    <div class="flex flex-col items-center gap-3">
      <div class="w-8 h-8 border-2 border-primary-400 border-t-transparent rounded-full animate-spin"></div>
      <p class="text-sm text-slate-400 font-medium">Cargando...</p>
    </div>
  </div>
{:else if !isAuthenticated}
  <Login onLogin={handleLogin} />
{:else}
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50">
    <Sidebar bind:currentView bind:params {user} onLogout={handleLogout} />

    <main class="lg:ml-72 min-h-screen">
      <div class="max-w-[1400px] mx-auto p-4 md:p-6 lg:p-8 pt-20 lg:pt-8">
        {#if currentView === 'dashboard'}
          <Dashboard onNavigate={navigate} />
        {:else if currentView === 'pacientes'}
          <Pacientes onNavigate={navigate} />
        {:else if currentView === 'ficha'}
          <FichaPaciente ind={params.ind as number} onNavigate={navigate} />
        {:else if currentView === 'estadisticas'}
          <EstadisticasPacientes onNavigate={navigate} />
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
{/if}

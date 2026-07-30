<script lang="ts">
  import ThiingsIcon from '$lib/components/ThiingsIcon.svelte';
  import { loginUser } from '$lib/api';

  let { onLogin }: { onLogin: (token: string, user: { id: number; usuario: string }) => void } = $props();

  let usuario = $state('');
  let password = $state('');
  let loading = $state(false);
  let error = $state('');
  let shake = $state(false);
  let showPassword = $state(false);

  async function handleSubmit(e: Event) {
    e.preventDefault();
    if (!usuario.trim() || !password.trim()) {
      error = 'Completa todos los campos';
      shake = true;
      setTimeout(() => shake = false, 500);
      return;
    }

    loading = true;
    error = '';

    try {
      const data = await loginUser(usuario.trim(), password);
      onLogin(data.token, data.user);
    } catch (err: any) {
      error = err.message || 'Credenciales incorrectas';
      shake = true;
      setTimeout(() => shake = false, 500);
    } finally {
      loading = false;
    }
  }
</script>

<div class="login-root">
  <!-- Fondo animado -->
  <div class="login-bg">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="mesh"></div>
  </div>

  <!-- Card principal -->
  <div class="login-card" class:shake>
    <!-- Header branding -->
    <div class="login-header">
      <div class="logo-wrap">
        <ThiingsIcon name="tooth" size={40} alt="Moya Ortodoncia" />
      </div>
      <h1 class="brand-title">Moya Ortodoncia</h1>
      <p class="brand-sub">Sistema de Consultas</p>
    </div>

    <!-- Formulario -->
    <form class="login-form" onsubmit={handleSubmit}>
      <div class="field">
        <label class="field-label" for="usuario">Usuario</label>
        <div class="input-wrap">
          <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
          </svg>
          <input
            id="usuario"
            type="text"
            class="input-field"
            placeholder="Tu usuario"
            bind:value={usuario}
            autocomplete="username"
            disabled={loading}
          />
        </div>
      </div>

      <div class="field">
        <label class="field-label" for="password">Password</label>
        <div class="input-wrap">
          <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
          </svg>
          <input
            id="password"
            type={showPassword ? 'text' : 'password'}
            class="input-field"
            placeholder="Tu password"
            bind:value={password}
            autocomplete="current-password"
            disabled={loading}
          />
          <button
            type="button"
            class="toggle-pw"
            onclick={() => showPassword = !showPassword}
            tabindex="-1"
            aria-label={showPassword ? 'Ocultar password' : 'Mostrar password'}
          >
            {#if showPassword}
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" class="pw-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
              </svg>
            {:else}
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" class="pw-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              </svg>
            {/if}
          </button>
        </div>
      </div>

      {#if error}
        <div class="error-msg" role="alert">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" class="error-icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
          </svg>
          <span>{error}</span>
        </div>
      {/if}

      <button
        type="submit"
        class="submit-btn"
        disabled={loading}
      >
        {#if loading}
          <span class="spinner"></span>
          <span>Iniciando sesion...</span>
        {:else}
          <span>Iniciar Sesion</span>
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" class="btn-arrow">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
          </svg>
        {/if}
      </button>
    </form>

    <!-- Footer -->
    <div class="login-footer">
      <span class="dot-pulse"></span>
      <span>Conexion segura</span>
    </div>
  </div>
</div>

<style>
  .login-root {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
    overflow: hidden;
    padding: 1rem;
  }

  /* ── Fondo ── */
  .login-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 35%, #1e40af 65%, #0f172a 100%);
  }

  .mesh {
    position: absolute;
    inset: 0;
    background:
      radial-gradient(60rem 40rem at 15% 20%, rgba(59,130,246,0.25), transparent 55%),
      radial-gradient(50rem 36rem at 85% 80%, rgba(16,185,129,0.20), transparent 50%),
      radial-gradient(40rem 30rem at 50% 50%, rgba(139,92,246,0.12), transparent 50%);
  }

  .orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.5;
    animation: float 20s ease-in-out infinite;
  }
  .orb-1 {
    width: 400px; height: 400px;
    background: rgba(59,130,246,0.35);
    top: -10%; left: -5%;
    animation-delay: 0s;
  }
  .orb-2 {
    width: 350px; height: 350px;
    background: rgba(16,185,129,0.30);
    bottom: -8%; right: -5%;
    animation-delay: -7s;
  }
  .orb-3 {
    width: 280px; height: 280px;
    background: rgba(139,92,246,0.25);
    top: 50%; left: 60%;
    animation-delay: -14s;
  }

  @keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -20px) scale(1.05); }
    66% { transform: translate(-20px, 15px) scale(0.95); }
  }

  /* ── Card ── */
  .login-card {
    position: relative;
    width: 100%;
    max-width: 420px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 2rem;
    padding: 2.5rem 2rem 2rem;
    box-shadow:
      0 32px 64px -16px rgba(0,0,0,0.5),
      0 0 0 1px rgba(255,255,255,0.05) inset;
    animation: card-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  @keyframes card-in {
    from { opacity: 0; transform: translateY(24px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
  }

  .login-card.shake {
    animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97);
  }

  @keyframes shake {
    10%, 90% { transform: translateX(-1px); }
    20%, 80% { transform: translateX(2px); }
    30%, 50%, 70% { transform: translateX(-4px); }
    40%, 60% { transform: translateX(4px); }
  }

  /* ── Header ── */
  .login-header {
    text-align: center;
    margin-bottom: 2rem;
  }

  .logo-wrap {
    width: 72px; height: 72px;
    margin: 0 auto 1rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 32px -8px rgba(0,0,0,0.3);
  }

  .brand-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.02em;
    margin: 0;
    line-height: 1.2;
  }

  .brand-sub {
    font-size: 0.875rem;
    color: rgba(255,255,255,0.55);
    margin: 0.35rem 0 0;
    font-weight: 500;
  }

  /* ── Form ── */
  .login-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
  }

  .field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
  }

  .field-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: rgba(255,255,255,0.6);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding-left: 0.25rem;
  }

  .input-wrap {
    position: relative;
    display: flex;
    align-items: center;
  }

  .input-icon {
    position: absolute;
    left: 1rem;
    width: 18px; height: 18px;
    color: rgba(255,255,255,0.35);
    pointer-events: none;
    transition: color 0.2s;
  }

  .input-field {
    width: 100%;
    padding: 0.85rem 1rem 0.85rem 2.75rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 0.875rem;
    color: #fff;
    font-size: 0.95rem;
    font-family: inherit;
    outline: none;
    transition: all 0.2s;
  }

  .input-field::placeholder {
    color: rgba(255,255,255,0.3);
  }

  .input-field:focus {
    background: rgba(255,255,255,0.1);
    border-color: rgba(96,165,250,0.5);
    box-shadow: 0 0 0 3px rgba(96,165,250,0.15);
  }

  .input-wrap:hover .input-icon {
    color: rgba(255,255,255,0.6);
  }

  .toggle-pw {
    position: absolute;
    right: 0.75rem;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.25rem;
    display: flex;
    color: rgba(255,255,255,0.35);
    transition: color 0.2s;
  }
  .toggle-pw:hover { color: rgba(255,255,255,0.7); }
  .pw-icon { width: 18px; height: 18px; }

  /* ── Error ── */
  .error-msg {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(239,68,68,0.12);
    border: 1px solid rgba(239,68,68,0.25);
    border-radius: 0.75rem;
    color: #fca5a5;
    font-size: 0.85rem;
    font-weight: 500;
    animation: fade-in 0.3s ease-out;
  }
  .error-icon { width: 18px; height: 18px; flex-shrink: 0; }

  @keyframes fade-in {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* ── Button ── */
  .submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.9rem 1.5rem;
    margin-top: 0.25rem;
    background: linear-gradient(135deg, #2563eb 0%, #059669 100%);
    border: none;
    border-radius: 0.875rem;
    color: #fff;
    font-size: 0.95rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 8px 24px -6px rgba(37,99,235,0.45);
    position: relative;
    overflow: hidden;
  }

  .submit-btn::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 50%);
    opacity: 0;
    transition: opacity 0.25s;
  }

  .submit-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 12px 32px -6px rgba(37,99,235,0.55);
  }
  .submit-btn:hover:not(:disabled)::before { opacity: 1; }

  .submit-btn:active:not(:disabled) {
    transform: translateY(0) scale(0.98);
  }

  .submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .btn-arrow {
    width: 18px; height: 18px;
    transition: transform 0.2s;
  }
  .submit-btn:hover:not(:disabled) .btn-arrow {
    transform: translateX(3px);
  }

  .spinner {
    width: 18px; height: 18px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* ── Footer ── */
  .login-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.75rem;
    padding-top: 1.25rem;
    border-top: 1px solid rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.35);
    font-size: 0.75rem;
    font-weight: 500;
  }

  .dot-pulse {
    width: 6px; height: 6px;
    background: #34d399;
    border-radius: 50%;
    animation: pulse-dot 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }

  @keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.5); }
  }

  /* ── Responsive ── */
  @media (max-width: 480px) {
    .login-card {
      padding: 2rem 1.5rem 1.5rem;
      border-radius: 1.5rem;
    }
    .brand-title { font-size: 1.3rem; }
    .orb-1 { width: 250px; height: 250px; }
    .orb-2 { width: 200px; height: 200px; }
    .orb-3 { display: none; }
  }

  @media (prefers-reduced-motion: reduce) {
    .orb { animation: none; }
    .login-card { animation: none; }
    .login-card.shake { animation: none; }
    .spinner { animation-duration: 1s; }
  }
</style>

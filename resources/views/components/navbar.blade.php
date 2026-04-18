{{-- resources/views/components/navbar.blade.php --}}

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 90px;
    height: 100vh;
    background: #dfe5da;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 20px;
    gap: 14px;
    border-radius: 0 20px 20px 0;
    box-shadow: inset -2px 0 5px rgba(0,0,0,0.05);
    z-index: 999;
}

.sidebar .logo {
    width: 60px; /* Ukuran container sedikit lebih besar untuk logo png */
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}

/* 🔥 Style Khusus Logo Baru */
.logo-img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.logo-img:hover {
    transform: scale(1.15) rotate(5deg);
}

.sidebar .menu {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f4f4f4;
    box-shadow: inset 0 2px 2px rgba(255,255,255,0.7),
                0 3px 6px rgba(0,0,0,0.15);
    cursor: pointer;
    transition: 0.2s;
    text-decoration: none;
}

.sidebar .menu:hover {
    transform: scale(1.08);
}

.sidebar .menu.active {
    background: #2f2f2f;
}

.sidebar .menu svg {
    width: 22px;
    height: 22px;
    stroke: #666;
    fill: none;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.sidebar .menu.active svg {
    stroke: #ffffff;
}

.sidebar .sidebar-spacer {
    flex: 1;
}

.sidebar .bottom-nav {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    padding-bottom: 24px;
}

.sidebar .logout-form {
    margin: 0;
    padding: 0;
}

.sidebar .logout-form button {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f4f4f4;
    box-shadow: inset 0 2px 2px rgba(255,255,255,0.7),
                0 3px 6px rgba(0,0,0,0.15);
    cursor: pointer;
    transition: 0.2s;
    border: none;
}

.sidebar .logout-form button:hover {
    transform: scale(1.08);
}

.sidebar .logout-form button svg {
    width: 22px;
    height: 22px;
    stroke: #666;
    fill: none;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}
</style>

<div class="sidebar">

    {{-- 🔥 Logo (Sudah Diubah ke PNG) --}}
    <div class="logo">
        <a href="/dashboard">
            <img src="{{ asset('images/Logo.png') }}" alt="Logo SehatYuk" class="logo-img">
        </a>
    </div>

    {{-- Dashboard --}}
    <a href="/dashboard" class="menu {{ request()->is('dashboard') ? 'active' : '' }}" title="Dashboard">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
    </a>

    {{-- Katalog Makanan --}}
    <a href="/katalog" class="menu {{ request()->is('katalog*') ? 'active' : '' }}" title="Katalog Makanan">
        <svg viewBox="0 0 24 24"><path d="M3 2v7c0 1.1.9 2 2 2h2a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M17 2a5 5 0 0 1 0 10v10"/></svg>
    </a>

    {{-- Aktivitas --}}
    <a href="/aktivitas" class="menu {{ request()->is('aktivitas*') ? 'active' : '' }}" title="Log Aktivitas">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2.5 2.5"/><path d="M9 3h6"/><path d="M12 3v2"/></svg>
    </a>

    {{-- Edukasi / Artikel --}}
    <a href="/edukasi" class="menu {{ request()->is('edukasi*') ? 'active' : '' }}" title="Edukasi">
        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="13" y2="17"/></svg>
    </a>

    {{-- Riwayat --}}
    <a href="/riwayat" class="menu {{ request()->is('riwayat*') ? 'active' : '' }}" title="Riwayat & Laporan">
        <svg viewBox="0 0 24 24"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
    </a>

    {{-- Kalkulator Gizi --}}
    <a href="/gizi" class="menu {{ request()->is('gizi*') ? 'active' : '' }}" title="Kalkulator Gizi">
        <svg viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="10" y2="10"/><line x1="14" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="10" y2="14"/><line x1="14" y1="14" x2="16" y2="14"/><line x1="8" y1="18" x2="10" y2="18"/><line x1="14" y1="18" x2="16" y2="18"/></svg>
    </a>

    {{-- Profil --}}
    <a href="/profile" class="menu {{ request()->is('profile*') ? 'active' : '' }} " title="Profil">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
    </a>

    {{-- ADMIN MENU (HANYA ADMIN) --}}
    @auth
        @if(auth()->user()->role === 'admin')
        <a href="/admin" class="menu {{ request()->is('admin*') ? 'active' : '' }}" title="Admin Panel">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        </a>
        @endif
    @endauth

    <div class="sidebar-spacer"></div>

    <div class="bottom-nav">

        {{-- Logout --}}
        <form class="logout-form" method="POST" action="/logout">
            @csrf
            <button type="submit" title="Logout">
                <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </button>
        </form>

        {{-- Help --}}
        <a href="#" class="menu" title="Bantuan">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17" stroke-width="2.5"/></svg>
        </a>

    </div>

</div>
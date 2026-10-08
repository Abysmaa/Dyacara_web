
<!--- Font Awesome CDN --->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!--- Navbar --->
<nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-logo">
            <span class="brand-text ms-2">Dyacara</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars text-white"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-home me-1"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('events*') ? 'active' : '' }}" href="{{ route('events.index') }}">
                        <i class="fas fa-calendar-alt me-2"></i> Event
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('services') ? 'active' : '' }}" href="{{ route('services.index') }}">
                        <i class="fas fa-concierge-bell me-1"></i> Layanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('gallery') ? 'active' : '' }}" href="{{ route('gallery.index') }}">
                        <i class="fas fa-images me-1"></i> Galeri
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('contact') ? 'active' : '' }}" href="{{ route('contact.index') }}">
                        <i class="fas fa-envelope me-1"></i> Kontak
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @if(Auth::user()->is_admin)
                                <li>
                                    <a class="dropdown-item" href="{{ route('filament.admin.pages.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="fas fa-user-circle me-2"></i>Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('bookings.index') }}">
                                    <i class="fas fa-calendar-check me-2"></i>Pemesanan Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('payments') }}">
                                    <i class="fas fa-receipt me-2"></i>Pembayaran Saya
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<style>
#mainNav {
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.95), rgba(0, 0, 0, 0.85));
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    padding: 0.875rem 0;
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.15);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1030;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.navbar-brand {
    padding: 0.25rem;
    display: flex;
    align-items: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.navbar-brand:hover {
    transform: translateY(-2px);
}

.brand-logo {
    height: 42px;
    width: 42px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.brand-logo:hover {
    transform: scale(1.05);
    border-color: rgba(255, 255, 255, 0.3);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.brand-text {
    color: #ffffff;
    font-weight: 700;
    font-size: 1.4rem;
    margin-left: 0.875rem;
    letter-spacing: 0.5px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.navbar-toggler {
    padding: 0.625rem;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.navbar-toggler:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: scale(1.05);
}

.navbar-toggler:focus {
    box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.15);
}

.navbar-nav .nav-link {
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 500;
    font-size: 1rem;
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin: 0 0.25rem;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.navbar-nav .nav-link i {
    font-size: 1rem;
    transition: transform 0.3s ease;
}

.navbar-nav .nav-link:hover i {
    transform: translateY(-1px) scale(1.1);
}

.navbar-nav .nav-link.active i {
    color: #ffffff;
}

.navbar-nav .nav-link::after {
    content: '';
    position: absolute;
    bottom: 6px;
    left: 50%;
    width: 0;
    height: 2px;
    background: #ffffff;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateX(-50%);
}

.navbar-nav .nav-link:hover::after {
    width: 60%;
}

.navbar-nav .nav-link:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-1px);
}

.navbar-nav .nav-link.active {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.navbar-nav .nav-link.active::after {
    width: 60%;
}

.navbar-nav .nav-link i {
    width: 18px;
    text-align: center;
    margin-right: 8px;
    font-size: 1rem;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.navbar-nav .nav-link:hover i {
    transform: translateY(-1px);
}

.dropdown-menu {
    margin-top: 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: none;
    border-radius: 14px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    padding: 0.75rem;
    animation: dropdownFade 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dropdown-item {
    padding: 0.75rem 1rem;
    color: #1a1a1a;
    font-weight: 500;
    border-radius: 10px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    margin: 0.125rem 0;
    font-size: 0.95rem;
}

.dropdown-item i {
    width: 20px;
    text-align: center;
    margin-right: 10px;
    font-size: 1rem;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dropdown-item:hover,
.dropdown-item:focus {
    background-color: #f8f9fa;
    color: #000000;
    transform: translateX(5px);
}

.dropdown-item:hover i {
    transform: scale(1.1);
}

@media (max-width: 991.98px) {
    #mainNav {
        padding: 0.75rem 0;
    }

    .navbar-collapse {
        background: rgba(0, 0, 0, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        margin-top: 1rem;
        padding: 1.25rem;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .navbar-nav .nav-link {
        padding: 0.875rem 1.25rem;
        margin: 0.25rem 0;
        font-size: 1rem;
    }

    .dropdown-menu {
        background: rgba(255, 255, 255, 0.05);
        margin: 0.5rem 0;
        padding: 0.625rem;
    }

    .dropdown-item {
        color: rgba(255, 255, 255, 0.9);
        padding: 0.875rem 1rem;
    }
}
</style>
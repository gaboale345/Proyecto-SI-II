<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema Digital de Citas y Turnos - Hospital Plan 3000')</title>
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #0f4c81;
            --primary-dark: #093156;
            --primary-light: #eef5fc;
            --accent: #00b4d8;
            --success: #2ec4b6;
            --warning: #ff9f1c;
            --danger: #e71d36;
            --dark: #1d2d44;
            --light: #f8fafc;
            --card-shadow: 0 10px 25px -5px rgba(15, 76, 129, 0.08), 0 8px 10px -6px rgba(15, 76, 129, 0.04);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Header Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: white;
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .navbar-brand i {
            font-size: 1.75rem;
            color: var(--accent);
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            list-style: none;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            padding: 0.5rem 0.85rem;
            border-radius: 6px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .badge-role {
            font-size: 0.75rem;
            padding: 0.25rem 0.6rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-admin { background-color: #ef4444; color: white; }
        .badge-medico { background-color: #10b981; color: white; }
        .badge-ventanilla { background-color: #f59e0b; color: white; }
        .badge-paciente { background-color: #3b82f6; color: white; }

        /* Container & Grid */
        .container {
            max-width: 1240px;
            width: 100%;
            margin: 1.75rem auto;
            padding: 0 1.25rem;
            flex: 1;
        }

        /* Glassmorphism Card */
        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 1.25rem;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Alert Banners */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
        }
        .alert-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        /* Custom Table */
        .table-responsive {
            overflow-x: auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.92rem;
        }
        .table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #e2e8f0;
        }
        .table td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-primary:hover { background-color: var(--primary-dark); }
        .btn-accent { background-color: var(--accent); color: white; }
        .btn-accent:hover { background-color: #0096c7; }
        .btn-danger { background-color: var(--danger); color: white; }
        .btn-danger:hover { background-color: #c1121f; }
        .btn-secondary { background-color: #e2e8f0; color: #334155; }
        .btn-secondary:hover { background-color: #cbd5e1; }
        .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.82rem; }

        /* Form Inputs */
        .form-group {
            margin-bottom: 1.1rem;
        }
        .form-label {
            display: block;
            font-weight: 600;
            font-size: 0.88rem;
            color: #475569;
            margin-bottom: 0.35rem;
        }
        .form-control, .form-select {
            width: 100%;
            padding: 0.65rem 0.85rem;
            font-size: 0.92rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.2s;
            font-family: inherit;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 76, 129, 0.1);
        }

        /* Status Badge */
        .status-badge {
            padding: 0.25rem 0.6rem;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            display: inline-block;
        }
        .status-SOLICITADA { background: #e0f2fe; color: #0369a1; }
        .status-CONFIRMADA { background: #dcfce7; color: #15803d; }
        .status-ATENDIDA { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .status-CANCELADA { background: #fee2e2; color: #b91c1c; }
        .status-REPROGRAMADA { background: #fef3c7; color: #b45309; }
        .status-BLOQUEADO { background: #ffedd5; color: #c2410c; }
        .status-COMPLETO { background: #f3e8ff; color: #6b21a8; }
        .status-DISPONIBLE { background: #e0f2fe; color: #0369a1; }

        /* Footer */
        footer {
            background-color: var(--primary-dark);
            color: #94a3b8;
            padding: 1.5rem 2rem;
            text-align: center;
            font-size: 0.88rem;
            margin-top: auto;
        }
        footer a { color: var(--accent); text-decoration: none; }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            <i class="fa-solid fa-hospital-user"></i>
            <span>Hospital Plan 3000</span>
        </a>

        @auth
        <ul class="navbar-nav">
            @if(Auth::user()->isPaciente())
                <li><a href="{{ route('paciente.dashboard') }}" class="nav-link"><i class="fa-solid fa-gauge"></i> Inicio</a></li>
                <li><a href="{{ route('paciente.citas.solicitar') }}" class="nav-link"><i class="fa-solid fa-calendar-plus"></i> Solicitar Cita</a></li>
                <li><a href="{{ route('paciente.citas.historial') }}" class="nav-link"><i class="fa-solid fa-clock-rotate-left"></i> Mis Citas</a></li>
            @elseif(Auth::user()->isMedico())
                <li><a href="{{ route('medico.agenda') }}" class="nav-link"><i class="fa-solid fa-user-doctor"></i> Mi Agenda Médica</a></li>
            @elseif(Auth::user()->isCallCenter())
                <li><a href="{{ route('ventanilla.dashboard') }}" class="nav-link"><i class="fa-solid fa-desktop"></i> Ventanilla / Call Center</a></li>
                <li><a href="{{ route('paciente.citas.solicitar') }}" class="nav-link"><i class="fa-solid fa-calendar-check"></i> Asignar Cita</a></li>
                <li><a href="{{ route('agendas.index') }}" class="nav-link"><i class="fa-solid fa-calendar-days"></i> Gestión Agendas</a></li>
                <li><a href="{{ route('contingencia.index') }}" class="nav-link"><i class="fa-solid fa-triangle-exclamation"></i> Contingencias</a></li>
            @elseif(Auth::user()->isAdmin())
                <li><a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="fa-solid fa-chart-line"></i> Dashboard Admin</a></li>
                <li><a href="{{ route('admin.usuarios') }}" class="nav-link"><i class="fa-solid fa-users-gear"></i> Usuarios y Roles</a></li>
                <li><a href="{{ route('agendas.index') }}" class="nav-link"><i class="fa-solid fa-calendar-days"></i> Agendas</a></li>
                <li><a href="{{ route('contingencia.index') }}" class="nav-link"><i class="fa-solid fa-bell-concierge"></i> Contingencias</a></li>
                <li><a href="{{ route('admin.auditoria') }}" class="nav-link"><i class="fa-solid fa-shield-halved"></i> Auditoría</a></li>
                <li><a href="{{ route('admin.reportes') }}" class="nav-link"><i class="fa-solid fa-file-chart-column"></i> Reportes</a></li>
            @endif

            <li style="display: flex; align-items: center; gap: 0.75rem; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 1rem;">
                <span class="badge-role badge-{{ strtolower(Auth::user()->role->nombre_rol) }}">
                    {{ Auth::user()->role->nombre_rol }}
                </span>
                <span style="font-weight: 500;">{{ Auth::user()->nombre }}</span>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary" title="Cerrar Sesión">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </li>
        </ul>
        @else
        <ul class="navbar-nav">
            <li><a href="{{ route('login') }}" class="nav-link"><i class="fa-solid fa-key"></i> Iniciar Sesión</a></li>
            <li><a href="{{ route('register') }}" class="btn btn-accent btn-sm"><i class="fa-solid fa-user-plus"></i> Registrarse</a></li>
        </ul>
        @endauth
    </nav>

    <!-- Main Content Area -->
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <ul style="margin-left: 1.25rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer>
        <p><strong>Hospital Municipal Plan 3000</strong> — Sistema Digital de Gestión de Citas y Turnos</p>
        <p style="margin-top: 0.35rem; font-size: 0.8rem;">
            Distrito Municipal 8, Santa Cruz de la Sierra, Bolivia | Call Center: <strong>3494008</strong> |
            <span style="color: #38bdf8;"><i class="fa-solid fa-link"></i> Interoperabilidad SUIS Ministerio de Salud: <strong>ACTIVA</strong></span>
        </p>
    </footer>

    @yield('scripts')
</body>
</html>

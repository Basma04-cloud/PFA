<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        purple: {
                            400: '#9d6fce',
                            800: '#5d2a81'
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Additional Styles -->
    <style>
        /* Couleurs exactes du design Figma */
        .bg-purple-400 {
            background-color: #9d6fce;
        }
        .bg-purple-800 {
            background-color: #5d2a81;
        }
        .border-purple-400 {
            border-color: #9d6fce;
        }
        
        /* Styles supplémentaires */
        .bg-gray-300 {
            background-color: #d9d9d9;
        }

        /* Styles pour les boutons et liens */
        .btn-primary {
            background-color: #5d2a81;
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #4a256b;
        }

        .btn-secondary {
            background-color: #9d6fce;
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn-secondary:hover {
            background-color: #8a5bb8;
        }

        /* Styles pour les formulaires */
        .form-input {
            width: 100%;
            padding: 12px 20px;
            margin: 10px 0;
            border: 2px solid #d6b3f4;
            border-radius: 25px;
            background-color: #fff;
            outline: none;
            transition: border-color 0.3s;
        }

        .form-input:focus {
            border-color: #9d6fce;
        }

        /* Styles pour les alertes */
        .alert {
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 8px;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }

        /* Styles pour les tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #9d6fce;
        }

        .table th {
            background-color: #f8f4fc;
            font-weight: 600;
            color: #5d2a81;
        }

        /* Styles pour les cartes */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background-color: #f8f4fc;
            padding: 16px 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .card-body {
            padding: 20px;
        }

        .card-footer {
            background-color: #f8f4fc;
            padding: 16px 20px;
            border-top: 1px solid #e9ecef;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>

    <!-- Scripts additionnels -->
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen">
        @yield('content')
    </div>

    <!-- Scripts JavaScript -->
    <script>
        // Fonction pour afficher/masquer les alertes automatiquement
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }, 5000); // Masquer après 5 secondes
            });
        });

        // Fonction pour confirmer les suppressions
        function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
            return confirm(message);
        }

        // Fonction pour formater les nombres
        function formatNumber(number, decimals = 2) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(number);
        }

        // Fonction pour formater les montants en euros
        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        }
    </script>

    @stack('scripts')
    @if(Auth::check() && Auth::user()->is_admin)
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    @endif

</body>
</html>
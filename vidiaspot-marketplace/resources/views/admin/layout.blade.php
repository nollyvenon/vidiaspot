<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Admin CSS -->
    <style>
        :root {
            --admin-sidebar-width: 250px;
            --admin-header-height: 60px;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: var(--admin-sidebar-width);
            background: #1e293b;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            transition: all 0.3s;
        }
        
        .admin-header {
            height: var(--admin-header-height);
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: var(--admin-sidebar-width);
            right: 0;
            z-index: 90;
            display: flex;
            align-items: center;
            padding: 0 20px;
            transition: all 0.3s;
        }
        
        .admin-content {
            flex: 1;
            margin-top: var(--admin-header-height);
            margin-left: var(--admin-sidebar-width);
            padding: 20px;
            transition: all 0.3s;
        }
        
        .admin-sidebar-menu {
            padding: 20px 0;
        }
        
        .admin-sidebar-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .admin-sidebar-menu li {
            padding: 10px 20px;
            border-left: 3px solid transparent;
        }
        
        .admin-sidebar-menu li a {
            color: #cbd5e1;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .admin-sidebar-menu li:hover {
            background: #334155;
        }
        
        .admin-sidebar-menu li.active {
            border-left-color: #3b82f6;
            background: #334155;
        }
        
        .admin-sidebar-menu li.active a {
            color: white;
        }
        
        .admin-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th,
        .admin-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .admin-table th {
            background-color: #f1f5f9;
            font-weight: 600;
        }
        
        .admin-table tr:hover {
            background-color: #f8fafc;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
        }
        
        .status-pending { background-color: #fef3c7; color: #d97706; }
        .status-completed { background-color: #d1fae5; color: #059669; }
        .status-failed { background-color: #fee2e2; color: #dc2626; }
        .status-refunded { background-color: #e0e7ff; color: #4f46e5; }
        .status-approved { background-color: #d1fae5; color: #059669; }
        .status-pending { background-color: #fef3c7; color: #d97706; }
        .status-rejected { background-color: #fee2e2; color: #dc2626; }
        
        .admin-btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .admin-btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        
        .admin-btn-success {
            background-color: #10b981;
            color: white;
        }
        
        .admin-btn-danger {
            background-color: #ef4444;
            color: white;
        }
        
        .admin-btn-sm {
            padding: 4px 8px;
            font-size: 0.75rem;
        }
        
        .admin-form-group {
            margin-bottom: 1rem;
        }
        
        .admin-form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .admin-form-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        
        .admin-form-select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            background-color: white;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="p-4">
                <h2 class="text-xl font-bold">Admin Dashboard</h2>
            </div>
            <nav class="admin-sidebar-menu">
                <ul>
                    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="mr-2">📊</i> Dashboard
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}">
                            <i class="mr-2">👥</i> Users
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.vendors.index') }}">
                            <i class="mr-2">🏢</i> Vendors
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.payments.index') }}">
                            <i class="mr-2">💳</i> Payments
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.subscriptions.index') }}">
                            <i class="mr-2">📋</i> Subscriptions
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.ads.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.ads.index') }}">
                            <i class="mr-2">📢</i> Ads
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.categories.index') }}">
                            <i class="mr-2">🏷️</i> Categories
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.blogs.index') }}">
                            <i class="mr-2">📝</i> Blogs
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.how-it-works.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.how-it-works.index') }}">
                            <i class="mr-2">⚙️</i> How It Works
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.settings.index') }}">
                            <i class="mr-2">⚙️</i> Settings
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Header -->
        <header class="admin-header">
            <div class="flex items-center justify-between w-full">
                <div>
                    <h1 class="text-xl font-semibold">@yield('title', 'Admin Dashboard')</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button id="notification-toggle" class="p-2 rounded-full hover:bg-gray-100">
                            <i>🔔</i>
                        </button>
                    </div>
                    
                    <div class="relative">
                        <button id="user-menu-toggle" class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-sm font-medium">{{ substr(auth()->user()->name ?? 'Admin', 0, 1) }}</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="admin-content">
            @if (session('status'))
                <div class="admin-card bg-green-50 text-green-800 p-4 mb-4 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Add JavaScript for admin panel if needed
    </script>
</body>
</html>
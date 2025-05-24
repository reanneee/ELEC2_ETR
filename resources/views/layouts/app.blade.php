<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - @yield('title', 'App')</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        .sidebar {
            height: 100vh;
            width: 220px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #212529; /* Bootstrap dark bg */
            display: flex;
            flex-direction: column;
            padding: 1rem;
        }
        .sidebar .nav-link, .sidebar .navbar-brand, .sidebar form button {
            color: white;
            margin-bottom: 0.5rem;
        }
        .sidebar .nav-link:hover {
            background-color: #343a40;
            color: white;
        }
        .sidebar .logout-form {
            margin-top: auto; /* Push logout to bottom */
        }
        .content {
            margin-left: 220px; /* Make room for sidebar */
            padding: 1rem;
        }
    </style>
</head>
<body>
    <nav class="sidebar">
        <a class="navbar-brand mb-4" href="{{ route('dashboard') }}">Property Stock Card</a>

        <a class="nav-link btn btn-dark text-start mb-2" href="{{ route('entities.index') }}">Entities</a>
        <a class="nav-link btn btn-dark text-start mb-2" href="{{ route('branches.index') }}">Branches</a>
        <a class="nav-link btn btn-dark text-start mb-2" href="{{ route('fund_clusters.index') }}">Fund Clusters</a>
        <a class="nav-link btn btn-dark text-start mb-2" href="{{ route('received_equipment.index') }}">Received Equipment</a>
        <a class="nav-link btn btn-dark text-start mb-2" href="{{ route('property_cards.index') }}">Property Cards</a>
        <a class="nav-link btn btn-dark text-start mb-2" href="{{ route('equipment-list.index') }}">Equipments List</a>

        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button class="btn btn-outline-light w-100">Logout</button>
        </form>
    </nav>

    <div class="content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>

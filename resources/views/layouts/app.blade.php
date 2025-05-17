<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - @yield('title', 'App')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CDN for quick setup (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">My App</a>
            <div>
                <a class="btn btn-light" href="{{ route('entities.index') }}">Entities</a>
                <a class="btn btn-light" href="{{ route('branches.index') }}">Branches</a>
                <a class="btn btn-light" href="{{ route('fund_clusters.index') }}">Fund Clusters</a>
                
                <a class="btn btn-light" href="{{ route('received_equipment.index') }}">Received Equipment</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-light ms-2">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <!-- Optional Bootstrap JS (for dropdowns, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


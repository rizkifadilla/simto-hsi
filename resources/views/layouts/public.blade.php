<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f9fafb;
            font-size: 14px;
        }

        .site-header {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
        }

        .navbar-brand span {
            color: #1d4ed8;
        }

        .job-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            transition: .2s;
        }

        .job-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,.07);
        }

        .badge-active {
            background: #dcfce7;
            color: #15803d;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .badge-inactive {
            background: #eee;
            color: #777;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .site-footer {
            background: #fff;
            border-top: 1px solid #e5e7eb;
            padding: 20px 0;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- HEADER -->
    <header class="site-header">
        <nav class="navbar navbar-expand-lg py-2">
            <div class="container-lg">
                <a class="navbar-brand fw-bold" href="{{ route('career.index') }}">
                    HSI<span>Career</span>
                </a>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#nav"
                >
                    <i class="bi bi-list"></i>
                </button>

                <div class="collapse navbar-collapse" id="nav">
                    <ul class="navbar-nav ms-4 me-auto">
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                href="{{ route('public.index') }}"
                            >
                                Vacancy
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                href="{{ route('public.tracking') }}"
                            >
                                Track Application
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- CONTENT -->
    <main class="flex-grow-1">
        <div class="container-lg py-4">
            @yield('content')
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container-lg d-flex justify-content-between align-items-center">

            <div>
                <strong>
                    HSI<span style="color:#1d4ed8;">Career</span>
                </strong>
                <br>
                <small class="text-muted">
                    © {{ date('Y') }} Career Portal
                </small>
            </div>

        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
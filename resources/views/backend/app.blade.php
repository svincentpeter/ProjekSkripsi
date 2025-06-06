<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Koperasi | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ url('assets/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ url('assets/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ url('assets/css/style.css') }}" rel="stylesheet">

    <!-- Cropper.js CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">

    @yield('head')
</head>

<body>
    <div class="container-fluid p-0 d-flex">
        <!-- Spinner -->
        <div id="spinner" class="show position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <!-- Sidebar -->
        @include('backend._partials.sidebar')

        <!-- Main Content -->
        <div class="content">
            <!-- Header / Navbar -->
            @include('backend._partials.header')

            <!-- Page Content -->
            <main class="pt-4">
                @yield('content')
            </main>

            <!-- Footer -->
            @include('backend._partials.footer')
        </div>

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top">
            <i class="bi bi-arrow-up"></i>
        </a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('assets/lib/chart/chart.min.js') }}"></script>
    <script src="{{ url('assets/lib/easing/easing.min.js') }}"></script>
    <script src="{{ url('assets/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ url('assets/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ url('assets/lib/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ url('assets/lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ url('assets/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ url('assets/js/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    @yield('scripts')
</body>

</html>

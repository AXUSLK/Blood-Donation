<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @hasSection('head')
        @yield('head')
    @else
        <title>Blood Bank LTE</title>
    @endif

    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    @include('backend.layouts.css')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('backend.layouts.top-nav')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('backend.layouts.side-nav.admin-nav')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        @include('backend.layouts.sidebar')
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        @include('backend.layouts.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    @include('backend.layouts.js')
</body>

</html>

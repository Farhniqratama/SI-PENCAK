<!DOCTYPE html>
<html lang="id-ID">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{!! $title ?? 'SIPENCAK' !!} — LLDIKTI III</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    @vite(['resources/scss/icons.scss'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    @yield('css')

</head>
<body>
<div id="app-container">

    <!-- ▒▒ SIDEBAR ▒▒ -->
    @include('layouts/sidebar')
    
    <!-- Sidebar mobile overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ▒▒ MAIN CONTENT WRAPPER ▒▒ -->
    <div id="main-content-wrapper">

        <!-- ▒▒ TOP NAVBAR / HEADER ▒▒ -->
        @include('layouts/navbar')

        <!-- ▒▒ CONTENT BODY ▒▒ -->
        <main class="content-body">
            @yield('content')
        </main>

        <!-- ▒▒ FOOTER ▒▒ -->
        <footer class="site-footer">
            <div class="footer-inner">
                <span class="footer-copy">&copy; {!! date('Y') !!} Lembaga Layanan Pendidikan Tinggi Wilayah III &mdash; Hak Cipta Dilindungi.</span>
                <div class="footer-right">
                    <span class="ver-tag">v2.6.1</span>
                </div>
            </div>
        </footer>

    </div><!-- #main-content-wrapper -->

</div><!-- #app-container -->

<script src="{!! url('assets/vendor/jquery/jquery.min.js'); !!}"></script>
<script src="{!! url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); !!}"></script>

<script>
$(function(){
    /* Sidebar Toggles (Mobile and General) */
    var sidebar = $('#sidebar');
    var overlay = $('#sidebarOverlay');
    var toggleBtn = $('#sidebarToggle');

    function toggleSidebar() {
        sidebar.toggleClass('show');
        overlay.toggleClass('show');
        if (sidebar.hasClass('show')) {
            $('body').css('overflow', 'hidden');
        } else {
            $('body').css('overflow', '');
        }
    }

    if (toggleBtn.length) {
        toggleBtn.on('click', function (e) {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (overlay.length) {
        overlay.on('click', toggleSidebar);
    }
});
</script>
@yield('js')
</body>
</html>

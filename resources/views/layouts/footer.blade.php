<!-- Footer Start -->
<footer class="footer sipencak-footer-fixed">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="fw-bold fs-16 text-primary" style="letter-spacing: 1px;">SIPENCAK</div>
                <div class="text-muted d-none d-sm-block fs-13">
                    LLDIKTI Wilayah III Jakarta &copy; {{ date('Y') }} KIP Kuliah
                </div>
            </div>

            <div class="d-flex gap-3 fs-13 fw-medium">
                <a href="{{ url(session('role') === 'operator' ? 'dashboard' : 'home') }}" class="text-muted text-decoration-none hover-primary transition-all">Dashboard</a>
                <a href="https://lldikti3.kemdikbud.go.id/" target="_blank" rel="noopener noreferrer" class="text-muted text-decoration-none hover-primary transition-all">LLDIKTI III</a>
                <a href="https://kip-kuliah.kemdikbud.go.id/" target="_blank" rel="noopener noreferrer" class="text-muted text-decoration-none hover-primary transition-all">KIP Kuliah</a>
            </div>
        </div>
    </div>
</footer>
<!-- end Footer -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Feather -->
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- AdminKit default -->
<script src="{{ asset('template-admin/js/app.js') }}"></script>

<!-- Bootstrap -->
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const themeBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');
        
        // Cek localStorage
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
            if(themeIcon) {
                themeIcon.setAttribute('data-feather', 'sun');
                if (typeof feather !== 'undefined') feather.replace();
            }
        }

        if(themeBtn) {
            themeBtn.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                
                if (document.body.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                    themeIcon.setAttribute('data-feather', 'sun');
                } else {
                    localStorage.setItem('theme', 'light');
                    themeIcon.setAttribute('data-feather', 'moon');
                }
                if (typeof feather !== 'undefined') feather.replace();
            });
        }
    });
</script>

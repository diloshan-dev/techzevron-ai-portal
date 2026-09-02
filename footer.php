</div>
    <!-- Main Content End -->

    <!-- Developed By Section -->
    <div class="developed-by">
        <p>Developed by <a href="#">P. Diloshan</a></p>
    </div>

    <footer class="footer">
        <p>&copy; 2026 TechZevron. Built with ❤️ by P. Diloshan</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        // Theme System
        function applyTheme(themeName) {
            document.body.setAttribute('data-theme', themeName);
            localStorage.setItem('techzevron_theme', themeName);
            
            // Update active state in modal
            document.querySelectorAll('.theme-option').forEach(opt => {
                opt.classList.remove('active');
            });
            const activeOpt = document.querySelector(`.theme-option[data-theme="${themeName}"]`);
            if (activeOpt) activeOpt.classList.add('active');
            
            closeThemeModal();
            showToast('Theme: ' + themeName.charAt(0).toUpperCase() + themeName.slice(1));
        }
        
        function loadTheme() {
            const savedTheme = localStorage.getItem('techzevron_theme') || 'midnight';
            document.body.setAttribute('data-theme', savedTheme);
        }
        
        function openThemeModal() {
            const modal = document.getElementById('themeModal');
            if (modal) {
                modal.style.display = 'flex';
            }
            closeSidebar();
        }
        
        function closeThemeModal() {
            const modal = document.getElementById('themeModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
        
        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuToggle = document.getElementById('menuToggle');
            
            if (sidebar) sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
            if (menuToggle) menuToggle.classList.toggle('active');
            
            document.body.style.overflow = document.body.style.overflow === 'hidden' ? '' : 'hidden';
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuToggle = document.getElementById('menuToggle');
            
            if (sidebar) sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            if (menuToggle) menuToggle.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // Toast Notification
        function showToast(message) {
            const existingToast = document.querySelector('.toast-notification');
            if (existingToast) existingToast.remove();
            
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Close modals on outside click
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
                closeThemeModal();
            }
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
        });
        
        // Scroll reveal
        function reveal() {
            var reveals = document.querySelectorAll('.reveal');
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add('active');
                }
            }
        }
        window.addEventListener('scroll', reveal);
    </script>
</body>
</html>

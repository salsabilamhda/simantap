    </main>
</div>

<script>
    lucide.createIcons();
    function toggleSidebar() {
        const sidebar = document.getElementById('main-sidebar');
        const backdrop = document.getElementById('mobile-sidebar-backdrop');
        sidebar.classList.toggle('-translate-x-full');
        backdrop.classList.toggle('hidden');
    }
</script>
</body>
</html>

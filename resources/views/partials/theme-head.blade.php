<script>
    (function () {
        try {
            var savedTheme = localStorage.getItem('site-theme');
            var theme = savedTheme === 'light' || savedTheme === 'dark'
                ? savedTheme
                : 'dark';

            document.documentElement.setAttribute('data-theme', theme);
        } catch (error) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    })();
</script>

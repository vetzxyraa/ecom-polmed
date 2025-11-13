</div> 
</main> 

<footer class="footer">
    <div class="container">
        <p>PoliTeknik Medan</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace();
</script>

<script>
    window.onload = function() {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            const loadTime = Date.now() - startTime;
            const delay = Math.max(0, MIN_LOAD_TIME - loadTime);

            setTimeout(() => {
                preloader.classList.add('hidden');
            }, delay);
        }
    };
</script>

</body>
</html>
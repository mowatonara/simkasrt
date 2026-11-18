    </div>
  </main>
</div>
<script>
document.getElementById('toggle-sidebar').addEventListener('click', function () {
  const sidebar = document.getElementById('sidebar');
  sidebar.classList.toggle('w-64');
  sidebar.classList.toggle('w-20');
  sidebar.querySelectorAll('.menu-label').forEach(function (label) {
    label.classList.toggle('hidden');
  });
});
document.getElementById('mobile-menu').addEventListener('click', function () {
  document.getElementById('sidebar').classList.toggle('hidden');
});
</script>
</body>
</html>

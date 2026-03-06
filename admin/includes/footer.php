    </div><!-- end page-content -->
</div><!-- end main-content -->

<script>
// Auto dismiss alerts
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'all 0.4s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        setTimeout(() => el.remove(), 400);
    }, 3500);
});

// Image preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>

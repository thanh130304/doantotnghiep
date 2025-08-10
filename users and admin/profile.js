document.getElementById("avatarInput").addEventListener("change", function () {
    if (this.files && this.files[0]) {
        const form = document.getElementById("avatarForm");
        form.submit();
    }
});

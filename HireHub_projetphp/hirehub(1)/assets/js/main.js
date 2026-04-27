// Validation spécifique selon le champ fichier
document.addEventListener("DOMContentLoaded", function () {

    /* =========================
       VALIDATION CV (PDF ONLY)
       ========================= */
    const cvInput = document.querySelector('input[name="cv"]');

    if (cvInput) {
        cvInput.form.addEventListener("submit", function (e) {
            const file = cvInput.files[0];

            if (!file) {
                alert("Please upload your CV");
                e.preventDefault();
                return;
            }

            const ext = file.name.split('.').pop().toLowerCase();

            if (ext !== "pdf") {
                alert("Only PDF files are allowed for CV");
                e.preventDefault();
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert("CV size must be less than 2MB");
                e.preventDefault();
            }
        });
    }

    /* =========================
       VALIDATION IMAGE (ADMIN)
       ========================= */
    const imageInputs = document.querySelectorAll('input[type="file"][name="image"]');

    imageInputs.forEach(function (imageInput) {

        imageInput.form.addEventListener("submit", function (e) {

            if (imageInput.files.length === 0) {
                return; // image optionnelle
            }

            const file = imageInput.files[0];
            const ext = file.name.split('.').pop().toLowerCase();
            const allowed = ["jpg", "jpeg", "png", "gif", "webp"];

            if (!allowed.includes(ext)) {
                alert("Only JPG, PNG, GIF or WEBP images are allowed");
                e.preventDefault();
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert("Image size must be less than 5MB");
                e.preventDefault();
            }
        });

    });

});

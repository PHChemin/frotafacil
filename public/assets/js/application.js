document.addEventListener("DOMContentLoaded", function () {
  // Avatar
  const imagePreviewInput = document.getElementById("image_preview_input");
  const preview = document.getElementById("image_preview");
  const imagePreviewSubmit = document.getElementById("image_preview_submit");

  if (imagePreviewInput && preview && imagePreviewSubmit) {
    imagePreviewInput.style.display = "none";
    imagePreviewSubmit.style.display = "none";

    preview.addEventListener("click", function () {
      imagePreviewInput.click();
    });

    imagePreviewInput.addEventListener("change", function (event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          imagePreviewSubmit.style.display = "block";
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // Botão de salvar (CNH)
  const checkboxes = document.querySelectorAll(
    'input[name="driver[license_category][]"]'
  );
  const submitButton = document.getElementById("submit_update_profile");

  if (checkboxes.length > 0 && submitButton) {
    function updateButtonState() {
      const isChecked = Array.from(checkboxes).some(
        (checkbox) => checkbox.checked
      );
      submitButton.disabled = !isChecked;
    }

    checkboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", updateButtonState);
    });

    updateButtonState(); // Garante que o estado inicial do botão esteja correto
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const searchBtn = document.getElementById("buscar_motorista");

  searchBtn.addEventListener("click", function () {
    const cpf = document.getElementById("driver_cpf")?.value;
    const feedback = document.getElementById("cpf_feedback");
    const driverIdInput = document.getElementById("driver_id");

    if (!cpf) {
      feedback.innerHTML = "Por favor, informe um CPF.";
      return;
    }

    fetch(`/api/driver/find-by-cpf?cpf=${cpf}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          driverIdInput.value = data.driver_id;
          feedback.innerHTML = `<span class="text-success">${data.message}</span>`;
        } else {
          driverIdInput.value = "";
          feedback.innerHTML = `<span class="text-danger">${data.message}</span>`;
        }
      })
      .catch(() => {
        feedback.innerHTML =
          '<span class="text-danger">Erro ao buscar motorista.</span>';
      });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const availableRoutes = document.getElementById("available-routes");
  const selectedRoutes = document.getElementById("selected-routes");

  function updatePositions() {
    selectedRoutes
      .querySelectorAll(".list-group-item")
      .forEach((item, index) => {
        item.querySelector(".position").textContent = index + 1 + ".";
      });
  }

  // Adicionar rota
  document.querySelectorAll(".add-route").forEach((button) => {
    button.addEventListener("click", function () {
      const routeId = this.dataset.id;

      // Verifica se já foi adicionada
      if (selectedRoutes.querySelector(`[data-id="${routeId}"]`)) return;

      const listItem = this.closest(".list-group-item").cloneNode(true);
      const routeText = listItem.textContent.replace(`Adicionar`, "").trim();

      listItem.querySelector(".add-route").remove();

      listItem.innerHTML = `
        <span class="position"></span>
        ${routeText}
        <div>
          <button type="button" class="btn btn-sm btn-secondary move-up" id="move_up_${routeId}">↑</button>
          <button type="button" class="btn btn-sm btn-secondary move-down" id="move_down_${routeId}">↓</button>
          <button type="button" class="btn btn-sm btn-danger remove-route" id="remove_route_${routeId}">Remover</button>
        </div>
        <input type="hidden" name="routes[]" value="${routeId}">
      `;

      listItem.setAttribute("data-id", routeId);
      selectedRoutes.appendChild(listItem);
      updatePositions();
    });
  });

  // Remover rota
  selectedRoutes.addEventListener("click", function (event) {
    if (event.target.classList.contains("remove-route")) {
      event.target.closest(".list-group-item").remove();
      updatePositions();
    }
  });

  // Mover para cima
  selectedRoutes.addEventListener("click", function (event) {
    if (event.target.classList.contains("move-up")) {
      const item = event.target.closest(".list-group-item");
      if (item.previousElementSibling) {
        item.parentNode.insertBefore(item, item.previousElementSibling);
        updatePositions();
      }
    }
  });

  // Mover para baixo
  selectedRoutes.addEventListener("click", function (event) {
    if (event.target.classList.contains("move-down")) {
      const item = event.target.closest(".list-group-item");
      if (item.nextElementSibling) {
        item.parentNode.insertBefore(item.nextElementSibling, item);
        updatePositions();
      }
    }
  });
});

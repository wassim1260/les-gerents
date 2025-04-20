document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const inputs = form.querySelectorAll("input[required], select[required], textarea[required]");
    const emailField = form.querySelector("input[type='email']");
  
    form.addEventListener("submit", function (e) {
      e.preventDefault();
  
      let hasError = false;
      let errorMessage = "";
  
      inputs.forEach((input) => {
        input.classList.remove("error");
  
        if (input.value.trim() === "") {
          input.classList.add("error");
          hasError = true;
        }
      });
  
      if (emailField) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value.trim())) {
          emailField.classList.add("error");
          hasError = true;
          errorMessage = "Adresse email invalide.";
        }
      }
  
      if (hasError) {
        if (!errorMessage) {
          errorMessage = "Veuillez remplir tous les champs obligatoires.";
        }
        alert(errorMessage);
      } else {
        form.submit(); // soumettre si tout est bon
      }
    });
  
    // Ajout d’un effet de focus
    const allInputs = form.querySelectorAll("input, select, textarea");
  
    allInputs.forEach((input) => {
      input.addEventListener("focus", () => {
        input.style.borderColor = "#007bff";
      });
  
      input.addEventListener("blur", () => {
        input.style.borderColor = "#ccc";
      });
    });
  });
  
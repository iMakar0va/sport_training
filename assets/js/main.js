// Переключение форм авторизации и регистрации
function toggleForms(showRegister) {
  document.getElementById("login-box").classList.toggle("hidden", showRegister);
  document
    .getElementById("register-box")
    .classList.toggle("hidden", !showRegister);
}

// Обработчик регистрации
function registerUser(e) {
  e.preventDefault();
  const form = document.getElementById("registerForm");
  const formData = new FormData(form);

  fetch("register.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.text())
    .then((response) => {
      alert(response);
      form.reset();
    })
    .catch((err) => {
      alert("Ошибка при регистрации.");
      console.error(err);
    });
}

// Обработчик авторизации
function loginUser(e) {
  e.preventDefault();
  const form = document.getElementById("loginForm");
  const formData = new FormData(form);

  fetch("login.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.text())
    .then((response) => {
      if (response === "success") {
        window.location.href = "dashboard.php"; // или куда ты хочешь
      } else {
        alert(response);
      }
    })
    .catch((err) => {
      alert("Ошибка при входе.");
      console.error(err);
    });
}

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Отслеживайте свои тренировки и достигайте целей с нашим спортивным приложением." />
    <title>Твой Тренер Онлайн</title>
    <link rel="stylesheet" href="assets/css/main.css" />
</head>

<body>
    <main class="container">
        <header>
            <div class="container">
                <h1>Твой Тренер Онлайн</h1>
                <p>Начни путь к лучшей версии себя прямо сейчас!</p>
            </div>
        </header>
        <section class="auth-block">
            <div class="form-box" id="login-box">
                <h2>Вход</h2>
                <form id="loginForm" onsubmit="loginUser(event)">
                    <input type="email" name="email" placeholder="Почта" required />
                    <input type="password" name="password" placeholder="Пароль" required />
                    <button type="submit">Войти</button>
                </form>
                <p class="toggle-link">Нет аккаунта? <a href="#" onclick="toggleForms(true)">Зарегистрируйтесь</a></p>
            </div>
            <div class="form-box hidden" id="register-box">
                <h2>Регистрация</h2>
                <form id="registerForm" onsubmit="registerUser(event)">
                    <input type="text" name="name" placeholder="Имя" required />
                    <input type="email" name="email" placeholder="Email" required />
                    <input type="password" name="password" placeholder="Пароль" required />
                    <button type="submit">Зарегистрироваться</button>
                </form>
                <p class="toggle-link">Уже есть аккаунт? <a href="#" onclick="toggleForms(false)">Войти</a></p>
            </div>
        </section>

        <section class="advantages">
            <h2>Наши преимущества</h2>
            <div class="cards">
                <div class="card">
                    <img src="assets/img/1.svg" alt="Прогресс" />
                    <h3>Достижения в центре внимания</h3>
                    <p>Отслеживай прогресс, ставь цели и становись лучше каждый день. Наш сервис помогает видеть реальный результат!</p>
                </div>
                <div class="card">
                    <img src="assets/img/2.svg" alt="История силы" />
                    <h3>История твоей силы</h3>
                    <p>Вся твоя активность — в одном месте. Возвращайся, смотри, вдохновляйся своим ростом.</p>
                </div>
                <div class="card">
                    <img src="assets/img/3.svg" alt="Аналитика" />
                    <h3>Аналитика тренировок</h3>
                    <p>Получай наглядную статистику: продолжительность, калории, активность по неделям — всё в одном месте.</p>
                </div>
            </div>
        </section>
    </main>
    <script src="assets/js/main.js"></script>

</body>

</html>
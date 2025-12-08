<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    @vite(['resources/css/LS-style.css'])

</head>
<body>
    <main>
        <form class="Форма" action="{{ route('register') }}" method="post">
            @csrf
            <img
            class="лого"
            src="{{ asset('images/logo.jpg') }}"
            alt="logo"
            width="100px"
            height="100px"
            />
            <label for="login"></label>
            <input class="ввод логин" type="text" id="name" name="name" placeholder="Логин">

            <label for="email"></label>
            <input class="ввод логин" type="email" id="email" name="email" placeholder="Почта">

            <label for="password"></label>
            <input class="ввод пароль" type="password" id="password" name="password" placeholder="Пароль">

            <label for="password_confirmation"></label>
            <input class="ввод пароль" type="password" id="password_confirmation" name="password_confirmation" placeholder="Повтор пароля">

            <button class="ввод кнопка" type="submit">Отправить данные</button>

        </form>
    </main>
</body>
</html>

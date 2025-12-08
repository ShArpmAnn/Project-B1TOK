<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    @vite(['resources/css/LS-style.css'])

</head>
<body>
    <main>
        <form class="Форма" action="{{ route('login') }}" method="post">
            @csrf
            <img
            class="лого"
            src="{{ asset('images/logo.jpg') }}"
            alt="logo"
            width="100px"
            height="100px"
            />
            <label for="email"></label>
            <input class="ввод email" type="text" id="email" name="email" placeholder="Почта">

            <label for="password"></label>
            <input class="ввод пароль" type="password" id="password" name="password" placeholder="Пароль">

            <button class="ввод кнопка" type="submit">Отправить данные</button>

        </form>
    </main>
</body>
</html>

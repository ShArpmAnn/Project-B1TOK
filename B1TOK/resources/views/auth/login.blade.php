<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="CSS/normal.css">
    <link rel="stylesheet" href="CSS/LR-style.css">
    
</head>
<body>
    <main>
        <form class="Форма" action="">
            <img 
            class="лого"
            src="images/лого.jpg" 
            alt="logo"
            width="100px"
            height="100px"
            />
            <label for="login"></label>
            <input class="ввод логин" type="text" id="login" name="user-login" placeholder="Логин / Почта">

            <label for="password"></label>
            <input class="ввод пароль" type="text" id="password" name="user-password" placeholder="Пароль">

            <button class="ввод кнопка" type="submit">Отправить данные</button>

        </form>
    </main>
</body>
</html>

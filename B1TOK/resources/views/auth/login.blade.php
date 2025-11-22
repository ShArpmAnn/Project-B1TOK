<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Регистрация</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<form action="{{ route('login') }}"  method="post">
    @csrf
    <div class="form-group mt-3">
        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Введите email" id="email" class="form-control">
    </div>
    <div class="form-group mt-3">
        <label for="password">Пароль</label>
        <input type="password" name="password" placeholder="Введите пароль" id="password" class="form-control">
    </div>

    <button type="submit" class="btn btn-success mt-2">Войти</button>
</form>


</body>
</html>

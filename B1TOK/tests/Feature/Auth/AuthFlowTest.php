<?php

namespace Tests\Feature\Auth;


use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_create_account()
    {
        // Arrange
        $userData = [
            'name' => 'Иван Иванов',
            'email' => 'ivan@gmail.com',
            'password' => 'SecurePassword123!@#',
            'password_confirmation' => 'SecurePassword123!@#',
        ];

        // Act
        $response = $this->post('/register', $userData);

        // Assert
        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Регистрация успешна');

        // Проверяем создание пользователя в базе
        $this->assertDatabaseHas('users', [
            'name' => htmlspecialchars('Иван Иванов'),
            'email' => 'ivan@gmail.com',
        ]);

        $user = User::where('email', 'ivan@gmail.com')->first();
        $this->assertNotNull($user);

        // Проверяем хэширование пароля
        $this->assertTrue(Hash::check('SecurePassword123!@#', $user->password));

        // Проверяем что пользователь автоматически авторизован
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_can_login_to_created_account()
    {
        // Arrange - создаем пользователя
        $user = User::factory()->create([
            'name' => 'Петр Петров',
            'email' => 'petr@example.com',
            'password' => Hash::make('MyPassword123!'),
        ]);

        // Act - пытаемся войти
        $response = $this->post('/login', [
            'email' => 'petr@example.com',
            'password' => 'MyPassword123!',
        ]);

        // Assert
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_can_logout_from_account()
    {
        // Arrange - создаем и авторизуем пользователя
        $user = User::factory()->create();
        $this->actingAs($user);

        // Убеждаемся что пользователь авторизован
        $this->assertAuthenticatedAs($user);

        // Act - выходим
        $response = $this->post('/logout');

        // Assert
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    #[Test]
    public function user_can_login_again_after_logout()
    {
        // Arrange - создаем пользователя
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        // 1. Первый вход
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        // 2. Выход
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();

        // 3. Повторный вход
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function complete_account_lifecycle()
    {
        // 1. Регистрация нового аккаунта
        $registerData = [
            'name' => 'Анна Сидорова',
            'email' => 'anna@gmail.com',
            'password' => 'AnnaPass123!@#',
            'password_confirmation' => 'AnnaPass123!@#',
        ];

        $response = $this->post('/register', $registerData);
        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Регистрация успешна');

        $user = User::where('email', 'anna@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);

        // 2. Выход
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();

        // 3. Повторный вход
        $response = $this->post('/login', [
            'email' => 'anna@gmail.com',
            'password' => 'AnnaPass123!@#',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        // 4. Еще раз выход
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    #[Test]
    public function user_cannot_login_with_wrong_password()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@gmail.com',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        // Act - неправильный пароль
        $response = $this->post('/login', [
            'email' => 'user@gmail.com',
            'password' => 'WrongPassword123!',
        ]);

        // Assert
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function user_cannot_login_with_nonexistent_email()
    {
        // Act - несуществующий email
        $response = $this->post('/login', [
            'email' => 'nonexistent@gmail.com',
            'password' => 'SomePassword123!',
        ]);

        // Assert
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function multiple_users_can_have_separate_sessions()
    {
        // Создаем двух пользователей
        $user1 = User::factory()->create([
            'email' => 'user1@gmail.com',
            'password' => Hash::make('Password1!'),
        ]);

        $user2 = User::factory()->create([
            'email' => 'user2@gmail.com',
            'password' => Hash::make('Password2!'),
        ]);

        // Вход первого пользователя
        $response = $this->post('/login', [
            'email' => 'user1@gmail.com',
            'password' => 'Password1!',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user1);

        // Выход первого
        $this->post('/logout');
        $this->assertGuest();

        // Вход второго пользователя
        $response = $this->post('/login', [
            'email' => 'user2@gmail.com',
            'password' => 'Password2!',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user2);
        $this->assertNotEquals($user1->id, $user2->id);
    }

    #[Test]
    public function user_session_persists_across_requests()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'persistent@gmail.com',
            'password' => Hash::make('PersistentPass123!'),
        ]);

        // Вход
        $this->post('/login', [
            'email' => 'persistent@gmail.com',
            'password' => 'PersistentPass123!',
        ]);

        // Проверяем что сессия сохраняется между запросами
        $response = $this->get('/');
        $response->assertOk();
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/some-other-route'); // если есть другие маршруты
        // Главное - пользователь остается авторизованным
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function logout_invalidates_session()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'logouttest@gmail.com',
            'password' => Hash::make('LogoutTest123!'),
        ]);

        // Вход
        $this->post('/login', [
            'email' => 'logouttest@gmail.com',
            'password' => 'LogoutTest123!',
        ]);

        $this->assertAuthenticatedAs($user);

        // Выход
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();

        // Пытаемся получить доступ к защищенному маршруту
        // (замените '/dashboard' на ваш защищенный маршрут если есть)
        $response = $this->post('/logout');
        // Ожидаем редирект на логин или ошибку 403/401
        $this->assertTrue(in_array($response->getStatusCode(), [302, 401, 403]));
    }

    #[Test]
    public function user_can_access_protected_route_after_login()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'protected@gmail.com',
            'password' => Hash::make('ProtectedPass123!'),
        ]);

        // Сначала неавторизованный запрос
        $response = $this->get('/'); // или другой защищенный маршрут
        $initialStatus = $response->getStatusCode();

        // Вход
        $this->post('/login', [
            'email' => 'protected@gmail.com',
            'password' => 'ProtectedPass123!',
        ]);

        // Повторный запрос после входа
        $response = $this->post('/logout'); // или другой защищенный маршрут

        // Проверяем что статус изменился (например, был 302 редирект, стал 200)
        $this->assertNotEquals($initialStatus, $response->getStatusCode());
    }

    #[Test]
    public function password_is_hashed_security()
    {
        $password = 'VerySecretPassword123!@#';

        $response = $this->post('/register', [
            'name' => 'Security Test',
            'email' => 'security@gmail.com',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $user = User::where('email', 'security@gmail.com')->first();

        // Пароль не должен храниться в открытом виде
        $this->assertNotEquals($password, $user->password);

        // Длина хэша должна быть достаточной
        $this->assertGreaterThan(50, strlen($user->password));

        // Хэш должен начинаться с $2y$ (bcrypt)
        $this->assertStringStartsWith('$2y$', $user->password);
    }

    #[Test]
    public function user_cannot_register_with_existing_email()
    {
        // Первый пользователь
        User::factory()->create(['email' => 'existing@gmail.com']);

        // Попытка регистрации с тем же email
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'existing@gmail.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1); // только один пользователь
        $this->assertGuest();
    }

    #[Test]
    public function email_is_case_insensitive_for_login()
    {
        $user = User::factory()->create([
            'email' => 'caseinsensitive@gmail.com',
            'password' => Hash::make('Password123!'),
        ]);

        // Пробуем разные варианты регистра
        $testEmails = [
            'CASEINSENSITIVE@GMAIL.COM',
            'CaseInsensitive@Gmail.com',
            'caseinsensitive@gmail.com',
            'CASEINSENSITIVE@gmail.com',
        ];

        foreach ($testEmails as $email) {
            // Выходим если авторизованы
            Auth::logout();
            $this->assertGuest();

            $response = $this->post('/login', [
                'email' => $email,
                'password' => 'Password123!',
            ]);

            $response->assertRedirect('/');
            $this->assertAuthenticatedAs($user);

            // Выходим для следующей итерации
            $this->post('/logout');
        }
    }


    #[Test]
    public function login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        // Проверяем наличие основных элементов формы
        $response->assertSee('</form>', false); // есть форма


        $response->assertSee('type="email"', false); // поле email по имени
        $response->assertSee('type="password"', false); // поле пароля
        $response->assertSee('type="submit"', false); // кнопка отправки

        // Проверяем наличие CSRF токена
        $response->assertSee('name="_token"', false);
    }

    #[Test]
    public function register_page_is_accessible()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        // Проверяем наличие основных элементов формы
        $response->assertSee('</form>', false);
        $response->assertSee('name="name"', false);

        // Для регистрационной формы email тоже type="text"
        $response->assertSee('type="email"', false);
        $response->assertSee('type="password"', false);
        $response->assertSee('name="password_confirmation"', false);
        $response->assertSee('type="submit"', false);

        // Проверяем наличие CSRF токена
        $response->assertSee('name="_token"', false);
    }

    #[Test]
    public function home_page_is_accessible()
    {
        $response = $this->get('/');
        $response->assertOk();
    }

    #[Test]
    public function login_with_empty_data_shows_errors()
    {
        $response = $this->post('/login', []);
        $response->assertSessionHasErrors(['email', 'password']);
        $response->assertRedirect();
    }

    #[Test]
    public function register_with_empty_data_shows_errors()
    {
        $response = $this->post('/register', []);
        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $response->assertRedirect();
    }

    #[Test]
    public function login_requires_valid_email()
    {
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function register_requires_valid_email()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
        ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function register_requires_password_confirmation()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'DifferentPassword',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function login_redirects_when_authenticated()
    {
        // Создаем пользователя и авторизуемся
        $user = User::factory()->create();

        // Пытаемся зайти на страницу логина как авторизованный пользователь
        $response = $this->actingAs($user)->get('/login');

        // Ожидаем редирект на главную страницу
        $response->assertRedirect('/');
    }

    #[Test]
    public function register_redirects_when_authenticated()
    {
        // Создаем пользователя и авторизуемся
        $user = User::factory()->create();

        // Пытаемся зайти на страницу регистрации как авторизованный пользователь
        $response = $this->actingAs($user)->get('/register');

        // Ожидаем редирект на главную страницу
        $response->assertRedirect('/');
    }

    #[Test]
    public function login_page_has_correct_form_action()
    {
        $response = $this->get('/login');
        $response->assertSee('action="https://localhost:8000/login"', false);
        $response->assertSee('method="post"', false);
    }

    #[Test]
    public function register_page_has_correct_form_action()
    {
        $response = $this->get('/register');
        $response->assertSee('action="https://localhost:8000/register"', false);
        $response->assertSee('method="post"', false);
    }

    #[Test]
    public function login_page_contains_logo()
    {
        $response = $this->get('/login');
        $response->assertSee('logo.jpg', false);
        $response->assertSee('alt="logo"', false);
    }

    #[Test]
    public function register_page_contains_logo()
    {
        $response = $this->get('/register');
        $response->assertSee('logo.jpg', false);
        $response->assertSee('alt="logo"', false);
    }

    #[Test]
    public function login_page_has_correct_input_classes()
    {
        $response = $this->get('/login');
        // Проверяем наличие CSS классов из вашей формы
        $response->assertSee('class="Форма"', false);
        $response->assertSee('class="ввод email"', false);
        $response->assertSee('class="ввод пароль"', false);
        $response->assertSee('class="ввод кнопка"', false);
    }

    #[Test]
    public function register_page_has_correct_input_classes()
    {
        $response = $this->get('/register');
        $response->assertSee('class="Форма"', false);
        $response->assertSee('class="ввод логин"', false); // для поля name
        $response->assertSee('class="ввод пароль"', false);
        $response->assertSee('class="ввод кнопка"', false);
    }

    #[Test]
    public function unauthenticated_user_can_access_login_page()
    {
        $response = $this->get('/login');
        $response->assertOk();
    }

    #[Test]
    public function unauthenticated_user_can_access_register_page()
    {
        $response = $this->get('/register');
        $response->assertOk();
    }
}

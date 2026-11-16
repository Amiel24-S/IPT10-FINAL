<?php
namespace App\Controllers;

use App\Models\User;
use Spine\Controller;
use Spine\Request;
use Spine\Response;
use Spine\Session;

class AuthController extends Controller
{
    public function loginForm(Request $request): Response
    {
        return $this->view('auth/login', ['title' => 'Log in']);
    }

    public function login(Request $request): Response
    {
        $user = User::findByEmail((string) $request->input('email'));

        // password_verify compares the typed password with the saved hash
        if (!$user || !password_verify((string) $request->input('password'), $user['password'])) {
            return $this->redirect('/login', 'Wrong email or password.', 'error');
        }

        session_regenerate_id(true);
        Session::set('user', ['id' => $user['id'], 'name' => $user['name'], 'role' => $user['role']]);

        $home = $user['role'] === 'admin' ? '/admin' : '/books';
        return $this->redirect($home, "Welcome back, {$user['name']}!");
    }

    public function registerForm(Request $request): Response
    {
        return $this->view('auth/register', ['title' => 'Create account']);
    }

    public function register(Request $request): Response
    {
        $name     = (string) $request->input('name');
        $email    = (string) $request->input('email');
        $password = (string) $request->input('password');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            return $this->redirect('/register', 'Enter your name, a valid email and a password of at least 6 characters.', 'error');
        }
        if (User::findByEmail($email)) {
            return $this->redirect('/register', 'That email is already registered.', 'error');
        }

        $id = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),   // never store plain passwords
            'role'     => 'customer',
        ]);

        Session::set('user', ['id' => $id, 'name' => $name, 'role' => 'customer']);
        return $this->redirect('/books', 'Your account is ready. Happy reading!');
    }

    public function logout(Request $request): Response
    {
        Session::forget('user');
        return $this->redirect('/', 'You have logged out.');
    }
}

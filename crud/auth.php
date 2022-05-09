<?php
session_name('login_session');
session_start([
    'cookie_lifetime' => 60 * 60 * 24,
]);

$_SESSION['logedin'] = false;
$_SESSION['userRoal'] = null;

$name = '';
$userName = '';
$email = '';
$password = '';
$password2 = '';
define('DB_USER', getcwd() . '\\data\\db-users.json');

if (isset($_POST['login']) || isset($_POST['signup'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $userName = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $password2 = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_STRING);
}


if (isset($_POST['login'])) {
    if ($userName != '' && $password != '') {
        $jsonUsersData = file_get_contents(DB_USER);
        $usersData = json_decode($jsonUsersData, true) ?? [];

        foreach ($usersData as $user) {
            if ($user['password'] == sha1($password) && $user['username'] == $userName) {
                $_SESSION['logedin'] = true;
                $_SESSION['user'] = $user['name'];
                $_SESSION['userRoal'] = $user['userRoal'];
                header('location: \\crud\\index.php');
            }
        }
        if (!$_SESSION['logedin']) {
            header("location: index.php?task=signup");
        }
    } else {
        header("location: index.php?task=login");
        return false;
    }
}

if (isset($_POST['signup'])) {
    if ($name != '' && $userName != '' && $email != '' && $password != '' && $password2 != '') {
        $jsonUsersData = file_get_contents(DB_USER);
        $usersData = json_decode($jsonUsersData, true) ?? [];

        if ($password != $password2) {
            header("location: index.php?task=signup");
            return false;
        }

        $newUser = array(
            'name'  => $name,
            'username' => $userName,
            'email'    => $email,
            'password' => sha1($password),
            'userRoal' => 'subscriber'
        );
        array_push($usersData, $newUser);

        $jsonUsersData = json_encode($usersData);
        file_put_contents(DB_USER, $jsonUsersData, LOCK_EX);
        header("location: index.php?task=login");
    } else {
        header("location: index.php?task=signup");
        return false;
    }
}

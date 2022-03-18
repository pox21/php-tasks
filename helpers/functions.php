<?php
session_start();
define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

const DB_HOST = 'localhost';
const DB_NAME = 'users';
const DB_USER = 'root';
const DB_PASS = 'root';

function dbConnect() {

    $pdoOptions = [
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    static $connect = null;

    if ($connect === null) {

        try {
            $connect = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, $pdoOptions);
        } catch (PDOException $e) {
            die($e->getMessage());

        }
    }

    return $connect;
}

function dbQuery($sql, $params = [], $exec = false) {
    if (empty($sql)) return false;

    $query = dbConnect()->prepare($sql);
    $query->execute($params);
    if ($exec) {
        return true;
    }
    return $query;
}

function setFlashMessage($name, $message) {
    $_SESSION[$name] = $message;
}

function getUserInfo($name) {
    $params = ['first_name' => $name];
    $sql = "SELECT * FROM `users_table` WHERE `first_name` = :first_name";
    return dbQuery($sql, $params)->fetch();
}

function addUser($firstName, $lastName = '', $username = '') {
    $params = [
        'first_name' => $firstName,
        'last_name' => $lastName ? $lastName : 'no name',
        'username' => $username ? $username : 'no name'
    ];
    $sql = "INSERT INTO `users_table` (`first_name`, `last_name`, `username`) VALUES (:first_name, :last_name, :username);";
    return dbQuery($sql, $params, true);
}

function registerUser($authData) {
    if (empty($authData) ||
        !isset($authData['firstName']) || empty(trim($authData['firstName'])) ) return false;


    $user = getUserInfo($authData['firstName']);
    if (!empty($user)) {
        setFlashMessage("errorRegister", "Пользователь " . $authData['firstName'] . " уже существует");
        header('Location: /task_10.php');
        return false;
    }

    addUser($authData['firstName'], $authData['lastName'], $authData['username']);
    setFlashMessage("successRegister", "Пользователь " . $authData['firstName'] . " успешно зарегистрирован");
    return "Пользователь успешно добавлен";
}
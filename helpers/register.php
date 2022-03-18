<?php

include 'functions.php';

if (isset($_POST) && !empty($_POST)) {
    registerUser($_POST);
}

header("Location: /task_10.php");

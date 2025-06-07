<?php
require_once __DIR__ . '/../config/session.php';

Session::start();
Session::destroy();

header('Location: ../../pages/login.php');
exit();
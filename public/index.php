<?php
declare(strict_types=1);

session_start();

require_once './../bootstrap.php';

// handle request
dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
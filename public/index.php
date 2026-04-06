<?php

session_start();
require_once __DIR__ . '/../app/config/bootstrap.php';

Router::dispatch();

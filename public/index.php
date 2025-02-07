<?php

require __DIR__ . '/../config/config.php';

$action = array_key_exists('action', $_GET) ? $_GET['action'] : 'home';

require PROJECT_DIR . '/appli/controller/'.$action.'.php';
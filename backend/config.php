<?php

namespace Planner;

define('ROOT', __DIR__ . '/');

$config = json_decode(file_get_contents(ROOT . 'config.json'), true);
define('DB_HOST', $config['db']['host']);
define('DB_NAME', $config['db']['name']);
define('DB_USER', $config['db']['user']);
define('DB_PASSWORD', $config['db']['password']);
define('DB_PORT', $config['db']['port']);

// Enums
include_once ROOT . 'enums/ApiResponseCodes.php';
include_once ROOT . 'enums/Roles.php';
include_once ROOT . 'enums/Tables.php';

// Classes
include_once ROOT . 'classes/Database.php';
include_once ROOT . 'classes/DataValidator.php';
include_once ROOT . 'classes/api/ApiBase.php';
include_once ROOT . 'classes/api/ProfessionApi.php';
include_once ROOT . 'classes/api/ProfessionsApi.php';
include_once ROOT . 'classes/api/UserApi.php';
include_once ROOT . 'classes/api/UsersApi.php';

// Models
include_once ROOT . 'models/Profession.php';
include_once ROOT . 'models/User.php';

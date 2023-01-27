<?php

namespace Planner\Classes\Api;

use Planner\Classes\Database;
use Planner\Enums\ApiResponseCodes;
use Planner\Enums\Tables;
use Planner\Models\User;

if (!defined('ROOT')) {
    exit;
}

/**
 * API Klasse für den Endpunkt /users.
 */
class UsersApi extends ApiBase {

    /**
     * Übergibt die Request Methoden an die Elternklasse.
     */
    public function __construct() {
        parent::__construct([
            parent::REQUEST_METHOD_GET => 'getUsers'
        ]);
    }

    /**
     * Gibt alle Benutzer zurück.
     */
    private function getUsers() {

        $users = $this->db->query('SELECT * FROM ' . Tables::Users->value);
        $users = $users->fetch_all(MYSQLI_ASSOC);
        $users = array_map(function ($user) {
            return new User(
                $user['email'],
                $user['first_name'],
                $user['last_name'],
                $user['role'],
                $user['profession_id'],
                $user['id']
            );
        }, $users);

        $this->sendResponse(
            ApiResponseCodes::RequestSuccessful,
            empty($users) ? 204 : 200,
            $users
        );
    }
}

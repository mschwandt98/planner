<?php

namespace Planner\Classes\Api;

use Planner\Classes\DataValidator;
use Planner\Enums\ApiResponseCodes;
use Planner\Enums\Roles;
use Planner\Enums\Tables;
use Planner\Models\User;

if (!defined('ROOT')) {
    exit;
}

/**
 * API Klasse für den Endpunkt /user.
 */
class UserApi extends ApiBase {

    /**
     * Übergibt die Request Methoden an die Elternklasse.
     */
    public function __construct() {
        parent::__construct([
            parent::REQUEST_METHOD_POST     => 'postUser',
            parent::REQUEST_METHOD_PUT      => 'updateUser',
            parent::REQUEST_METHOD_DELETE   => 'deleteUser'
        ]);
    }

    /**
     * Löscht einen Benutzer aus der Datenbank.
     *
     * @return void
     */
    private function deleteUser() {

        global $_DELETE;

        if (!array_key_exists('id', $_DELETE)) {
            $this->sendResponse(ApiResponseCodes::MissingParameter);
        }

        $userId = $_DELETE['id'];
        if (is_string($userId)) {
            $_DELETE['id'] = trim($userId);
        }

        $userId = intval($userId);

        $deleted = $this->db->execute_query('DELETE FROM ' . Tables::Users->value . ' WHERE id = ?', 'i', $userId);
        $this->sendResponse($deleted
            ? ApiResponseCodes::RequestSuccessful
            : ApiResponseCodes::UnknownDatabaseError
        );
    }

    /**
     * Erstellt einen neuen Benutzer in der Datenbank und gibt dessen ID an
     * den Client zurück.
     *
     * @return void
     */
    private function postUser() {

        extract($_POST);

        if (!isset($email) ||
            !isset($first_name) ||
            !isset($last_name) ||
            !isset($password)
        ) {
            $this->sendResponse(ApiResponseCodes::MissingParameter);
        }

        $email = DataValidator::sanitize_string($email);
        if (!DataValidator::is_email($email)) {
            $this->sendResponse(ApiResponseCodes::InvalidEmail);
        }

        $password = DataValidator::sanitize_string($password);
        if (!DataValidator::is_valid_password($password)) {
            $this->sendResponse(ApiResponseCodes::InvalidPassword);
        }
        $password = password_hash($password, PASSWORD_DEFAULT);

        $first_name = DataValidator::sanitize_string($first_name);
        $last_name = DataValidator::sanitize_string($last_name);

        $user = new User(
            $email,
            $first_name,
            $last_name,
            isset($role) ? DataValidator::sanitize_string($role) : null,
            isset($profession_id) ? $profession_id : null
        );

        $fields = [
            'email'         => $user->email,
            'first_name'    => $user->first_name,
            'last_name'     => $user->last_name,
            'password'      => $password
        ];
        $formats = ['s', 's', 's', 's'];
        if ($user->role !== null) {
            $fields['role'] = $user->role->value;
            $formats[] = 's';
        }
        if ($user->profession_id !== null) {
            $fields['profession_id'] = $user->profession_id;
            $formats[] = 'i';
        }

        $insertId = $this->db->insert(Tables::Users, $fields, $formats);
        $insertId === 0
            ? $this->sendResponse(ApiResponseCodes::UnknownDatabaseError)
            : $this->sendResponse(ApiResponseCodes::RequestSuccessful, $insertId);
    }

    /**
     * Aktualisiert einen Benutzer in der Datenbank.
     *
     * @return void
     */
    private function updateUser() {

        global $_PUT;

        if (!array_key_exists('id', $_PUT)) {
            $this->sendResponse(ApiResponseCodes::MissingParameter);
        }

        $updateFields = [];
        $formats = [];
        if (array_key_exists('email', $_PUT)) {
            $email = DataValidator::sanitize_string($_PUT['email']);
            if (!DataValidator::is_email($email)) {
                $this->sendResponse(ApiResponseCodes::InvalidEmail);
            }
            $updateFields['email'] = $email;
            $formats[] = 's';
        }

        if (array_key_exists('first_name', $_PUT)) {
            $first_name = DataValidator::sanitize_string($_PUT['first_name']);
            $updateFields['first_name'] = $first_name;
            $formats[] = 's';
        }

        if (array_key_exists('last_name', $_PUT)) {
            $last_name = DataValidator::sanitize_string($_PUT['last_name']);
            $updateFields['last_name'] = $last_name;
            $formats[] = 's';
        }

        if (array_key_exists('role', $_PUT)) {
            $role = DataValidator::sanitize_string($_PUT['role']);
            $role = Roles::tryFrom($role);
            if ($role === null) {
                $this->sendResponse(ApiResponseCodes::InvalidRole);
            }
            $updateFields['role'] = $role;
            $formats[] = 's';
        }

        if (array_key_exists('profession_id', $_PUT)) {
            $profession_id = intval($_PUT['profession_id']);
            $updateFields['profession_id'] = $profession_id;
            $formats[] = 'i';
        }

        if (empty($updateFields)) {
            $this->sendResponse(ApiResponseCodes::MissingParameter);
        }

        $userId = $_PUT['id'];
        if (is_string($userId)) {
            $_PUT['id'] = trim($userId);
        }

        $userId = intval($userId);

        $updated = $this->db->update(Tables::Users, $updateFields, $formats, ['id' => $userId], ['i']);
        $this->sendResponse($updated
            ? ApiResponseCodes::RequestSuccessful
            : ApiResponseCodes::UnknownDatabaseError
        );
    }
}

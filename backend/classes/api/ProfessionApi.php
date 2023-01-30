<?php

namespace Planner\Classes\Api;

use Planner\Classes\DataValidator;
use Planner\Enums\ApiResponseCodes;
use Planner\Enums\Tables;
use Planner\Models\Profession;

if (!defined('ROOT')) {
    exit;
}

/**
 * API Klasse für den Endpunkt /profession.
 */
class ProfessionApi extends ApiBase {

    /**
     * Übergibt die Request Methoden an die Elternklasse.
     */
    public function __construct() {

        parent::__construct([
            parent::REQUEST_METHOD_POST,
            parent::REQUEST_METHOD_PUT,
            parent::REQUEST_METHOD_DELETE
        ]);

        switch ($this->method) {
            case parent::REQUEST_METHOD_POST:
                $this->postProfession();
                break;
            case parent::REQUEST_METHOD_PUT:
                $this->updateProfession();
                break;
            case parent::REQUEST_METHOD_DELETE:
                $this->deleteProfession();
                break;
        }
    }

    /**
     * Löscht einen Beruf aus der Datenbank.
     *
     * @return void
     */
    private function deleteProfession() {

        global $_DELETE;

        if (!array_key_exists('id', $_DELETE)) {
            $this->sendResponse(ApiResponseCodes::MissingParameter);
        }

        $professionId = $_DELETE['id'];
        if (is_string($professionId)) {
            $_DELETE['id'] = trim($professionId);
        }

        $professionId = intval($professionId);

        $deleted = $this->db->execute_query(
            'DELETE FROM ' . Tables::Professions->value . ' WHERE id = ?', 'i', $professionId
        );
        $this->sendResponse($deleted
            ? ApiResponseCodes::RequestSuccessful
            : ApiResponseCodes::UnknownDatabaseError
        );
    }

    /**
     * Erstellt einen neuen Beruf in der Datenbank und gibt dessen ID an
     * den Client zurück.
     *
     * @return void
     */
    private function postProfession() {

        extract($_POST);

        if (!isset($title)) {
            $this->sendResponse(ApiResponseCodes::MissingParameter);
        }

        $title = DataValidator::sanitize_string($title);

        $profession = new Profession($title);

        $fields = [
            'title' => $profession->title
        ];
        $formats = ['s'];

        $insertId = $this->db->insert(Tables::Professions, $fields, $formats);
        $insertId === 0
            ? $this->sendResponse(ApiResponseCodes::UnknownDatabaseError)
            : $this->sendResponse(ApiResponseCodes::RequestSuccessful, null, $insertId);
    }

    /**
     * Aktualisiert einen Beruf in der Datenbank.
     *
     * @return void
     */
    private function updateProfession() {

        global $_PUT;

        extract($_PUT);
        if (!isset($id) || !isset($title)) {
            $this->sendResponse(ApiResponseCodes::MissingParameter);
        }

        $title = DataValidator::sanitize_string($title);
        $updateFields = ['title' => $title];
        $formats = ['s'];

        if (is_string($id)) {
            $id = trim($id);
        }

        $id = intval($id);

        $updated = $this->db->update(Tables::Professions, $updateFields, $formats, ['id' => $id], ['i']);
        $this->sendResponse($updated
            ? ApiResponseCodes::RequestSuccessful
            : ApiResponseCodes::UnknownDatabaseError
        );
    }
}

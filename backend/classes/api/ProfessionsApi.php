<?php

namespace Planner\Classes\Api;

use Planner\Enums\ApiResponseCodes;
use Planner\Enums\Tables;
use Planner\Models\Profession;

if (!defined('ROOT')) {
    exit;
}

/**
 * API Klasse für den Endpunkt /professions.
 */
class ProfessionsApi extends ApiBase {

    /**
     * Übergibt die Request Methoden an die Elternklasse.
     */
    public function __construct() {

        parent::__construct([
            parent::REQUEST_METHOD_GET
        ]);

        $this->getProfessions();
    }

    /**
     * Gibt alle Berufe zurück.
     */
    private function getProfessions() {

        $professions = $this->db->query('SELECT * FROM ' . Tables::Professions->value);
        $professions = $professions->fetch_all(MYSQLI_ASSOC);
        $professions = array_map(function ($profession) {
            return new Profession(
                $profession['title'],
                $profession['id']
            );
        }, $professions);

        $this->sendResponse(
            ApiResponseCodes::RequestSuccessful,
            empty($professions) ? 204 : 200,
            $professions
        );
    }
}

<?php

namespace Planner\Classes\Api;

use Planner\Classes\Database;
use Planner\Enums\ApiResponseCodes;

if (!defined('ROOT')) {
    exit;
}

/**
 * Basis Klasse für alle API Endpunkt Klassen.
 */
class ApiBase {

    protected const REQUEST_METHOD_GET     = 'GET';
    protected const REQUEST_METHOD_POST    = 'POST';
    protected const REQUEST_METHOD_PUT     = 'PUT';
    protected const REQUEST_METHOD_DELETE  = 'DELETE';

    protected readonly Database $db;

    /**
     * Bereitet die Verarbeitung der Anfrage vor und ruft die entsprechende
     * Methode auf.
     *
     * @param array $allowedMethods
     */
    protected function __construct(array $allowedMethods) {
        $this->checkRequestMethod($allowedMethods);
        $this->readMethodInput();
        $this->db = Database::getInstance();
    }

    /**
     * Sendet eine Antwort an den Client.
     *
     * @param ApiResponseCodes  $responseCode       Der Response Code.
     * @param integer|null      $httpResponseCode   Der HTTP Response Code.
     * @param mixed             $responseData       Die Daten, die mit der
     *                                              Antwort gesendet werden.
     * @return void
     */
    protected function sendResponse(
        ApiResponseCodes $responseCode,
        int $httpResponseCode = null,
        mixed $responseData = null
    ) {

        $response = [
            'code'      => $responseCode->getValue(),
            'message'   => $responseCode->getDescription()
        ];
        if ($responseData !== null) {
            $response['data'] = json_encode($responseData);
        }

        http_response_code($httpResponseCode);
        echo $response;

        die();
    }

    /**
     * Überprüft die HTTP Request Methode und ruft die entsprechende Methode
     * auf.
     *
     * @param array $allowedMethods Die erlaubten HTTP Request Methoden mit den
     *                              zugehörigen Methodennamen.
     *
     * Beispiel:
     * [
     *     ApiBase::REQUEST_METHOD_GET => 'get_method_of_child_to_call',
     *     ApiBase::REQUEST_METHOD_POST => 'post_method_of_child_to_call'
     * ]
     */
    private function checkRequestMethod(array $allowedMethods) {

        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if (!in_array($requestMethod, array_keys($allowedMethods))) {
            $this->sendResponse(ApiResponseCodes::MethodNotAllowed);
        }

        if (method_exists($this, $allowedMethods[$requestMethod])) {
            $this->{$allowedMethods[$requestMethod]}();
        }
    }

    /**
     * Liest die Daten aus dem Request Body aus und speichert sie in den
     * globalen Variablen $_PUT bzw. $_DELETE.
     *
     * @return void
     */
    private function readMethodInput() {

        global $_DELETE;
        global $_PUT;

        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod === self::REQUEST_METHOD_PUT || $requestMethod === self::REQUEST_METHOD_DELETE) {

            $requestInput = json_decode(file_get_contents('php://input'), true);
            if ($requestMethod === self::REQUEST_METHOD_PUT) {
                $_PUT = $requestInput;
            } elseif ($requestMethod === self::REQUEST_METHOD_DELETE) {
                $_DELETE = $requestInput;
            }
        }
    }
}

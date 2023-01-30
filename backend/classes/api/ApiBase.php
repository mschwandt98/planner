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
    protected readonly string $method;

    /**
     * Bereitet die Verarbeitung der Anfrage vor und ruft die entsprechende
     * Methode auf.
     *
     * @param array $allowedMethods
     */
    protected function __construct(array $allowedMethods) {
        $this->db = Database::getInstance();
        $this->readMethodInput();
        $this->checkRequestMethod($allowedMethods);
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
            $response['data'] = $responseData;
        }

        if ($httpResponseCode === null) {
            $httpResponseCode = $responseCode->getHttpCode();
        }
        http_response_code($httpResponseCode);

        echo json_encode($response, JSON_UNESCAPED_UNICODE);

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
        if (!in_array($requestMethod, $allowedMethods)) {
            $this->sendResponse(ApiResponseCodes::MethodNotAllowed);
        }

        $this->method = $requestMethod;
    }

    /**
     * Liest die Daten aus dem Request Body aus und speichert sie in den
     * globalen Variablen $_PUT bzw. $_DELETE.
     *
     * @return void
     */
    private function readMethodInput() {

        global $_DELETE;
        global $_GET;
        global $_POST;
        global $_PUT;

        $requestMethod = $_SERVER['REQUEST_METHOD'];

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            switch ($requestMethod) {
            case self::REQUEST_METHOD_GET:
                $_GET = $data;
                break;
            case self::REQUEST_METHOD_POST:
                $_POST = $data;
                break;
            case self::REQUEST_METHOD_PUT:
                $_PUT = $data;
                break;
            case self::REQUEST_METHOD_DELETE:
                $_DELETE = $data;
                break;
            default:
                $this->sendResponse(ApiResponseCodes::MethodNotAllowed);
            }

        } catch (\Exception $e) {
            $this->sendResponse(ApiResponseCodes::InvalidInput);
        }
    }
}

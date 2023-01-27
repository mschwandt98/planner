<?php

namespace Planner\Enums;

enum ApiResponseCodes {

    case RequestSuccessful;
    case MissingParameter;
    case InvalidEmail;
    case InvalidPassword;
    case InvalidRole;
    case MethodNotAllowed;
    case UnknownDatabaseError;

    /**
     * Gibt die Beschreibung des Response Codes zurück.
     *
     * @return string Die Beschreibung des Response Codes.
     */
    public function getDescription(): string {
        return match($this) {
            self::RequestSuccessful     => 'Request successful',
            self::MissingParameter      => 'Missing parameter',
            self::InvalidEmail          => 'Invalid email',
            self::InvalidPassword       => 'Password not matching requirements. Password must be at least 8 ' .
                'characters long and contain at least one uppercase letter, one lowercase letter and one number.',
            self::InvalidRole           => 'Invalid role',
            self::MethodNotAllowed      => 'Method not allowed',
            self::UnknownDatabaseError  => 'Unknown database error'
        };
    }

    /**
     * Gibt den HTTP Response Code zurück.
     *
     * @return integer Der HTTP Response Code.
     */
    public function getHttpCode(): int {
        return match($this) {
            self::RequestSuccessful     => 200,
            self::MissingParameter      => 400,
            self::InvalidEmail          => 400,
            self::InvalidPassword       => 400,
            self::InvalidRole           => 400,
            self::MethodNotAllowed      => 405,
            self::UnknownDatabaseError  => 500
        };
    }

    /**
     * Gibt den Response Code zurück.
     *
     * @return string Der Response Code.
     */
    public function getValue(): string {
        return 'MASCP_' . match($this) {
            self::RequestSuccessful     => '', // TODO
            self::MissingParameter      => '', // TODO
            self::InvalidEmail          => '', // TODO
            self::InvalidPassword       => '', // TODO
            self::InvalidRole           => '', // TODO
            self::MethodNotAllowed      => '', // TODO
            self::UnknownDatabaseError  => '', // TODO
        };
    }
}

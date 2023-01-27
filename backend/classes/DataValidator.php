<?php

namespace Planner\Classes;

if (!defined('ROOT')) {
    exit;
}

/**
 * Stellt Methoden zum Validieren von Daten zur Verfügung.
 */
class DataValidator {

    /**
     * Prüft, ob eine E-Mail-Adresse gültig ist.
     *
     * @param string $email Die zu prüfende E-Mail-Adresse.
     *
     * @return boolean true, wenn die E-Mail-Adresse gültig ist, sonst false.
     */
    public static function is_email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Entfernt HTML- und PHP-Tags aus einem String und entfernt Leerzeichen
     * am Anfang und Ende des Strings.
     *
     * @param string $string Der zu bereinigende String.
     *
     * @return string Der bereinigte String.
     */
    public static function sanitize_string(string $string): string {
        return htmlspecialchars(trim($string));
    }

    /**
     * Prüft, ob ein Passwort den Anforderungen entspricht.
     * Das Passwort muss mindestens 8 Zeichen lang sein und mindestens eine
     * Zahl, einen Groß- und einen Kleinbuchstaben enthalten.
     *
     * @param string $password Das zu prüfende Passwort.
     *
     * @return bool true, wenn das Passwort den Anforderungen entspricht, sonst
     *              false.
     */
    public static function is_valid_password(string $password): bool {

        if (strlen($password) < 8) {
            return false;
        }

        $pattern = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';
        return preg_match($pattern, $password) === 1;
    }
}

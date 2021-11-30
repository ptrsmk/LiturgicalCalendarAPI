<?php
/**
 * enum REQUEST_CONTENT_TYPE
 * Represents all possible Content Types
 * for a Request that the API might receive
 */
class REQUEST_CONTENT_TYPE {
    const JSON      = "application/json";
    const FORMDATA  = "application/x-www-form-urlencoded";
    public static array $values = [ "application/json", "application/x-www-form-urlencoded" ];

    public static function isValid( $value ) {
        return in_array( $value, self::$values );
    }
}

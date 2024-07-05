<?php

namespace Johnrdorazio\LitCal\Params;

use Johnrdorazio\LitCal\Paths\RegionalData;
use Johnrdorazio\LitCal\Enum\Route;
use Johnrdorazio\LitCal\Enum\StatusCode;
use Johnrdorazio\LitCal\Enum\LitLocale;
use Johnrdorazio\LitCal\Enum\RequestMethod;

class RegionalDataParams
{
    private ?object $calendars              = null;
    private static array $widerRegionNames         = [];
    public const array EXPECTED_CATEGORIES  = [
        "nation"      => "NATIONALCALENDAR",
        "diocese"     => "DIOCESANCALENDAR",
        "widerregion" => "WIDERREGIONCALENDAR"
    ];
    public ?string $category = null;
    public ?string $key = null;
    public ?string $locale = null;
    public ?object $payload = null;

    public function __construct()
    {
        $calendarsRoute = (defined('API_BASE_PATH') ? API_BASE_PATH : 'https://litcal.johnromanodorazio.com/api/dev') . Route::CALENDARS->value;
        $metadataRaw = file_get_contents($calendarsRoute);
        if ($metadataRaw) {
            $metadata = json_decode($metadataRaw);
            if (JSON_ERROR_NONE === json_last_error() && property_exists($metadata, 'litcal_metadata')) {
                $this->calendars = $metadata->litcal_metadata;
                //let's remove the Vatican calendar from the list
                array_pop($this->calendars->national_calendars);
                self::$widerRegionNames = $this->calendars->wider_regions_keys;
            }
        }
    }

    public function setData(object $data): bool
    {
        if (false === property_exists($data, 'category') || false === property_exists($data, 'key')) {
            RegionalData::produceErrorResponse(
                StatusCode::BAD_REQUEST,
                "Expected params `category` and `key` but either one or both not present"
            );
        }
        if (false === in_array($data->category, self::EXPECTED_CATEGORIES)) {
            RegionalData::produceErrorResponse(
                StatusCode::BAD_REQUEST,
                "Unexpected value '{$data->category}' for param `category`, acceptable values are: " . implode(', ', array_values(self::EXPECTED_CATEGORIES))
            );
        } else {
            $this->category = $data->category;
            switch ($data->category) {
                case 'NATIONALCALENDAR':
                    if (
                        false === in_array($data->key, $this->calendars->national_calendars_keys)
                        && RegionalData::$APICore->getRequestMethod() !== RequestMethod::PUT
                    ) {
                        $validVals = implode(', ', $this->calendars->national_calendars_keys);
                        RegionalData::produceErrorResponse(StatusCode::BAD_REQUEST, "Invalid value {$data->key} for param `key`, valid values are: {$validVals}");
                    } else {
                        $this->key = $data->key;
                    }
                    // Check the request method: cannot DELETE National calendar data if it is still in use by a Diocesan calendar
                    if (RegionalData::$APICore->getRequestMethod() === RequestMethod::DELETE) {
                        foreach ($this->calendars->diocesan_calendars as $diocesanCalendar) {
                            if ($diocesanCalendar->nation === $data->key) {
                                RegionalData::produceErrorResponse(
                                    StatusCode::BAD_REQUEST,
                                    "Cannot DELETE National Calendar data while there are Diocesan calendars that depend on it."
                                    . " Currently, {$data->key} is in use by Diocesan calendar {$diocesanCalendar->calendar_id}."
                                );
                            }
                        }
                    }
                    break;
                case 'DIOCESANCALENDAR':
                    if (
                        false === in_array($data->key, $this->calendars->diocesan_calendars_keys)
                        && RegionalData::$APICore->getRequestMethod() !== RequestMethod::PUT
                    ) {
                        $validVals = implode(', ', $this->calendars->diocesan_calendars_keys);
                        RegionalData::produceErrorResponse(
                            StatusCode::BAD_REQUEST,
                            "Invalid value {$data->key} for param `key`, valid values are: {$validVals}"
                        );
                    } else {
                        $this->key = $data->key;
                    }
                    break;
                case 'WIDERREGIONCALENDAR':
                    if (
                        false === in_array($data->key, self::$widerRegionNames)
                        && RegionalData::$APICore->getRequestMethod() !== RequestMethod::PUT
                    ) {
                        $validVals = implode(', ', self::$widerRegionNames);
                        $message = "Invalid value {$data->key} for param `key`, valid values are: {$validVals}";
                        RegionalData::produceErrorResponse(StatusCode::BAD_REQUEST, $message);
                    } else {
                        $this->key = $data->key;
                    }
                    // A locale parameter is required for WiderRegion data, whether supplied by the Accept-Language header or by a `locale` parameter
                    $currentWiderRegionArr = array_values(array_filter($this->calendars->wider_regions, fn ($el) => $el->name === $data->key));
                    if (count($currentWiderRegionArr)) {
                        $currentWiderRegion = $currentWiderRegionArr[0];
                        $validLangs = $currentWiderRegion->languages;
                    } else {
                        $message = "I thought I told you that {$data->key} was an invalid wider region value for param `key`, I could not find such a key in a `name` prop in the array: "
                            . json_encode($this->calendars->wider_regions, JSON_PRETTY_PRINT);
                        RegionalData::produceErrorResponse(StatusCode::BAD_REQUEST, $message);
                    }
                    if (property_exists($data, 'locale')) {
                        $data->locale = \Locale::canonicalize($data->locale);
                        if (in_array($data->locale, $validLangs)) {
                            $this->locale = $data->locale;
                        } else {
                            $message = "Invalid value {$data->locale} for param `locale`, valid values for wider region {$currentWiderRegion->name} are: "
                                        . implode(', ', $validLangs);
                            RegionalData::produceErrorResponse(StatusCode::BAD_REQUEST, $message);
                        }
                    } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                        $value = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
                        if (in_array($value, $validLangs)) {
                            $this->locale = $value;
                        } else {
                            $message = "Invalid value {$value} for Accept-Language header, valid values for wider region {$currentWiderRegion->name} are: "
                                        . implode(', ', $validLangs);
                            RegionalData::produceErrorResponse(StatusCode::BAD_REQUEST, $message);
                        }
                    } else {
                        RegionalData::produceErrorResponse(StatusCode::BAD_REQUEST, "`locale` param or `Accept-Language` header required for Wider Region calendar data");
                    }
                    // Check the request method: cannot DELETE Wider Region calendar data if there are national calendars that depend on it
                    if (RegionalData::$APICore->getRequestMethod() === RequestMethod::DELETE) {
                        foreach ($this->calendars->national_calendars as $nationalCalendar) {
                            if (in_array($data->key, $nationalCalendar->wider_regions)) {
                                RegionalData::produceErrorResponse(
                                    StatusCode::BAD_REQUEST,
                                    "Cannot DELETE Wider Region calendar data while there are National calendars that depend on it. "
                                    . "Currently {$data->key} is in use by {$nationalCalendar->calendar_id}"
                                );
                            }
                        }
                    }
                    break;
                default:
                    //nothing to do
            }
        }
        if (in_array(RegionalData::$APICore->getRequestMethod(), [RequestMethod::PUT,RequestMethod::PATCH])) {
            if (false === property_exists($data, 'payload') || false === $data->payload instanceof \stdClass) {
                RegionalData::produceErrorResponse(StatusCode::BAD_REQUEST, "Cannot create or update Calendar data without a payload");
            }
            switch ($this->category) {
                case 'NATIONALCALENDAR':
                    if (
                        false === property_exists($data->payload, 'litcal')
                        || false === property_exists($data->payload, 'metadata')
                        || false === property_exists($data->payload, 'settings')
                    ) {
                        $message = "Cannot create or update National calendar data when the payload does not have required properties `LitCal`, `Metadata` or `Settings`. Payload was:\n" . json_encode($data->payload, JSON_PRETTY_PRINT);
                        RegionalData::produceErrorResponse(StatusCode::BAD_REQUEST, $message);
                    }
                    break;
                case 'DIOCESANCALENDAR':
                    if (
                        false === property_exists($data->payload, 'caldata')
                        || false === property_exists($data->payload, 'diocese')
                        || false === property_exists($data->payload, 'nation')
                    ) {
                        $message = "Cannot create or update Diocesan calendar data when the payload does not have required properties `caldata`, `diocese` or `nation`. Payload was:\n" . json_encode($data->payload, JSON_PRETTY_PRINT);
                        RegionalData::produceErrorResponse(StatusCode::BAD_REQUEST, $message);
                    }
                    break;
                case 'WIDERREGIONCALENDAR':
                    if (
                        false === property_exists($data->payload, 'litcal')
                        || false === property_exists($data->payload, 'metadata')
                        || false === property_exists($data->payload, 'national_calendars')
                        || false === property_exists($data->payload->metadata, 'wider_region')
                        || false === property_exists($data->payload->metadata, 'multilingual')
                    ) {
                        $message = "Cannot create or update Wider Region calendar data when the payload does not have required properties `litcal`, `national_calendars`, `metadata`, `metadata->wider_region`, `metadata->multilingual`. Payload was:\n" . json_encode($data->payload, JSON_PRETTY_PRINT);
                        RegionalData::produceErrorResponse(StatusCode::BAD_REQUEST, $message);
                    }
                    if (
                        true === $data->payload->metadata->multilingual
                        && false === property_exists($data->payload->metadata, 'languages')
                    ) {
                        $message = "Cannot create or update Wider Region calendar data when the payload has value `true` for `metadata->multilingual` but does not have required array `metadata->languages`. Payload was:\n" . json_encode($data->payload, JSON_PRETTY_PRINT);
                        RegionalData::produceErrorResponse(StatusCode::BAD_REQUEST, $message);
                    }
                    break;
            }
            $this->payload = $data->payload;
        }
        return true;
    }
}

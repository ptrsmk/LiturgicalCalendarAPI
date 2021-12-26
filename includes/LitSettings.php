<?php

include_once( 'enums/Epiphany.php' );
include_once( 'enums/Ascension.php' );
include_once( 'enums/CorpusChristi.php' );
include_once( 'enums/LitLocale.php' );
include_once( 'enums/ReturnType.php' );

class LitSettings {
    public int $Year;
    public string $Epiphany             = Epiphany::JAN6;
    public string $Ascension            = Ascension::THURSDAY;
    public string $CorpusChristi        = CorpusChristi::THURSDAY;
    public ?string $Locale              = null;
    public ?string $ReturnType          = null;
    public ?string $NationalCalendar    = null;
    public ?string $DiocesanCalendar    = null;

    const ALLOWED_PARAMS  = [
        "YEAR",
        "EPIPHANY",
        "ASCENSION",
        "CORPUSCHRISTI",
        "LOCALE",
        "RETURNTYPE",
        "NATIONALCALENDAR",
        "DIOCESANCALENDAR"
    ];

    const SUPPORTED_NATIONAL_CALENDARS = [ "ITALY", "USA", "VATICAN" ];

    //If we can get more data from 1582 (year of the Gregorian reform) to 1969
    // perhaps we can lower the limit to the year of the Gregorian reform
    //For now we'll just deal with the Liturgical Calendar from the Editio Typica 1970
    //const YEAR_LOWER_LIMIT          = 1583;
    const YEAR_LOWER_LIMIT          = 1970;

    //The upper limit is determined by the limit of PHP in dealing with DateTime objects
    const YEAR_UPPER_LIMIT          = 9999;
  
    public function __construct( array $DATA ) {
        //we need at least a default value for the current year
        $this->Year = (int)date("Y");

        foreach( $DATA as $key => $value ) {
            $key = strtoupper( $key );
            if( in_array( $key, self::ALLOWED_PARAMS ) ){
                switch( $key ){
                    case "YEAR":
                        $this->enforceYearValidity( $value );
                        break;
                    case "EPIPHANY":
                        $this->Epiphany         = Epiphany::isValid( strtoupper( $value ) ) ? strtoupper( $value ) : Epiphany::JAN6;
                        break;
                    case "ASCENSION":
                        $this->Ascension        = Ascension::isValid( strtoupper( $value ) ) ? strtoupper( $value ) : Ascension::THURSDAY;
                        break;
                    case "CORPUSCHRISTI":
                        $this->CorpusChristi    = CorpusChristi::isValid( strtoupper( $value ) ) ? strtoupper( $value ) : CorpusChristi::THURSDAY;
                        break;
                    case "LOCALE":
                        $this->Locale           = LitLocale::isValid( strtoupper( $value ) ) ? strtoupper( $value ) : LitLocale::LATIN;
                        break;
                    case "RETURNTYPE":
                        $this->ReturnType       = ReturnType::isValid( strtoupper( $value ) ) ? strtoupper( $value ) : ReturnType::JSON;
                        break;
                    case "NATIONALCALENDAR":
                        $this->NationalCalendar = in_array( strtoupper( $value ), self::SUPPORTED_NATIONAL_CALENDARS ) ? strtoupper( $value ) : null;
                        break;
                    case "DIOCESANCALENDAR":
                        $this->DiocesanCalendar = strtoupper( $value );
                        break;
                }
            }
        }
        if( $this->Locale === null ) {
            if( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
                $value = explode("_", Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']) )[0];
                $this->Locale = LitLocale::isValid( strtoupper( $value ) ) ? strtoupper( $value ) : LitLocale::LATIN;
            } else {
                $this->Locale = LitLocale::LATIN;
            }
        }
    }

    private function enforceYearValidity( int|string $value ) {
        if( gettype( $value ) === 'string' ){
            if( is_numeric( $value ) && ctype_digit( $value ) && strlen( $value ) === 4 ){
                $value = (int)$value;
                if( $value >= self::YEAR_LOWER_LIMIT && $value <= self::YEAR_UPPER_LIMIT ){
                    $this->Year = $value;
                }
            }
        } elseif( gettype( $value ) === 'integer' ) {
            if( $value >= self::YEAR_LOWER_LIMIT && $value <= self::YEAR_UPPER_LIMIT ){
                $this->Year = $value;
            }
        }
    }

}
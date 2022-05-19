<?php
ini_set('date.timezone', 'Europe/Vatican');

include_once( 'enums/LitColor.php' );
include_once( 'enums/LitCommon.php' );
include_once( 'enums/LitFeastType.php' );
include_once( 'enums/LitGrade.php' );

class Festivity implements JsonSerializable
{
    public static $eventIdx = 0;

    public int      $idx;
    public string   $name;
    public DateTime $date;
    public array    $color;
    public string   $type;
    public int      $grade;
    public string   $displayGrade;
    public array    $common;  //"Proper" or specified common(s) of saints...

    /** The following properties are not used in construction, they are only set externally */
    public ?string  $liturgicalYear = null;
    public ?bool    $isVigilMass    = null;
    public ?bool    $hasVigilMass   = null;
    public ?bool    $hasVesperI     = null;
    public ?bool    $hasVesperII    = null;
    public ?int     $psalterWeek    = null;

    function __construct(string $name, DateTime $date, string|array $color = [ '???' ], string $type = '???', int $grade = LitGrade::WEEKDAY, string|array $common = [ '???' ], string $displayGrade='')
    {
        $this->idx          = self::$eventIdx++;
        $this->name         = $name;
        $this->date         = $date; //DateTime object
        if( is_array( $color ) ) {
            if( LitColor::areValid( $color ) ) {
                $this->color = $color;
            }
        }
        else if ( is_string( $color ) ) {
            $_color             = strtolower( $color );
            //the color string can contain multiple colors separated by a comma, when there are multiple commons to choose from for that festivity
            $this->color        = strpos( $_color, "," ) && LitColor::areValid( explode(",", $_color) ) ? explode(",", $_color) : ( LitColor::isValid( $_color ) ? [ $_color ] : [ '???' ] );
        }
        $_type              = strtolower( $type );
        $this->type         = LitFeastType::isValid( $_type ) ? $_type : '???';
        $this->grade        = $grade >= LitGrade::WEEKDAY && $grade <= LitGrade::HIGHER_SOLEMNITY ? $grade : -1;
        $this->displayGrade = $displayGrade;
        //Festivity::debugWrite( "*** Festivity.php *** common vartype = " . gettype( $common ) );
        if( is_string( $common ) ) {
            //Festivity::debugWrite( "*** Festivity.php *** common vartype is string, value = $common" );
            $this->common       = LitCommon::areValid( explode(",", $common) ) ? explode(",", $common) : [];
        }
        else if( is_array( $common ) ) {
            //Festivity::debugWrite( "*** Festivity.php *** common vartype is array, value = " . implode( ', ', $common ) );
            if( LitCommon::areValid( $common ) ) {
                $this->common = $common;
            } else {
                //Festivity::debugWrite( "*** Festivity.php *** common values have not passed the validity test!" );
                $this->common = [];
            }
        }
    }

    private static function debugWrite( string $string ) {
        file_put_contents( "debug.log", $string . PHP_EOL, FILE_APPEND );
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * *
     * Funzione statica di comparazione
     * in vista dell'ordinamento di un array di oggetti Festivity
     * Tiene conto non soltanto del valore della data,
     * ma anche del grado della festa qualora ci fosse una concomitanza
     * * * * * * * * * * * * * * * * * * * * * * * * * */
    public static function comp_date(Festivity $a, Festivity $b) {
        if ( $a->date == $b->date ) {
            if ( $a->grade == $b->grade ) {
                return 0;
            }
            return ( $a->grade > $b->grade ) ? +1 : -1;
        }
        return ( $a->date > $b->date ) ? +1 : -1;
    }

    /* Per trasformare i dati in JSON, dobbiamo indicare come trasformare soprattutto l'oggetto DateTime */
    public function jsonSerialize() : mixed {
        $returnArr = [
            'eventIdx'      => $this->idx,
            'name'          => $this->name,
            'date'          => $this->date->format('U'), //serialize the DateTime object as a PHP timestamp
            'color'         => $this->color,
            'type'          => $this->type,
            'grade'         => $this->grade,
            'displayGrade'  => $this->displayGrade,
            'common'        => $this->common
        ];
        if ( $this->liturgicalYear !== null ) {
            $returnArr['liturgicalYear']    = $this->liturgicalYear;
        }
        if ( $this->isVigilMass !== null ) {
            $returnArr['isVigilMass']       = $this->isVigilMass;
        }
        if ( $this->hasVigilMass !== null ) {
            $returnArr['hasVigilMass']      = $this->hasVigilMass;
        }
        if ( $this->hasVesperI !== null ) {
            $returnArr['hasVesperI']        = $this->hasVesperI;
        }
        if ( $this->hasVesperII !== null ) {
            $returnArr['hasVesperII']       = $this->hasVesperII;
        }
        if ( $this->psalterWeek !== null ) {
            $returnArr['psalterWeek']       = $this->psalterWeek;
        }
        return $returnArr;
    }
}

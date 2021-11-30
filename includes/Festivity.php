<?php
ini_set('date.timezone', 'Europe/Vatican');

include_once( 'enums/LitColor.php' );
include_once( 'enums/LitFeastType.php' );
include_once( 'enums/LitGrade.php' );

class Festivity implements JsonSerializable
{
    public static $eventIdx = 0;

    public int      $idx;
    public string   $name;
    public DateTime $date;
    public string   $color;
    public string   $type;
    public int      $grade;
    public string   $displayGrade;
    public string   $common;  //"Proper" or specified common(s) of saints...

    /** The following properties are not used in construction, they are only set externally */
    public ?string  $liturgicalyear = null;
    public ?bool    $isVigilMass    = null;
    public ?bool    $hasVigilMass   = null;
    public ?bool    $hasVesperI     = null;
    public ?bool    $hasVesperII    = null;
    public ?int     $psalterWeek    = null;

    function __construct(string $name, DateTime $date, string $color = '???', string $type = '???', int $grade = LitGrade::WEEKDAY, string $common = '', string $displayGrade='')
    {
        $this->idx          = self::$eventIdx++;
        $this->name         = $name;
        $this->date         = $date; //DateTime object
        $_color             = strtolower( $color );
        //the color string can contain multiple colors separated by a pipe character, which correspond with the multiple commons to choose from for that festivity
        $this->color        = strpos( $_color, "," ) && LitColor::areValid( explode(",", $_color) ) ? $_color : ( LitColor::isValid( $_color ) ?? $_color );
        $_type              = strtolower( $type );
        $this->type         = LitFeastType::isValid( $_type ) ?? $_type;
        $this->grade        = $grade >= LitGrade::WEEKDAY && $grade <= LitGrade::HIGHER_SOLEMNITY ? $grade : -1;
        $this->displayGrade = $displayGrade;
        $this->common       = $common;
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * *
     * Funzione statica di comparazione
     * in vista dell'ordinamento di un array di oggetti Festivity
     * Tiene conto non soltanto del valore della data,
     * ma anche del grado della festa qualora ci fosse una concomitanza
     * * * * * * * * * * * * * * * * * * * * * * * * * */
    public static function comp_date(Festivity $a, Festivity $b)
    {
        if ( $a->date == $b->date ) {
            if ( $a->grade == $b->grade ) {
                return 0;
            }
            return ( $a->grade > $b->grade ) ? +1 : -1;
        }
        return ( $a->date > $b->date ) ? +1 : -1;
    }

    /* Per trasformare i dati in JSON, dobbiamo indicare come trasformare soprattutto l'oggetto DateTime */
    public function jsonSerialize() {
        $returnArr = [
            'eventidx'      => $this->idx,
            'name'          => $this->name,
            'date'          => $this->date->format('U'), //serialize the DateTime object as a PHP timestamp
            'color'         => $this->color,
            'type'          => $this->type,
            'grade'         => $this->grade,
            'displaygrade'  => $this->displayGrade,
            'common'        => $this->common
        ];
        if ( $this->liturgicalyear !== null ) {
            $returnArr['liturgicalyear']    = $this->liturgicalyear;
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

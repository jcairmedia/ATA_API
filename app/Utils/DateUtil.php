<?php

namespace App\Utils;

class DateUtil
{
    private $array_month = [];

    public function __construct()
    {
        $this->array_months = ['01' => 'Enero',
        '02' => 'Febrero',
        '03' => 'Marzo',
        '04' => 'Abril',
        '05' => 'Mayo',
        '06' => 'Junio',
        '07' => 'Julio',
        '08' => 'Agosto',
        '09' => 'Septiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre', ];
    }

    public function getNameMonth(string $month_number)
    {
        return $this->array_months[$month_number];
    }

    public function getNameMonthByDate(string $date)
    {
        $dt = new \DateTime($date);
        $month = $dt->format('m');

        return $this->array_months[$month];
    }

    public function getDayByDate(string $date)
    {
        $dt = new \DateTime($date);

        return $dt->format('d');
    }

    public function getYearByDate(string $date)
    {
        $dt = new \DateTime($date);

        return $dt->format('Y');
    }

    public function getTime(string $date)
    {
        $dt = new \DateTime($date);

        return $dt->format('H:i:s');
    }

    public function getTimeWithMeridian(string $date)
    {
        $dt = new \DateTime($date);

        return $dt->format('h:i:s a');
    }
}

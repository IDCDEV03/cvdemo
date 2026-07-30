<?php
use Carbon\Carbon;

if (!function_exists('thai_datetime')) {
    /** * @param string|Carbon $datetime
     * @param bool $fullMonth (true = เดือนเต็ม, false = เดือนย่อ)
     * @return string
     */
    function thai_datetime($datetime, $fullMonth = false)
    {
        $carbon = $datetime instanceof Carbon ? $datetime : Carbon::parse($datetime);
        $carbon->locale('th');
        $buddhistYear = $carbon->year + 543;

        // เช็คเงื่อนไข: ถ้า $fullMonth เป็น true ใช้ 'F' (เดือนเต็ม), ถ้า false ใช้ 'M' (เดือนย่อ)
        $monthFormat = $fullMonth ? 'F' : 'M';

        return $carbon->format('d') . ' ' .
               $carbon->translatedFormat($monthFormat) . ' ' .
               $buddhistYear . ' เวลา ' .
               $carbon->format('H:i') . ' น.';
    }
}

if (!function_exists('thai_date')) {
    /** * @param string|Carbon $date
     * @param bool $fullMonth (true = เดือนเต็ม, false = เดือนย่อ)
     * @return string
     */
    function thai_date($date, $fullMonth = false)
    {
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $carbon->locale('th');
        $buddhistYear = $carbon->year + 543;

        // เช็คเงื่อนไข: ถ้า $fullMonth เป็น true ใช้ 'F' (เดือนเต็ม), ถ้า false ใช้ 'M' (เดือนย่อ)
        $monthFormat = $fullMonth ? 'F' : 'M';

        return $carbon->format('d') . ' ' . 
               $carbon->translatedFormat($monthFormat) . ' ' . 
               $buddhistYear;
    }
}
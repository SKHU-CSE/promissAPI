<?php
/**
 * Created by PhpStorm.
 * User: jaeryo
 * Date: 2019-06-07
 * Time: 13:13
 */
namespace App\Service;
class GpsService
{
    public static function deg2rad($deg)
    {
        $radians = $deg * M_PI / 180.0;
        return ($radians);
    }
    public static function geoDistance($lat1, $lon1, $lat2, $lon2, $unit="k") {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtolower($unit);
        if ($unit == "k") {
            return ($miles * 1.609344);
        } else {
            return $miles;
        }
    }
}

<?php // Code within app\Helpers\Helper.php
namespace App\Helpers;

use Carbon\Carbon;

class Helper
{
    public static function formatDate($date)
    {
        try {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            // Handle the exception if the date parsing fails
            return null; // or handle it as needed
        }
    }
}
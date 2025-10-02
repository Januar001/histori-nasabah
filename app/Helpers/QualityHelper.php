<?php

namespace App\Helpers;

class QualityHelper
{
    public static function getQualityLabel($numericValue)
    {
        $qualityMap = [
            '1' => 'LANCAR',
            '2' => 'DALAM PERHATIAN KHUSUS',
            '3' => 'KURANG LANCAR',
            '4' => 'DIRAGUKAN',
            '5' => 'MACET'
        ];

        return $qualityMap[$numericValue] ?? $numericValue;
    }

    public static function getQualityBadge($numericValue)
    {
        $badgeMap = [
            '1' => 'bg-success',
            '2' => 'bg-info',
            '3' => 'bg-warning',
            '4' => 'bg-custom-orange',
            '5' => 'bg-danger'
        ];

        return $badgeMap[$numericValue] ?? 'bg-secondary';
    }

    public static function getQualityColor($numericValue)
    {
        $colorMap = [
            '1' => 'success',
            '2' => 'info',
            '3' => 'warning',
            '4' => 'custom-orange',
            '5' => 'danger'
        ];

        return $colorMap[$numericValue] ?? 'secondary';
    }

    public static function isImprovement($before, $after)
    {
        return $after < $before;
    }

    public static function isWorsening($before, $after)
    {
        return $after > $before;
    }

    public static function getAllQualityOptions()
    {
        return [
            '1' => '1 - LANCAR',
            '2' => '2 - DALAM PERHATIAN KHUSUS',
            '3' => '3 - KURANG LANCAR',
            '4' => '4 - DIRAGUKAN',
            '5' => '5 - MACET'
        ];
    }

    public static function getTunggakanColor($value)
    {
        if ($value == 0) {
            return 'bg-success'; // hijau
        } elseif ($value == 1) {
            return 'bg-warning text-dark'; // kuning
        } elseif ($value >= 2) {
            return 'bg-danger'; // merah
        } else {
            return 'bg-secondary'; // default
        }
    }

}
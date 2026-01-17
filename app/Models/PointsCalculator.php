<?php


namespace App\Models;

class PointsCalculator {
    
    public static function calculate($purchaseAmount) {
        $pointsPer100 = 10;
        return floor($purchaseAmount / 100) * $pointsPer100;
    }
    
    
    public static function canRedeem($userPoints, $requiredPoints) {
        return $userPoints >= $requiredPoints;
    }
    
    
    public static function calculateExpiringPoints($transactions) {
        
        $expiring = 0;
        $oneYearAgo = date('Y-m-d', strtotime('-1 year'));
        
        foreach ($transactions as $transaction) {
            if ($transaction['type'] === 'earned' && 
                $transaction['created_at'] < $oneYearAgo) {
                $expiring += $transaction['amount'];
            }
        }
        
        return $expiring;
    }
}
<?php
namespace Services;

class PointsCalculator {
    const POINTS_PER_DOLLAR = 10;
    const DOLLAR_THRESHOLD = 100;
    
    public function calculatePoints(float $amount): int {

    return floor($amount / self::DOLLAR_THRESHOLD) * self::POINTS_PER_DOLLAR;
    }
    
    public function validateRedemption(int $userPoints, int $requiredPoints): bool {
        return $userPoints >= $requiredPoints;
    }
}
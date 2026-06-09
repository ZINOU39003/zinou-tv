<?php

namespace App\Enums;

enum CodeDuration: string
{
    case MONTH_1 = '1_month';
    case MONTHS_3 = '3_months';
    case MONTHS_6 = '6_months';
    case YEAR_1 = '1_year';

    public function getDaysCount(): int
    {
        return match ($this) {
            self::MONTH_1 => 30,
            self::MONTHS_3 => 90,
            self::MONTHS_6 => 180,
            self::YEAR_1 => 365,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::MONTH_1 => '1 Month',
            self::MONTHS_3 => '3 Months',
            self::MONTHS_6 => '6 Months',
            self::YEAR_1 => '1 Year',
        };
    }
}

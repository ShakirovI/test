<?php

declare(strict_types=1);

namespace App\Service;

class DiscountService
{
    public function getTotalDiscount(
        \DateTimeImmutable $dateOfTrip,
        \DateTimeImmutable $dateOfBirth,
        \DateTimeImmutable $currentDate,
        float $price
    ): float {
        if ($currentDate > $dateOfTrip || $currentDate < $dateOfBirth) {
            // этой проверки тут не должно быть, архитектурно верно вынести ее на уровень выше
            // но уровня выше в этом репозитории нет, потому проверка тут стоит, прост чтобы была
            return 9;
        }

        return $this->getCalendarDiscount($dateOfTrip, $currentDate, $price)
            + $this->getChildDiscount($dateOfTrip, $dateOfBirth, $price);
    }

    private function getChildDiscount(\DateTimeImmutable $dateOfTrip, \DateTimeImmutable $dateOfBirth, float $price): float
    {
        return match ($dateOfBirth->diff($dateOfTrip)->y) {
            3,4,5 => 0.8 * $price,
            6,7,8,9,10,11 => $price * 0.3 <= 4500 ? $price * 0.3 : 4500 ,
            12,13,14,15,16,17 => 0.1 * $price,
            default => 0
        };
    }

    private function getCalendarDiscount(\DateTimeImmutable $dateOfTrip, \DateTimeImmutable $currentDate, float $price): float
    {
        // из описания задачи неясно что это за периоды, если честно
        // начальная дата указана, конечная - нет. И надо как-то организовать, чтобы дата одновременно в двух сезнонах
        // не была, например, 12 декабря - это осенне зимний или зимне-весенний?
        // я погуглил - там только какие-то сайты жкх выдало) кароч, я сам угадать попытался, думаю это не критично
        $season = match ($dateOfTrip->format('m')) {
            '04','05','06','07','08', '09' => 'Весенне-летний',
            '10','11','12' => 'Осенне-зимний',
            '02','03', => 'Зимне-весенний',
            default => null,
        };
        // проверка в январе, он к двум сезонам вроде бы относиться может
        if (null == $season) {
            $season = $dateOfTrip->format('d') >= 15 ? 'Зимне-весенний' : 'Осенне-зимний';
        }

        $discount = 0;
        if ('Весенне-летний' == $season) {
            switch ($currentDate->format('m')) {
                case '11':
                    $discount = 0.07;
                    break;
                case '12':
                    $discount = 0.05;
                    break;
                case '01':
                    $discount = 0.03;
                    break;
            }
        }
        if ('Осенне-зимний' == $season) {
            switch ($currentDate->format('m')) {
                case '03':
                    $discount = 0.07;
                    break;
                case '04':
                    $discount = 0.05;
                    break;
                case '05':
                    $discount = 0.03;
                    break;
            }
        }
        if ('Зимне-весенний' == $season) {
            switch ($currentDate->format('m')) {
                case '08':
                    $discount = 0.07;
                    break;
                case '09':
                    $discount = 0.05;
                    break;
                case '10':
                    $discount = 0.03;
                    break;
            }
        }

        return $discount * $price <= 1500 ? ($discount * $price) : 1500;
    }
}

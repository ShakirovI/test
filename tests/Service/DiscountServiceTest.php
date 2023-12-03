<?php
// tests/Service/DiscountServiceTest.php

namespace App\Tests\Service;

use App\Service\DiscountService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class DiscountServiceTest extends KernelTestCase
{

    public function calendarDiscountDataProvider(): array
    {
        return [
            //скидка по дате рождения есть, по бронированию заранее нет
            [new DateTimeImmutable('2023-09-01'), new DateTimeImmutable('2019-01-01'), new DateTimeImmutable('2023-05-01'),  1000, 800],
            [new DateTimeImmutable('2023-09-01'), new DateTimeImmutable('2016-05-01'), new DateTimeImmutable('2023-05-01'),  1000, 300],
            [new DateTimeImmutable('2023-09-01'), new DateTimeImmutable('2016-05-01'), new DateTimeImmutable('2023-05-01'),  100000, 4500],
            [new DateTimeImmutable('2023-09-01'), new DateTimeImmutable('2010-05-01'), new DateTimeImmutable('2023-05-01'),  1000, 100],
            [new DateTimeImmutable('2023-09-01'), new DateTimeImmutable('2000-05-01'), new DateTimeImmutable('2023-05-01'),  1000, 0],

            //скидка по дате рождения - нет, по бронированию есть
            [new DateTimeImmutable('2023-04-01'), new DateTimeImmutable('2000-05-01'), new DateTimeImmutable('2022-12-01'),  1000, 50],
            [new DateTimeImmutable('2023-10-01'), new DateTimeImmutable('2000-05-01'), new DateTimeImmutable('2023-03-01'),  1000, 70],
            [new DateTimeImmutable('2023-01-15'), new DateTimeImmutable('2000-05-01'), new DateTimeImmutable('2022-09-01'),  1000, 50],

            //скидка по дате + по бронированию
            [new DateTimeImmutable('2023-04-01'), new DateTimeImmutable('2010-05-01'), new DateTimeImmutable('2022-12-01'),  1000, 150],
            [new DateTimeImmutable('2023-10-01'), new DateTimeImmutable('2019-05-01'), new DateTimeImmutable('2023-03-01'),  1000, 870],


        ];
    }



    /**
     * @dataProvider calendarDiscountDataProvider
     */
    public function testGetChildDiscount(
        DateTimeImmutable $dateOfTrip,
        DateTimeImmutable $dateOfBirth,
        DateTimeImmutable $currentDate,
        float $price,
        float $expectedDiscount
    )
    {
        self::bootKernel();
        $container = static::getContainer();
        $discountService = $container->get(DiscountService::class);

        echo (new DateTimeImmutable())->format('m');
        /** @var DiscountService $discount */
        $discount = $discountService->getTotalDiscount($dateOfTrip, $dateOfBirth, $currentDate, $price);


        $this->assertEquals($expectedDiscount, $discount);
    }

}

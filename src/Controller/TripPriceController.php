<?php
declare(strict_types=1);
namespace App\Controller;

use App\Service\DiscountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeImmutable;
use OpenApi\Attributes as OA;

class TripPriceController extends AbstractController
{
    public function __construct(
        private readonly DiscountService $discountService
    )
    {
    }

    #[Route('/api/price', methods: ['GET'])]
    #[OA\QueryParameter(name: 'dateOfTrip', in: 'query', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\QueryParameter(name: 'dateOfBirth', in: 'query', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\QueryParameter(name: 'price', in: 'query', required: true, schema: new OA\Schema(type: 'int'))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'ok',
        content: new OA\JsonContent(
            examples: [
                new OA\Examples(
                    example: 'trip price',
                    summary: 'trip pice with discount',
                    value: [
                        'errors' => [],
                        'response' => [
                            'tripPriceWithDiscount' => 90,
                        ]
                    ]
                ),
            ])
    )]
    public function getPrice(Request $request) : JsonResponse
    {
        $queryParams = [];
        parse_str($request->getQueryString(), $queryParams);

        $result = $queryParams['price'] - $this->discountService->getTotalDiscount(
            DateTimeImmutable::createFromFormat('Y-m-d',$queryParams['dateOfTrip'] )->setTime(0, 0),
            DateTimeImmutable::createFromFormat('Y-m-d',$queryParams['dateOfBirth'] )->setTime(0, 0),
            new DateTimeImmutable(),
            (float)$queryParams['price']
            );

        return $this->json(['status' => Response::HTTP_OK, 'content' => ['tripPriceWithDiscount'=> $result]]);
    }

}
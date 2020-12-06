<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Restaurant;

/**
  * @IsGranted("IS_AUTHENTICATED_FULLY")
  */
class ApiRestaurantTableController extends AbstractController
{
    /**
     * @Route("/api/restaurant/table/{restaurant}", name="api_restaurant")
     */
    public function index(Restaurant $restaurant): Response
    {
        if ($restaurant->getUser() !== $this->getUser()) {
            throw new \Exception($this->translator->trans('Wrong restaurant ID'));
        }
        
        $restaurantTables = $restaurant->getTables()->toArray();
        
        if (!empty($restaurantTables)) {
            $encoder = new JsonEncoder();
            $defaultContext = [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                    return $object->getId();
                },
            ];
            $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
            $serializer = new Serializer([$normalizer], [$encoder]);
            $content = $serializer->serialize($restaurantTables, 'json');
        } else {
            $content = $this->json($restaurantTables);
        }
        
        $response = new Response(
            $content,
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );

        return $response;
    }
}

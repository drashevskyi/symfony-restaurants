<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\RestaurantType;
use App\Entity\Restaurant;
use App\Entity\RestaurantTable;
use App\Service\RestaurantService;

/**
  * @IsGranted("IS_AUTHENTICATED_FULLY")
  */
class RestaurantController extends AbstractController
{
    const PAGINATOR_START_PAGE = 1;
    const PAGINTATOR_ROWS_PER_PAGE = 5;
    
    /**
     * @var TranslatorInterface
     */
    private $translator;
    
    /**
     * @var RestaurantService
     */
    private $restaurantService;

    /**
     * @param TranslatorInterface $translator
     * @param RestaurantService   $restaurantService
     */
    public function __construct(TranslatorInterface $translator, RestaurantService $restaurantService)
    {
        $this->translator = $translator;
        $this->restaurantService = $restaurantService;
    }
    
    /**
    * @Route("/", name="index_restaurant")
     * @Route("/restaurant", name="restaurant")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $title = $request->query->get('filterValue'); //knp paginator filter
        $restaurants = $this->getDoctrine()->getRepository(Restaurant::class)
            ->findRestaurantsBy($title, $this->getUser());
        $pagination = $paginator->paginate(
            $restaurants,
            $request->query->getInt('page', self::PAGINATOR_START_PAGE), self::PAGINTATOR_ROWS_PER_PAGE);

        return $this->render('restaurant/index.html.twig', ['pagination' => $pagination]);
    }
    
    /**
     * @Route("/restaurant/add", name="restaurant_add", methods={"GET", "POST"})
     */
    public function add(Request $request): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->restaurantService->saveRestaurant($restaurant, $form->get('photo')->getData());
            $this->addFlash('success', $this->translator->trans('Restaurant created'));

            return $this->redirectToRoute('restaurant');
        }
        
        return $this->render('restaurant/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/restaurant/update/{restaurant}", name="restaurant_update", methods={"PUT"})
     */
    public function update(Restaurant $restaurant, Request $request): Response
    {
        //to check if restaurant belongs to the same user
        if ($restaurant->getUser() !== $this->getUser()) {
            throw new \Exception($this->translator->trans('Wrong restaurant ID'));
        }
        
        $currentMaxActiveTables = $restaurant->getMaxActiveTables();//getting value before the request is handled
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $updatedMaxActiveTables = $form->get('max_active_tables')->getData();
            //if number of max active tables was decreased
            if ($updatedMaxActiveTables < $currentMaxActiveTables) {
                //to get currently active tables count
                $activeTablesCount = $this->getDoctrine()->getRepository(RestaurantTable::class)
                    ->countRestaurantActiveTables($restaurant);
                //if there are more currently active tables than the updated amount
                if ($activeTablesCount > $updatedMaxActiveTables) {
                    $this->addFlash("warning", $this->translator->trans('restaurant has active tables').' '.$restaurant->getId());

                    return $this->redirectToRoute('restaurant_update', ['restaurant' => $restaurant->getId()]);
                }
            }
            
            $this->restaurantService->saveRestaurant($restaurant, $form->get('photo')->getData(), false);
            $this->addFlash('success', $this->translator->trans('Restaurant updated'));

            return $this->redirectToRoute('restaurant');
        }
        
        return $this->render('restaurant/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/restaurant/delete/{restaurant}", name="restaurant_delete", methods={"GET"})
     */
    public function delete(Restaurant $restaurant, Request $request): Response
    {
        if ($restaurant->getUser() !== $this->getUser()) {
            throw new \Exception($this->translator->trans('Wrong restaurant ID'));
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($restaurant);
        $em->flush();
        $this->addFlash('success', $this->translator->trans('Restaurant deleted'));
        
        return $this->redirectToRoute('restaurant');
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Restaurant;
use App\Entity\RestaurantTable;
use App\Form\RestaurantTableType;

 /**
  * @IsGranted("IS_AUTHENTICATED_FULLY")
  */
class RestaurantTableController extends AbstractController
{
    const PAGINATOR_START_PAGE = 1;
    const PAGINTATOR_ROWS_PER_PAGE = 5;
    
    /**
     * @var TranslatorInterface
     */
    private $translator;
    
    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * @Route("/restaurant/table/{restaurant}", name="restaurant_table")
     */
    public function index(Restaurant $restaurant, Request $request, PaginatorInterface $paginator): Response
    {
        //to check if user visiting his restaurant tables
        if ($restaurant->getUser() !== $this->getUser()) {
            throw new \Exception($this->translator->trans('Wrong restaurant ID'));
        }
        
        $restaurantTables = $this->getDoctrine()->getRepository(RestaurantTable::class)
            ->findBy(['restaurantId' => $restaurant->getId()]);
        $pagination = $paginator->paginate(
            $restaurantTables,
            $request->query->getInt('page', self::PAGINATOR_START_PAGE), self::PAGINTATOR_ROWS_PER_PAGE);
        
        return $this->render('restaurant_table/index.html.twig', [
            'restaurant' => $restaurant,
            'pagination' => $pagination
        ]);
    }
    
    /**
     * @Route("/restaurant/table/{restaurant}/add", name="restaurant_table_add", methods={"GET", "POST"})
     */
    public function add(Request $request, Restaurant $restaurant): Response
    {
        if ($restaurant->getUser() !== $this->getUser()) {
            throw new \Exception($this->translator->trans('Wrong restaurant ID'));
        }
        
        $restaurantTable = new RestaurantTable();
        $form = $this->createForm(RestaurantTableType::class, $restaurantTable);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $statusActive = $form->getData()->getStatus();
            //if table status is set to active
            if ($statusActive) {
                $restaurantActiveTablesCount = $em->getRepository(RestaurantTable::class)
                    ->countRestaurantActiveTables($restaurant);
                //if after creation there will be more active tables than max amount for restaurant
                if (($restaurantActiveTablesCount + 1) > $restaurant->getMaxActiveTables()) {
                    $this->addFlash("warning", $this->translator->trans('max active tables reached')." ".$restaurant->getId());

                    return $this->redirectToRoute('restaurant_table_add', ['restaurant' => $restaurant->getId()]);
                }
            }
            
            $restaurantTable->setRestaurant($restaurant);
            $em->persist($restaurantTable);
            $em->flush();
            $this->addFlash('success', $this->translator->trans('table created'));

            return $this->redirectToRoute('restaurant_table', ['restaurant' => $restaurant->getId()]);
        }
       
        return $this->render('restaurant_table/add.html.twig', [
            'form' => $form->createView(),
            'restaurant' => $restaurant
        ]);
    }
    
    /**
     * @Route("/restaurant/table/update/{restaurantTable}", name="restaurant_table_update", methods={"PUT"})
     */
    public function update(RestaurantTable $restaurantTable, Request $request): Response
    {
        $restaurant = $restaurantTable->getRestaurant();
        
        if ($restaurant->getUser() !== $this->getUser()) {
            throw new \Exception($this->translator->trans('Wrong restaurant ID'));
        }
        
        $currentStatusActive = $restaurantTable->getStatus();//getting value before request is handled
        $form = $this->createForm(RestaurantTableType::class, $restaurantTable);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $updatedStatusActive = $form->getData()->getStatus();
            //if table status was changed from inactive to active
            if (!$currentStatusActive && $updatedStatusActive) {
                $restaurantActiveTablesCount = $em->getRepository(RestaurantTable::class)
                    ->countRestaurantActiveTables($restaurant);
                //if after update number of active tables will be bigger than set for restaurant
                if (($restaurantActiveTablesCount + 1) > $restaurant->getMaxActiveTables()) {
                    $this->addFlash("warning", "Maximum active tables for restaurant ID: ".$restaurant->getId()." reached.");

                    return $this->redirectToRoute('restaurant_table_update', ['restaurantTable' => $restaurantTable->getId()]);
                }
            }
            
            $em->persist($restaurantTable);
            $em->flush();
            $this->addFlash('success', 'Table ID: '.$restaurantTable->getId().' updated.');

            return $this->redirectToRoute('restaurant_table', ['restaurant' => $restaurant->getId()]);
        }
        
        return $this->render('restaurant_table/edit.html.twig', [
            'form' => $form->createView(),
            'restaurant' => $restaurant,
            'restaurantTable' => $restaurantTable
        ]);
    }
    
    /**
     * @Route("/restaurant/table/delete/{restaurantTable}", name="restaurant_table_delete", methods={"GET"})
     */
    public function delete(RestaurantTable $restaurantTable): Response
    {
        $restaurant = $restaurantTable->getRestaurant();
        
        if ($restaurant->getUser() !== $this->getUser()) {
            throw new \Exception($this->translator->trans('Wrong restaurant ID'));
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($restaurantTable);
        $em->flush();
        $this->addFlash('success', 'Table ID: '.$restaurantTable->getId().' deleted.');
        
        return $this->redirectToRoute('restaurant_table', ['restaurant' => $restaurant->getId()]);
    }
}

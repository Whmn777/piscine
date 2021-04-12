<?php


namespace App\Controller\EventCreator;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class PageController extends AbstractController
{
    /**
     * @Route("/EventCreator/home", name="EventCreator_home")
     */



    public function listEvents(EventRepository $eventRepository)
    {

        $events = $eventRepository->findAll();




        return $this->render('EventCreator/home.html.twig', [
            'events' => $events
        ]);
    }
    /**
     * @Route("/EventCreator/home", name="EventCreator_home")
     */
    /*public function showEventRecent( EventRepository $eventRepository)

    {
        //Je récupère mes events publiés selon les critères :
        $eventRecents = $eventRepository ->find(['nom' => 'string'],['id' => 'DESC'],2);


        return $this->render('EventCreator/home.html.twig',[
            'eventRecents' => $eventRecents
        ]);
    }*/
}
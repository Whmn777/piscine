<?php


namespace App\Controller\EventCreator;


use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\FilesServices\FilePersister;

class EventController extends AbstractController
{
    /**
     * @Route("/EventCreator/events", name="EventCreator_list_events")
     */



    public function listEvents(EventRepository $eventRepository)
    {

        $events = $eventRepository->findAll();




        return $this->render('EventCreator/list_events.html.twig', [
            'events' => $events
        ]);
    }



    /**
     * @Route("/EventCreator/events/search", name="EventCreator_search_events")
     */
    public function searchEvents(Request $request, EventRepository $eventRepository)
    {
        // faire une recherche en base de données avec ce paramètre

        $search = $request->query->get('search'); // avec la class Request, je récupère la valeur dans l'url

        //Je récupère tous mes events ($events) contenant la valeur saisie de par l'utilisateur dans l'input = $search
        // dans ma BBD,
        // avec la méthode searchByTerm de la class EventRepository (que je vais créer),

        $events = $eventRepository->searchByTerm($search);

        return $this->render('EventCreator/home.html.twig', [
            "events" => $events
        ]);
    }



    /**
     * @route("/EventCreator/events/insert", name="EventCreator_event_insert")
     */

    public function insertEvent(
        EntityManagerInterface $entityManager,
        Request $request,
        FilePersister $filePersister
    )
    {
        //Dans la variable $event, j'instancie un nouvel objet newEvent de la classe entité Event.
        //Je peux ainsi redéfinir à ma class entité des nouvelles propriétés, qui seront insérer comme des
        //nouveaux champs sur ma table event dans ma BDD.
        $event = new Event();

        //Je stocke mon gabarit de formulaire dans la variable $eventForm :
        //avec la méthode creatForm() de l'AbstractController je crée une nouvelle instance
        //et lui passe en paramètres ma class EventType (qui contient mon gabarit de formulaire)
        //et $event, le nouvel event à créer.
        $eventForm = $this->createForm(EventType::class, $event);


        //Je récupère les données entrées par l'utilisateur dans le formulaire, avec la class Request
        //Je le lie ces données à ma variable $eventForm
        $eventForm->handleRequest($request);

        //Erreur 404 au cas où $event a une valeur nulle :
        if(is_null($event))
        {
            throw $this->createNotFoundException('Evénement non trouvé.');
        }
        //Si les données du formulaire sont envoyées et qu'elles sont valides
        if($eventForm->isSubmitted() && $eventForm->isValid())
        {

            //je les récupère avec la méthode getData() da la class FormInterface dans mon entité $event
            $event = $eventForm->getData();


            //J'utilise ma méthode saveFile() de ma class FilePersistor que j'ai crée dans un service.
            $filePersister->saveFile($event, $eventForm);



            /*Upload d'image avant refactorisation dans un service : FilesServices
             * Je récupère les données de mon image rentrée par l'utilisateur dans le formulaire d'insertion :

            $image = $eventForm->get('image')->getData();


            //Si des données ont bien étaient rentrées :
            if($image)
            {
                //Je récupère l'image uploadé par l'utilisateur et la stocke dans un dossier temporaire :
                // chemin vers le dossier temporaire de l'image
                $originalImage = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                //Pour cela avec la méthode slug() de la class SluggerInterface
                //Je crée un slugg de mon nom d'image.
                $safeImage = $slugger->slug($originalImage);

                //Je stocke ce slugg dans une variable $newImage, pour laquelle j'instancie une nouvelle extension unique avec les méthodes :
                //uniqid() => pour générer un nouvel id
                //guessExtension()
                $newImage = $safeImage.'-'.uniqid().'.'.$image->guessExtension();


                // Je lance un test  : si mon nom d'image a bien été saisi
                //et qu'il a bien bien été renommé avec un nom unique
                //donc je le déplace avec la méthode move()
                //dans mon dossier image (dans le dossier public => chemin gérer avec security yaml)
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newImage

                    );

                //Je renvoie un message d'erreur si l'image n'a pu être enregistrée dans mon dossier images :

                } catch (FileException $e) {
                    throw new \Exception("l'image n'a pas été enregistrée");
                }*/

            //Avec mon setteur, j'enregistre ma nouvelle image dans l'entité $event que j'ai instancié :
            //$event->setImage($newFile);





            //J'envoie et enregistre tout en BDD.
            $entityManager->persist($event);
            $entityManager->flush();

            // après suppression d'un event, j'ajoute un message flash (dans la session)
            // pour l'afficher sur la prochaine page
            $this->addFlash("success", "L'événement ". $event->getNom() ." a bien été chargé !");

            return $this->redirectToRoute('EventCreator_home');
        }

        //Je retourne ma méthode insertEvent() pour l'afficher avec la méthode render():
        //avec la méthode createView() je crée une vue de $eventForm que pourra lire twig
        //afin de pouvoir afficher mon formulaire grâce à la variable "eventFormView"

        return $this->render("EventCreator/insert_event.html.twig", [
            "eventFormView" => $eventForm->createView()

        ]);

    }

    //Je crée une méthode publique eventShow ayant pour paramètres, la valeur de la wild card : {id}
    // l'injection de dépendances (class) ou encore l'autowire ayant pour class EventRepository
    // et pour valeur $eventRepository

    /**
     * @Route("/EventCreator/events/{id}", name="EventCreator_event_show")
     */
    public function eventShow($id, EventRepository $eventRepository)
    {
        //Je récupère avec $event, tous mes events dans ma BBD grâce à leur id
        // avec la méthode find de la class EventRepository,

        $event = $eventRepository->find($id);//find() de la class EventRepository
        //(qui me permet de générer des requêtes en BDD);


        //(return) J'envoie donc cette requête ( => SELECT id FROM event)
        //pour (=$this) la  faire afficher grâce à la méthode render() de ma class AbstractController.
        //Cette méthode render() aura pour paramètres : la vue (ou affichage sur le navigateur) et un tableau
        //ayant pour index 'event' (que je rappellerai dans dans ma vue) ayant pour valeur: $event
        // (le résultat de ma requête)
        return $this->render('EventCreator/eventShow.html.twig', [
            'event' => $event
        ]);
    }

    /**
     * @route("/EventCreator/events/update/{id}", name ="EventCreator_update_event")
     */

    public function updateEvent(
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        Request $request,
        $id)
    {
        //Je récupère l'event à modifier avec son {id};
        $event = $eventRepository -> find($id);

        $eventForm = $this->createForm(EventType::class, $event);

        //Je récupère les données entrées par l'utilisateur dans le formulaire, avec la class Request
        //Je le lie ces données à ma variable $eventForm
        $eventForm->handleRequest($request);

        //Erreur 404 au cas où $event a une valeur nulle :
        if(is_null($event))
        {
            throw $this->createNotFoundException('Event non trouvé.');
        }

        if($eventForm->isSubmitted() && $eventForm->isValid())
        {
            $event = $eventForm->getData();

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash("success", "L'événement ". $event->getNom() ." a bien été chargé !");

            return $this->redirectToRoute('EventCreator_home');
        }


        return $this->render("EventCreator/update_event.html.twig",[
            "eventFormView" => $eventForm->createView()
        ]);
    }






    /**
     * @Route("/EventCreator/events/delete/{id}", name="EventCreator_delete_event")
     */
    public function deleteEvent($id, EventRepository $eventRepository, EntityManagerInterface $entityManager)
    {
        $event = $eventRepository->find($id);

        if (is_null($event)) {
            throw $this->createNotFoundException('event non trouvée');
        }

        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('success', "l'événement ". $event->getNom() ." a bien été supprimé");



        return $this->redirectToRoute('EventCreator_home');
    }

}
<?php

namespace App\Controller\EventCreator;


use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository ;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{

    /**
     * @Route("/EventCreator/categories", name="EventCreator_list_categories")
     */
    public function listCategory(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render('EventCreator/list_categories.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/EventCreator/categories/search", name="EventCreator_search_categories")
     */
    public function searchCategories(Request $request, CategoryRepository $categoryRepository)
    {
        $search = $request->query->get('search');

        $categories = $categoryRepository->searchByTerm($search);

        return $this->render('EventCreator/home.html.twig', [
            "categories" => $categories
        ]);
    }


    /**
     * @route("/EventCreator/categories/insert", name="EventCreator_insert_categorie")
     */

    public function insertCategorie(EntityManagerInterface $entityManager, Request $request)
    {
        $categorie = new Category();

        $categorieForm = $this->createForm(CategoryType::class, $categorie);

        $categorieForm->handleRequest($request);

        if($categorieForm->isSubmitted() && $categorieForm->isValid()){

            $categorie = $categorieForm->getData();

            $entityManager->persist($categorie);
            $entityManager->flush();
        }

        return $this->render("EventCreator/insert_categorie.html.twig", [
            "categorieFormView" => $categorieForm->createView()
        ]);

    }

    /**
     * @Route("/EventCreator/categories/{id}", name="EventCreator_show_categorie")
     */
    public function categorieShow($id, CategoryRepository $categoryRepository)
    {

        $categorie = $categoryRepository->find($id);

        return $this->render('EventCreator/show_categorie.html.twig', [
            'categorie' => $categorie
        ]);
    }

    /**
     * @route("/EventCreator/categories/update/{id}", name ="EventCreator_update_categorie")
     */
    public function updateCategorie(
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        Request $request,
        $id)
    {

        $categorie = $categoryRepository -> find($id);

        //Erreur 404 au cas où $article a une valeur nulle :
        if(is_null($categorie))
        {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        $categorieForm = $this->createForm(CategoryType::class, $categorie);

        $categorieForm->handleRequest($request);

        if($categorieForm->isSubmitted() && $categorieForm->isValid())
        {
            $categorie = $categorieForm->getData();

            $entityManager->persist($categorie);
            $entityManager->flush();
        }

        return $this->render("EventCreator/insert_categorie.html.twig",[
            "categorieFormView" => $categorieForm->createView()
        ]);


    }

    /**
     * @Route("/EventCreator/categories/delete/{id}", name="EventCreator_delete_categorie")
     */
    public function deleteCategorie($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {
        $categorie = $categoryRepository->find($id);

        if (is_null($categorie)) {
            throw $this->createNotFoundException('catégorie non trouvée');
        }

        $this->addFlash('success', "Cette catégorie a bien été supprimé");


        $entityManager->remove($categorie);
        $entityManager->flush();


        return $this->render('EventCreator/delete_categorie.html.twig.html.twig
        ');
        //return $this->redirectToRoute('EventCreator_insert_categorie');
    }

}
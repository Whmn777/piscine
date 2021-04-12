<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function searchByTerm($search)
    {
        //Je crée une nouvelle requête $queryBuilder pour récupérer toutes les catégories
        // contenant la valeur de notre input de recherche grâce à la méthode createQueryBuilder()

        //de la class EntityRepository:

        $queryBuilder = $this->createQueryBuilder("c"); //Je lui attribue un alias en paramètre

        //Je stocke cette nouvelle requête de recherche dans une variable $query et en y définnisant les termes :
        $query = $queryBuilder
            ->select('c')
            ->where('c.nom LIKE :search')
            //SELECT e.* FROM myclass AS e WHERE e.content LIKE :search'

            //Avec Doctrine on lie les paramètres à notre requête (liaison dynamique) => préparation de requête pour
            //plus de sécurité
            //L'appel de setParameter () déduit automatiquement le type que nous définissons comme valeur.
            ->setParameter('search','%'.$search.'%')
            ->getQuery('');//on récupère notre requête.

        return $query->getResult();//on retourne le résultat de cette dernière.
    }

    // /**
    //  * @return Category[] Returns an array of Category objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

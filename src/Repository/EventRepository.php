<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function searchByTerm($search)
    {
        //Je crée une nouvelle requête $queryBuilder pour récupérer tous les events
        // contenant la valeur de notre input de recherche grâce à la méthode createQueryBuilder()

        //de la class EntityRepository:

        $queryBuilder = $this->createQueryBuilder("e"); //Je lui attribue un alias en paramètre

        //Je stocke cette nouvelle requête de recherche dans une variable $query et en y définnisant les termes :
        $query = $queryBuilder
            ->select('e')
            ->where('e.nom LIKE :search')
            //SELECT e.* FROM myclass AS e WHERE e.content LIKE :search'

            //Avec Doctrine on lie les paramètres à notre requête (liaison dynamique) => préparation de requête pour
            //plus de sécurité
            //L'appel de setParameter () déduit automatiquement le type que nous définissons comme valeur.
            ->setParameter('search','%'.$search.'%')
            ->getQuery('');//on récupère notre requête.

        return $query->getResult();//on retourne le résultat de cette dernière.
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\DataColumn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DataColumn>
 *
 * @method DataColumn|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataColumn|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataColumn[]    findAll()
 * @method DataColumn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataColumnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataColumn::class);
    }

    // Tu pourras ajouter tes requêtes personnalisées ici plus tard
}
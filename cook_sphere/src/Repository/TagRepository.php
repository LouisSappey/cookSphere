<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * Find or create a tag by name
     */
    public function findOrCreateByName(string $name): Tag
    {
        $tag = $this->findOneBy(['name' => $name]);
        
        if (!$tag) {
            $tag = new Tag();
            $tag->setName($name);
        }
        
        return $tag;
    }

    /**
     * Find tags by partial name match
     */
    public function findByPartialName(string $query): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 
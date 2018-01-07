<?php

namespace Blog\CoreBundle\Repository;

use Blog\CoreBundle\Entity\Post;
use Blog\CoreBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class CenterRepository extends EntityRepository
{
    public function findCentersOrderedByName(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->innerJoin('c.translations', 'ct')
            ->orderBy('ct.title', 'ASC');


        if ($user->getRol() == User::ROLE_CENTER) {
            $queryBuilder->innerJoin('c.profiles', 'p')->andWhere('p.id = :profile')->setParameter('profile', $user->getProfile());
        }

        return $queryBuilder;
    }
}

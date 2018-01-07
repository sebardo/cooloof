<?php

namespace Blog\CoreBundle\Repository;

use Blog\CoreBundle\Entity\Post;
use Blog\CoreBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class WeekRepository extends EntityRepository
{
    public function findWeeksByUser(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('w')
            ->select('w')
            ->innerJoin('w.center', 'c')
            ->innerJoin('c.profiles', 'p');

        if ($user->getRol() != User::ROLE_ADMIN)
        {
            if ($user->getProfile()) {
                $queryBuilder->where('p.id = :profile')->setParameter('profile', $user->getProfile());
            }
            else {
                $queryBuilder->where('1 = 2');
            }
        }

        return $queryBuilder;
    }

    public function getPaginationQuery(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('w')
            ->select('w, c, ct, wt')
            ->innerJoin('w.center', 'c')
            ->innerJoin('c.profiles', 'p')
            ->leftJoin('c.translations', 'ct')
            ->leftJoin('w.translations', 'wt')
            ->addOrderBy('c.id', 'ASC')
            ->addOrderBy('w.startsAt', 'ASC');

        if ($user->getRol() != User::ROLE_ADMIN)
        {
            if ($user->getProfile()) {
                $queryBuilder->where('p.id = :profile')->setParameter('profile', $user->getProfile());
            }
            else {
                $queryBuilder->where('1 = 2');
            }
        }

        return $queryBuilder->getQuery();
    }

    public function findWeekByIdAndUser($id, User $user)
    {
        $queryBuilder = $this->createQueryBuilder('w')
            ->select('w')
            ->innerJoin('w.center', 'c')
            ->innerJoin('c.profiles', 'p')
            ->where('w.id = :id')->setParameter('id', $id);

        if ($user->getRol() != User::ROLE_ADMIN)
        {
            if ($user->getProfile()) {
                $queryBuilder->andWhere('p.id = :profile')->setParameter('profile', $user->getProfile());
            }
            else {
                $queryBuilder->andWhere('1 = 2');
            }
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}

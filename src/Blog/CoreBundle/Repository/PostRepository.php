<?php

namespace Blog\CoreBundle\Repository;

use Blog\CoreBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PostRepository extends EntityRepository
{
    public function getPaginationQuery(User $user, $query = null)
    {
        $queryBuilder = $this->createQueryBuilder('post');
        $queryBuilder->addOrderBy('post.date', 'DESC');
        $queryBuilder->addOrderBy('post.id', 'DESC');

        if ($query) {
            $queryBuilder->where('post.title LIKE :query OR post.content LIKE :query');
            $queryBuilder->setParameter('query', "%$query%");
        }

        if ($user->getRol() != User::ROLE_ADMIN)
        {
            $queryBuilder->innerJoin('post.week', 'w')
                         ->innerJoin('w.center', 'c')
                         ->innerJoin('c.profiles', 'p')
                         ->where('p.id = :profile')->setParameter('profile', $user->getProfile());
        }

        return $queryBuilder->getQuery();
    }

    public function getPaginationQueryForBlog(User $user, $query = null)
    {
        $date = new \DateTime();
        $queryBuilder = $this->createQueryBuilder('post');
        $queryBuilder->where("post.date <= '{$date->format('Y-m-d H:i:s')}'");
        $queryBuilder->andWhere('post.active = 1');
        $queryBuilder->orderBy('post.date', 'DESC');
        $queryBuilder->addOrderBy('post.id', 'DESC');

        /*
        if ($query) {
            $queryBuilder->andWhere('post.title LIKE :query OR post.content LIKE :query');
            $queryBuilder->setParameter('query', "%$query%");
        }
        */

        if ($user->getRol() == User::ROLE_PARENT)
        {
            $queryBuilder->innerJoin('post.week', 'w');
            $queryBuilder->andWhere('w.profile = :profile')->setParameter('profile', $user->getProfile());
        }

        if ($user->getRol() == User::ROLE_CENTER)
        {
            $queryBuilder->innerJoin('post.week', 'w')
                ->innerJoin('w.center', 'c')
                ->innerJoin('c.profiles', 'p')
                ->where('p.id = :profile')->setParameter('profile', $user->getProfile());
        }

        return $queryBuilder->getQuery();
    }

    public function findBySlugAndUser($slug, User $user, $validatedComments = false)
    {
        $queryBuilder = $this->createQueryBuilder('post')
            ->select('post, images, comments')
            ->leftJoin('post.images', 'images');

        if ($validatedComments) {
            $queryBuilder->leftJoin('post.comments', 'comments', "WITH", "comments.validated = 1");
        }
        else {
            $queryBuilder->leftJoin('post.comments', 'comments');
        }

        $this->addCheckPermissionsFilters($queryBuilder, $user);

        $queryBuilder->andWhere('post.slug = :slug')->setParameter('slug', $slug);

        $query = $queryBuilder->getQuery();
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');

        return $query->getOneOrNullResult();
    }

    private function addCheckPermissionsFilters(&$queryBuilder, User $user)
    {
        if ($user->getRol() == User::ROLE_PARENT)
        {
            $queryBuilder->innerJoin('post.week', 'w');
            $queryBuilder->andWhere('w.profile = :profile')->setParameter('profile', $user->getProfile());
        }

        if ($user->getRol() == User::ROLE_CENTER)
        {
            $queryBuilder->innerJoin('post.week', 'w')
                ->innerJoin('w.center', 'c')
                ->innerJoin('c.profiles', 'p')
                ->where('p.id = :profile')
                ->setParameter('profile', $user->getProfile());
        }
    }
}

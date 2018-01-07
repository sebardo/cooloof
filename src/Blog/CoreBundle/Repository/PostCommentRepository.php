<?php

namespace Blog\CoreBundle\Repository;

use Blog\CoreBundle\Entity\Post;
use Blog\CoreBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class PostCommentRepository extends EntityRepository
{
    public function getPaginationQuery(Post $post, $query = null)
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->orderBy('c.validated', 'DESC');
        $queryBuilder->addOrderBy('c.created', 'DESC');

        if ($query) {
            $queryBuilder->where('c.name LIKE :query OR c.comment LIKE :query');
            $queryBuilder->setParameter('query', "%$query%");
        }

        $queryBuilder->andWhere('c.post = :post')->setParameter('post', $post);

        return $queryBuilder->getQuery();
    }

    public function getPaginationAllQuery(User $user, $query = null)
    {
        $queryBuilder = $this->createQueryBuilder('comment')
            ->innerJoin('comment.post', 'post');

        $queryBuilder->orderBy('comment.validated', 'DESC');
        $queryBuilder->addOrderBy('comment.created', 'DESC');

        if ($query) {
            $queryBuilder->where('comment.name LIKE :query OR comment.comment LIKE :query');
            $queryBuilder->setParameter('query', "%$query%");
        }

        if ($user->getRol() != User::ROLE_ADMIN)
        {
            $queryBuilder->innerJoin('post.week', 'w')
                ->innerJoin('w.center', 'c')
                ->innerJoin('c.profiles', 'p')
                ->andWhere('p.id = :profile')
                ->setParameter('profile', $user->getProfile());
        }

        return $queryBuilder->getQuery();
    }

    public function findByPostAndIdAndUser($slug, $commentId, User $user)
    {
        $queryBuilder = $this->createQueryBuilder('comment')
            ->select('comment, post')
            ->innerJoin('comment.post', 'post')
            ->where('post.slug = :slug')
            ->andWhere('comment.id = :commentId');

        if ($user->getRol() != User::ROLE_ADMIN)
        {
            $queryBuilder->innerJoin('post.week', 'w')
                ->innerJoin('w.center', 'c')
                ->innerJoin('c.profiles', 'p')
                ->andWhere('p.id = :profile')
                ->setParameter('profile', $user->getProfile());
        }

        $queryBuilder->setParameter('slug', $slug);
        $queryBuilder->setParameter('commentId', $commentId);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findByIdAndUser($commentId, User $user)
    {
        $queryBuilder = $this->createQueryBuilder('comment')
            ->select('comment, post')
            ->innerJoin('comment.post', 'post')
            ->andWhere('comment.id = :commentId');

        if ($user->getRol() != User::ROLE_ADMIN)
        {
            $queryBuilder->innerJoin('post.week', 'w')
                ->innerJoin('w.center', 'c')
                ->innerJoin('c.profiles', 'p')
                ->andWhere('p.id = :profile')
                ->setParameter('profile', $user->getProfile());
        }

        $queryBuilder->setParameter('commentId', $commentId);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findByUserAndValidated(User $user, $validated = false)
    {
        $queryBuilder = $this->createQueryBuilder('comment')
            ->select('comment, post')
            ->innerJoin('comment.post', 'post')
            ->andWhere('comment.validated = :validated');

        if ($user->getRol() != User::ROLE_ADMIN)
        {
            $queryBuilder->innerJoin('post.week', 'w')
                ->innerJoin('w.center', 'c')
                ->innerJoin('c.profiles', 'p')
                ->andWhere('p.id = :profile')
                ->setParameter('profile', $user->getProfile());
        }

        $queryBuilder->setParameter('validated', $validated);

        return $queryBuilder->getQuery()->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findByRoomAndDate(int $roomId, \DateTimeInterface $dateLimit): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.room', 'r')
            ->join('m.sender', 'u')
            ->where('r.id = :roomId')
            ->andWhere('m.createdAt >= :dateLimit')
            ->setParameter('roomId', $roomId)
            ->setParameter('dateLimit', $dateLimit)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

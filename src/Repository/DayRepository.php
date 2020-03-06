<?php

namespace App\Repository;

use App\Entity\Day;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Day|null find($id, $lockMode = null, $lockVersion = null)
 * @method Day|null findOneBy(array $criteria, array $orderBy = null)
 * @method Day[]    findAll()
 * @method Day[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Day::class);
    }

    /**
     * @return Day[]
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function findCurrentMonthForUser(UserInterface $user)
    {
        $days = $this->createQueryBuilder('d')
            ->andWhere('d.date BETWEEN :from AND :to')
            ->andWhere('d.user = :user')
            ->setParameter('from', (new DateTime())->format('Y-m-01'))
            ->setParameter('to', (new DateTime())->format('Y-m-t'))
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        if (count($days) === 0) {
            $days = $this->generateNewMonth($user);
        }

        return $days;
    }

    /**
     * @return Day[]
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    private function generateNewMonth(UserInterface $user)
    {
        $days = [];
        /** @var User $user */
        $this->_em->beginTransaction();
        $date = new DateTime();
        [$year, $month] = [(int)$date->format('Y'), (int)$date->format('m')];
        foreach (array_fill(0, (int)date("t"), 0) as $dayIndex => $v) {
            $date->setDate($year, $month, ((int)$dayIndex) + 1);
            $dayOfWeek = (int)$date->format('w');
            $day = new Day();
            $day->setDate($date);
            $day->setUser($user);
            $day->setRequired(!in_array($dayOfWeek, [0, 6]) ? $user->getRequired() : 0);
            $this->_em->persist($day);
            $this->_em->flush();
            $this->_em->refresh($day);
            $days[] = $day;
        }
        $this->_em->commit();
        return $days;
    }

    /*
    public function findOneBySomeField($value): ?Day
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

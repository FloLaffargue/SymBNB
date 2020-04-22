<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService {

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager) 
    {
        $this->manager = $manager;
    }

    public function getStats(): array {
        $users    = $this->getUsersCount();
        $ads      = $this->getAdsCount();
        $bookings = $this->getBookingsCount();
        $comments = $this->getCommentsCount();

        return compact('users', 'ads', 'bookings', 'comments');
    }
    
    public function getUsersCount() {
        // getSingleScalarResult permet d'avoir un résultat directement plus tôt qu'un tableau dans lequel il faudrait aller chercher ce résultat
        return $this->manager->createQuery('SELECT count(u) from App\Entity\User u')->getSingleScalarResult(); 
    }

    public function getAdsCount() {
        return $this->manager->createQuery('SELECT count(a) from App\Entity\Ad a ')->getSingleScalarResult();

    }

    public function getBookingsCount() {
        return $this->manager->createQuery('SELECT count(b) from App\Entity\Booking b')->getSingleScalarResult();    
    }

    public function getCommentsCount() {
        return $this->manager->createQuery('SELECT count(c) from App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getAdsStats(String $direction): array {  
        $bestAds = $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
                FROM App\Entity\Comment c
                JOIN c.ad a
                JOIN c.author u
                GROUP BY a
                ORDER BY note ' . $direction
        )
        ->setMaxResults(5)
        ->getResult();
    
        return $bestAds;
    }






}
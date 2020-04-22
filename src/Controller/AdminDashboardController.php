<?php

namespace App\Controller;

use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(EntityManagerInterface $manager, StatsService $service)
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'stats'    => $service->getStats(),
            'bestAds'  => $service->getAdsStats('DESC'),
            'worstAds' => $service->getAdsStats('ASC')
        ]);
    }
}

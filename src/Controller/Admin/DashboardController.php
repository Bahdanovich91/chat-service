<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use App\Entity\Room;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Chat Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Entities');

        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);

        yield MenuItem::linkToCrud('Rooms', 'fa fa-comments', Room::class);

        yield MenuItem::linkToCrud('Messages', 'fa fa-envelope', Message::class);
    }
}

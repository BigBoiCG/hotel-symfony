<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Slider;
use App\Entity\Chambre;
use App\Entity\Commande;
use App\Controller\Admin\AdminCrudController;
use App\Entity\Avis;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
        
    }    
    
    #[Route('/admlgxvz', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        $url = $this->adminUrlGenerator->setController(AdminCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Hotel');
    }

    public function configureMenuItems(): iterable
    {

        return [
            MenuItem::linkToDashboard("Back Office", 'fa fa-igloo'),
            MenuItem::section('CRUDs'),
            MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', Admin::class),
            MenuItem::linkToCrud('Chambres', 'fas fa-bed', Chambre::class),
            MenuItem::linkToCrud('Slides', 'fas fa-sliders', Slider::class),
            MenuItem::linkToCrud('Commandes', 'fas fa-check-to-slot', Commande::class),
            MenuItem::linkToCrud('Avis', 'fas fa-comment', Avis::class),
            MenuItem::section('Site'),
            MenuItem::linkToRoute('Retourner sur le site', 'fa fa-home', 'home'),
        ]; 
    }
}

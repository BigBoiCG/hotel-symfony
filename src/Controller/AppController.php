<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\AvisRepository;
use App\Repository\SliderRepository;
use App\Repository\ChambreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(SliderRepository $repo): Response
    {
        $slides = $repo->findBy(['ordre' => [1,2,3]], ['ordre' => 'ASC']);
        return $this->render('app/index.html.twig', [
            'slides' => $slides
        ]);
    }

    #[Route('/chambres/classique/{min}/{max}', name: 'chambres_classiques')]
    #[Route('/chambres/confort/{min}/{max}', name: 'chambres_confort')]
    #[Route('/chambres/suite/{min}/{max}', name: 'chambres_suites')]
    public function chambres($min, $max, ChambreRepository $repo): Response
    {
        $chambres = $repo->findMinMax($min, $max);
        return $this->render('app/chambres.html.twig', [
            'chambres' => $chambres
        ]);
    }

    #[Route('/chambres/all', name: 'all_rooms')]
    public function allrooms(ChambreRepository $repo): Response
    {
        $chambres = $repo->findAll();
        return $this->render('app/allrooms.html.twig', [
            'chambres' => $chambres
        ]);
    }

    #[Route('/chambres/view/{id}', name: 'view_chambre')]
    public function view($id, ChambreRepository $repo, Request $request, EntityManagerInterface $manager): Response
    {
        $chambre = $repo->findOneBy(['id' => $id]);
        $prixChambre = $chambre->getPrixJournalier();
        $commande = new Commande;
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $dateDebut = $commande->getDateArrivee();
            $dateFin = $commande->getDateDepart();
            $diffDate = date_diff($dateDebut, $dateFin);
            $commande->setDateEnregistrement(new \Datetime);
            $commande->setPrixTotal($prixChambre*$diffDate->d );
            $commande->setChambre($chambre);
            $manager->persist($commande);
            $manager->flush();
            $this->addFlash('success', 'Réservation enregistrée !');
            return $this->redirectToRoute('home');
        }


        return $this->render('app/view.html.twig', [
            'chambre' => $chambre,
            'formCommande' => $form
        ]);
    }

    #[Route('/avis', name: 'comments')]
    public function comments(AvisRepository $repo, Request $request, EntityManagerInterface $manager): Response
    {
        $value = $request->query->get('filterSelect');
        switch ($value){
            case 'general' :
            case 'chambres' :
            case 'resto' :
            case 'spa' :
                $filter = $repo->findBy(['categorie' => $value], ['date_enregistrement' => 'DESC']);
                break;
            case '' :
                $filter = $repo->findAll();
                $filter = array_reverse($filter);                
            default:
                $filter = $repo->findAll();
                $filter = array_reverse($filter);            
        }
        $nouvelAvis = new Avis;
        $form = $this->createForm(AvisType::class, $nouvelAvis);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $nouvelAvis->setDateEnregistrement(new \Datetime);
            $manager->persist($nouvelAvis);
            $manager->flush();
            $this->addFlash('success', 'Merci pour votre commentaire !');
            return $this->redirectToRoute('comments');
        }

        return $this->render('app/avis.html.twig', [
            'formAvis' => $form,
            'filter' => $filter,
            'value' => $value,
        ]);
    }

    #[Route('/restaurant/{select}', name: 'resto')]
    public function resto($select): Response
    {
        return $this->render('app/resto.html.twig', [
            'select' => $select
        ]);
    }

    #[Route('/hotel/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('app/hotel.html.twig');
    }
}

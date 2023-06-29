<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Entity\Commande;
use App\Form\CommandeType;
use jcobhams\NewsApi\NewsApi;
use App\Service\CallApiService;
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
    #[Route('/chambres/search/{minSelect}/{maxSelect}', name: 'chambres_search')]
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

    #[Route('/chambres/filtered', name: 'filtered')]
    public function filtered(ChambreRepository $repo, Request $request): Response
    {
        // dd($request->get('minSelect'));
        if($request->get('minSelect') != null && $request->get('maxSelect') != null)
        {
        $min = $request->get('minSelect');
        $max = $request->get('maxSelect');
        // dd($min, $max);
        $chambres = $repo->findMinMax($min, $max);         
        }
        else {
        $min = 0;
        $max = 1500;
        // dd($min, $max);
        $chambres = $repo->findMinMax($min, $max);         
        }
        return $this->render('app/filtered.html.twig', [
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
        return $this->render('app/about.html.twig');
    }

    #[Route('/actu', name: 'actu')]
    public function actu(): Response
    {
        $your_api_key = '374037188db646a78f0c69ee803e359c';
        $newsapi = new NewsApi($your_api_key);
        $q = 'hôtellerie';
        $allArticles = $newsapi->getEverything($q, null, null, null, null, null, 'fr', 'popularity', null, 20);
        // dd($newsapi->getSortBy()) : relevancy, popularity, publishedAt
        dd($allArticles);
        return $this->render('app/actu.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('app/contact.html.twig');
    }

    #[Route('/hotel/acces', name: 'acces')]
    public function acces(): Response
    {
        return $this->render('app/acces.html.twig');
    }

    #[Route('/spa', name: 'spa')]
    public function spa(): Response
    {
        return $this->render('app/spa.html.twig');
    }
}

<?php

namespace App\Controller;

use App\Entity\LigneCommande;
use App\Form\LigneCommandeType;
use App\Entity\Commande;
use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProduitRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ligne/commande')]
class LigneCommandeController extends AbstractController
{
    #[Route('/', name: 'ligne_commande_index', methods: ['GET'])]
    public function index(LigneCommandeRepository $ligneCommandeRepository): Response
    {
        return $this->render('ligne_commande/index.html.twig', [
            'ligne_commandes' => $ligneCommandeRepository->findAll(),
        ]);
    }

    #[Route('/{id}/new', name: 'ligne_commande_new', methods: ['GET', 'POST'])]
    public function new(SessionInterface $session ,Request $request,Commande $commande, ProduitRepository $produitRepository , EntityManagerInterface $entityManager): Response
    {
        $panier = $session->get("panier", []);
        $total = 0;
        
        foreach($panier as $id => $quantite){
            $ligneCommande = new LigneCommande();
            
            $produit = $produitRepository->find($id);
           // $total += $produit->getPrix() * $quantite;
            $ligneCommande->setCommande($commande);
            $ligneCommande->setProduit($produit);
            $ligneCommande->setQuantite($quantite);
            $entityManager->persist($ligneCommande);
            $entityManager->flush();

        }
        return $this->redirectToRoute('ligne_commande_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'ligne_commande_show', methods: ['GET'])]
    public function show(LigneCommande $ligneCommande): Response
    {
        return $this->render('ligne_commande/show.html.twig', [
            'ligne_commande' => $ligneCommande,
        ]);
    }

    #[Route('/{id}/edit', name: 'ligne_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LigneCommande $ligneCommande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LigneCommandeType::class, $ligneCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('ligne_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ligne_commande/edit.html.twig', [
            'ligne_commande' => $ligneCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'ligne_commande_delete', methods: ['POST'])]
    public function delete(Request $request, LigneCommande $ligneCommande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ligneCommande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ligneCommande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ligne_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}

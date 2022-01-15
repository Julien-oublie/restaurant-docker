<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier')]
    public function index( SessionInterface $session , ProduitRepository $produitRepository): Response
    {

        $panier = $session->get("panier",[]);

        //on fabrique les données

        $dataPanier = [];
        $total = 0;
        foreach($panier as $id => $quantite){
            $produit = $produitRepository->find($id);
            $dataPanier[] = [
                "produit" => $produit,
                "quantite" => $quantite
            ];
            $total += $produit->getPrix() * $quantite;
           
        }


        return $this->render('panier/index.html.twig', compact("dataPanier","total"));
    }

    #[Route('/panier/add/{id}', name: 'add')]
    public function add(Produit $produit , $value , SessionInterface $session){

        //on récupérer le panier actuel
        $panier = $session->get("panier",[]);
        $id = $produit->getId();

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }


        //on sauvegarde la quantite

        $session->set("panier",$panier);


        if($value){
            return $this->redirectToRoute('restaurant_show', ['id'=>$produit->getRestaurant()->getId()], Response::HTTP_SEE_OTHER);
        }
        else{
            return $this->redirectToRoute('panier');
        }
    }

    #[Route('/panier/remove/{id}', name: 'remove')]
    public function remove(Produit $produit , SessionInterface $session){

        //on récupérer le panier actuel
        $panier = $session->get("panier",[]);
        $id = $produit->getId();

        if(!empty($panier[$id])){
            if( $panier[$id] > 1 ) {
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }


        //on sauvegarde la quantite

        $session->set("panier",$panier);

        return $this->redirectToRoute("panier");
    }

    #[Route('/panier/delete/{id}', name: 'delete')]
    public function delete(Produit $produit , SessionInterface $session){

        //on récupérer le panier actuel
        $panier = $session->get("panier",[]);
        $id = $produit->getId();

        if(!empty($panier[$id])){
             unset($panier[$id]);
        }


        //on sauvegarde la quantite

        $session->set("panier",$panier);

        return $this->redirectToRoute("panier");
    }
}

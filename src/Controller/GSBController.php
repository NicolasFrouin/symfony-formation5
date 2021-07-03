<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EmployeRepository;
use App\Form\FormationType;
use App\Form\EmployeType;
use App\Form\InscriptionType;
use App\Form\ProduitType;
use App\Entity\Employe;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * 0 Pour les employés du service formation
 * 1 pour les visiteurs
 * 
 * e pour les inscriptions en attente
 * a pour les inscriptions autorisées
 */


class GSBController extends AbstractController
{
    /**
     * @Route("accueil", name="accueil")
     */
    public function accueil(): Response
    {
        $session = new Session();
        $employe = $this->getDoctrine()->getRepository(Employe::class)->find($session->get('employeId'));
        $forma = $this->getDoctrine()->getRepository(Formation::class)->findAll();
        $insc = $this->getDoctrine()->getRepository(Inscription::class)->findAllInscriptionByEmploye($employe);
        return $this->render('gsb/accueil.html.twig', array('lesFormations' => $forma, 'employe' => $employe, 'lesInscriptions' => $insc));
    }

    /**
     * @Route("afficherFormation", name="afficher_formation")
     */
    public function AfficherFormations() 
    {
        $session = new Session();
        $employe = $this->getDoctrine()->getRepository(Employe::class)->find($session->get('employeId'));
        $ins = $this->getDoctrine()->getRepository(Inscription::class)->findAll();
        $forma = $this->getDoctrine()->getRepository(Formation::class)->findAll();
        if (!$forma)
        {
            $message = "Il n'y a pas de formations";
        }
        else {
            $message = null;
        }
        return $this->render('gsb/listFormation.html.twig', array('ensFormations' => $forma, 'lesInscrits' => $ins, 'employe' => $employe, 'message' => $message));
    }

    /**
     * @Route("ajoutFormation", name="ajout_formation")
     */
    public function AjoutFormation(Request $request, $formation= null)
    {
        if ($formation == null) {
            $formation = new Formation();
        }
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($formation);
            $em->flush();
            return $this->redirectToRoute('afficher_formation');
        }
        return $this->render('gsb/editer.html.twig', array('form'=>$form->createView()));
    }

    /**
     * @Route("supprimerFormation/{id}", name="supprimer_formation")
     */
    public function SupprimerFormation($id)
    {
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($id);
        $manager = $this->getDoctrine()->getManager();
        // var_dump($formation);
        // exit;
        $manager->remove($formation);
        $manager->flush();
        return $this->redirectToRoute('afficher_formation');
    }
    
    /**
     * @Route("inscriptionFormation/{id}", name="inscription_formation")
     */
    public function InscriptionFormation($id){
        $session = new Session();
        $e = $this->getDoctrine()->getRepository(Employe::class)->find($session->get('employeId'));
        $f = $this->getDoctrine()->getRepository(Formation::class)->find($id);

        $i = new Inscription();

        $i->setLaFormation($f);
        $i->setLEmploye($e);
        $i->setStatut("e");

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($i);
        $manager->flush();
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("login", name="login")
     */
    public function loginAction(Request $request, $emp = null)
    {
        if ($emp == null) {
            $emp = new Employe();
        }
        $form = $this->createForm(EmployeType::class, $emp);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $employe = $this->getDoctrine()->getRepository(Employe::class)->findOneBySomeLoginMdp($emp->getLogin(), $emp->getMdp());
            if ($employe) {
                $session = new Session();
                $session->set('employeId', $employe->getId());
                if ($employe->getStatut() == 0) {
                    // Il est medecin !
                    return $this->redirectToRoute('afficher_formation');
                }
                else {
                    // Il est visiteur !
                    return $this->redirectToRoute('accueil');
                }
            }
        }
        return $this->render('gsb/editer.html.twig', array('form'=>$form->createView()));
    }

    /**
     * @Route("afficherInscrits/{id}", name="afficher_inscrits")
     */
    public function AfficherInscrits($id) 
    {
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($id);
        $inscrits = $this->getDoctrine()->getRepository(Inscription::class)->findAllByFormation($formation);
        return $this->render('gsb/listInscrits.html.twig', array('lesInscrits' => $inscrits, 'formation' => $formation));
    }

    /**
     * @Route("autoriserInscription/{id}", name="autoriser_inscription")
     */
    public function AutoriserInscription($id)
    {
        $i = $this->getDoctrine()->getRepository(Inscription::class)->find($id);
        $i->setStatut("a");
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($i);
        $manager->flush();
        return $this->redirectToRoute('afficher_formation');
    }

    // <Castaing>
    // à revoir éventuellement
    /**
     * @Route("afficherFormationMoisAnnee/{mois}/{annee}", name="afficher_formation_mois_annee")
     */
    public function AfficherFormationMoisAnnee($mois, $annee)
    {
        $debut = date("Y-m-d", strtotime($annee."-".$mois."-01"));
        // var_dump($debut);
        $d = new \DateTime($debut);
        $mois += 1;
        $fin = date("Y-m-d", strtotime($annee."-".$mois."-01"));
        $f = new \DateTime($fin);
        $forma = $this->getDoctrine()->getRepository(Formation::class)->findAllFormationsByMoisEtAnnee($d, $f);
        $dump = var_dump($forma);
        return $this->render('gsb/Castaing_formations.html.twig', array('lesFormations' => $forma, 'dump' => $dump));
    }
    // </Castaing>
}

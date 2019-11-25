<?php


namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Evenement;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Twig\Environment;                            // template TWIG
use Symfony\Bridge\Doctrine\RegistryInterface;   // ORM Doctrine
use Symfony\Component\HttpFoundation\Request;    // objet REQUEST
use Symfony\Component\HttpFoundation\Response;    // objet RESPONSE

class EvenementController extends AbstractController
{
    /**
     * @Route("/", name="Evenement.index",methods={"GET"})
     */
    public function index(Environment $twig)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['username']=null;
        $_SESSION['role']=null;
        return $this->redirectToRoute('Evenement.showEvenement');
    }

    /**
     * @Route("/show/Evenement", name="Evenement.showEvenement", methods={"GET"})
     */
    public function showEvenement(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        if($_SESSION)
             $twig->addGlobal('session', $_SESSION);
        $evenement=NULL;
        $evenement=$doctrine->getRepository(Evenement::class)->findAll([],['date'=>'ASC']);
        dump($evenement);
        return new Response($twig->render('Evenement/showEvenement.html.twig', ['evenements' => $evenement]));
    }

    /**
     * @Route("/admin/add/Evenement", name="evenement.add", methods={"GET"})
     */
    public function addEvenement(Request $request, Environment $twig, RegistryInterface $doctrine) {

        $categorie=$doctrine->getRepository(Categorie::class)->findAll([],['id'=>'ASC']);
        return $this->render('Evenement/addEvent.html.twig', ['categorie' => $categorie]);
    }

    /**
     * @Route("/validAdd/Evenement", name="Evenement.validAddEvenement", methods={"POST"})
     */
    public function validAddEvenement(Request $request, Environment $twig, RegistryInterface $doctrine)
    {

        if(!$this->isCsrfTokenValid('add_valid', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $donnees['nom']=htmlspecialchars($_POST['nom']);
        $donnees['prix']=htmlspecialchars($_POST['prix']);
        $donnees['photo']=htmlspecialchars($_POST['photo']);
        $donnees['nbPlaces']=htmlspecialchars($_POST['nbPlaces']);
        $donnees['dateEvenement']=htmlentities($request->request->get('dateEvenement'));
        $donnees['description']=htmlspecialchars($_POST['description']);
        $donnees['categorie']=htmlentities($request->request->get('categorie'));

        if($donnees['nbPlaces']>0) $donnees['disponible']=true;
        else $donnees['disponible']=false;

        $erreurs=$this->validDonnees($donnees);

        if(! empty($erreurs))
        {
            $categorie=$doctrine->getRepository(Categorie::class)->findAll([],['id'=>'ASC']);
            $this->addFlash('error', 'des champs sont mal renseignés !');
            return $this->render('Evenement/addEvent.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs,'categorie'=> $categorie]);
        }else {
            $evenement = new Evenement();
            $evenement->setNom( $donnees['nom']);
            $evenement->setPrix( $donnees['prix']);
            $evenement->setPhoto( $donnees['photo']);
            $evenement->setDisponible( $donnees['disponible']);
            $evenement->setNombrePlaces( $donnees['nbPlaces']);
            $evenement->setDescription($donnees['description']);
            $evenement->setDate(new \DateTime($donnees['dateEvenement']));
            $categorie = $doctrine->getRepository(Categorie::class)->findOneBy(['id' => $donnees['categorie']]);
            $evenement->setCategorie($categorie);
            $doctrine->getEntityManager()->persist($evenement);
            $doctrine->getEntityManager()->flush();

            $this->addFlash('success', 'evenement ajouté !');
            return $this->redirectToRoute('Evenement.showEvenement');
        }
    }


    /**
     * @Route("/admin/delete/{id}/Evenement", name="evenement.delete", methods={"DELETE"}), requirements={id<\d+>?1)}
     */
    public function deleteEvenement(Request $request, Environment $twig, RegistryInterface $doctrine, $id) {


        if(!$this->isCsrfTokenValid('delete_valid', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $evenement=$doctrine->getRepository(Evenement::class)->findOneBy(['id'=>$id]);
        $doctrine->getEntityManager()->remove($evenement);
        $doctrine->getEntityManager()->flush();
        $this->addFlash('success', 'Evenement supprimmé !');
        return $this->redirectToRoute('Evenement.showEvenement');
    }

    /**
     * @Route("/admin/edit/{id}/Evenement", name="evenement.edit", methods={"GET"}), requirements={id<\d+>?1)}
     */
    public function editEvenement(Request $request, Environment $twig, RegistryInterface $doctrine, $id) {

        $evenement=$doctrine->getRepository(Evenement::class)->findOneBy(['id'=>$id]);
        $doctrine->getEntityManager()->remove($evenement);
        $donnees['id']=$id;
        $donnees['nom']=$evenement->getNom();
        $donnees['prix']=$evenement->getPrix();
        $donnees['photo']=$evenement->getPhoto();
        $donnees['nbPlaces']=$evenement->getNombrePlaces();
        $donnees['dateEvenement']=$evenement->getDate();
        $donnees['description']=$evenement->getDescription();
        $donnees['categorie'] = $evenement->getCategorie();
        $categorie=$doctrine->getRepository(Categorie::class)->findAll([],['id'=>'ASC']);

        return $this->render('Evenement/editEvent.html.twig',['donnees'=>$donnees,'categorie'=>$categorie]);
    }

    /**
     * @Route("/validEdit/Evenement", name="Evenement.validEditEvenement", methods={"POST"})
     */
    public function validEditEvenement(Request $request, Environment $twig, RegistryInterface $doctrine)
    {

        if(!$this->isCsrfTokenValid('edit_valid', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $id=htmlspecialchars($_POST['Id']);
        $donnees['nom']=htmlspecialchars($_POST['nom']);
        $donnees['prix']=htmlspecialchars($_POST['prix']);
        $donnees['photo']=htmlspecialchars($_POST['photo']);
        $donnees['nbPlaces']=htmlspecialchars($_POST['nbPlaces']);
        $donnees['dateEvenement']=htmlentities($request->request->get('dateEvenement'));
        $donnees['description']=htmlspecialchars($_POST['description']);
        $donnees['categorie']=htmlentities($request->request->get('categorie'));

        $erreurs=$this->validDonnees($donnees);

        if(! empty($erreurs))
        {
            $categorie=$doctrine->getRepository(Categorie::class)->findAll([],['id'=>'ASC']);
            $this->addFlash('error', 'des champs sont mal renseignés !');
            return $this->render('Evenement/editEvent.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs,'categorie'=> $categorie]);
        }else {

            $evenement = $doctrine->getRepository(Evenement::class)->findOneBy(['id' => $id]);
            $evenement->setNom( $donnees['nom']);
            $evenement->setPrix( $donnees['prix']);
            $evenement->setPhoto( $donnees['photo']);
            $evenement->setNombrePlaces( $donnees['nbPlaces']);
            $evenement->setDescription($donnees['description']);
            $evenement->setDate(new \DateTime($donnees['dateEvenement']));
            $categorie = $doctrine->getRepository(Categorie::class)->findOneBy(['id' => $donnees['categorie']]);
            $evenement->setCategorie($categorie);
            $doctrine->getEntityManager()->persist($evenement);
            $doctrine->getEntityManager()->flush();

            $this->addFlash('success', 'Evenement modifié !');
            return $this->redirectToRoute('Evenement.showEvenement');
        }
    }

    private function validDonnees($donnees) {
        $erreurs = array();
        if($donnees['nom']=="")
            $erreurs['nom'] = 'saisissez un nom';
        if (!is_numeric($donnees['prix']))
            $erreurs['prix'] = 'saisissez une valeur numérique';
        if (!is_numeric($donnees['nbPlaces']))
            $erreurs['nbPlaces'] = 'saisissez une valeur numérique';
       // if (!is_numeric(strtotime($donnees['dateEvenement'])))
       //     $erreurs['dateEvenement']= 'saisissez une date correct selon le format Y-m-d';
        if (!is_numeric($donnees['categorie']))
            $erreurs['categorie'] = 'veuillez saisir une valeur';
        return $erreurs;
    }
}
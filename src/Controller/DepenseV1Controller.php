<?php


namespace App\Controller;

use App\Entity\CategorieDepense;
use App\Entity\Depense;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Twig\Environment;                            // template TWIG
use Symfony\Bridge\Doctrine\RegistryInterface;   // ORM Doctrine
use Symfony\Component\HttpFoundation\Request;    // objet REQUEST
use Symfony\Component\HttpFoundation\Response;    // objet RESPONSE

class DepenseV1Controller extends AbstractController
{
    /**
     * @Route("/", name="Depense.index",methods={"GET"})
     */
    public function index(Environment $twig)
    {
        return $this->redirectToRoute('Depense.showDepenses');
    }

    /**
     * @Route("/show/depenses", name="Depense.showDepenses", methods={"GET"})
     */
    public function showDepenses(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        $twig->addGlobal('session', $_SESSION);
        $depenses=NULL;
        $depenses=$doctrine->getRepository(Depense::class)->findAll([],['date'=>'ASC']);
        dump($depenses);
        return new Response($twig->render('Depense/showDepenses.html.twig', ['depenses' => $depenses]));
    }

    /**
     * @Route("/add/depense", name="Depense.addDepense", methods={"GET"})
     */
    public function addDepense(Request $request, Environment $twig, RegistryInterface $doctrine) {

        $categorieDepenses=$doctrine->getRepository(CategorieDepense::class)->findAll([],['id'=>'ASC']);
        return $this->render('Depense/addDepense.html.twig', ['categorieDepenses' => $categorieDepenses]);
    }

    /**
     * @Route("/validAdd/depense", name="Depense.validAddDepense", methods={"POST"})
     */
    public function validAddDepense(Request $request, Environment $twig, RegistryInterface $doctrine)
    {

        if(!$this->isCsrfTokenValid('formulaire_produit_valide', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $donnees['montant']=htmlspecialchars($_POST['montant']);
        $donnees['description']=htmlspecialchars($_POST['description']);
        $donnees['dateDepense']=htmlentities($request->request->get('dateDepense'));
        $donnees['categorieDepense_id']=htmlentities($request->request->get('categorieDepense_id'));

        $erreurs=$this->validDonnees($donnees);

        if(! empty($erreurs))
        {
            $categorieDepenses=$doctrine->getRepository(CategorieDepense::class)->findAll([],['id'=>'ASC']);
            return $this->render('Depense/addDepense.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs,'categorieDepenses'=> $categorieDepenses]);
        }else {
            $depense = new Depense();
            $depense->setMontant($donnees['montant']);
            $depense->setDescription($donnees['description']);
            $depense->setDateDepense(new \DateTime($donnees['dateDepense']));
            $categorie = $doctrine->getRepository(CategorieDepense::class)->findOneBy(['id' => $donnees['categorieDepense_id']]);
            $depense->setCategorieDepense($categorie);
            $doctrine->getEntityManager()->persist($depense);
            $doctrine->getEntityManager()->flush();

            $this->addFlash('success', 'Depense ajoutée !');
            return $this->redirectToRoute('Depense.showDepenses');
        }
        return $this->redirectToRoute('Depense.showDepenses');
    }


    /**
     * @Route("/delete/{id}/depense", name="Depense.deleteDepense", methods={"DELETE"}), requirements={id<\d+>?1)}
     */
    public function deleteDepense(Request $request, Environment $twig, RegistryInterface $doctrine, $id) {

        if(!$this->isCsrfTokenValid('delete_valid', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $depense=$doctrine->getRepository(Depense::class)->findOneBy(['id'=>$id]);
        $doctrine->getEntityManager()->remove($depense);
        $doctrine->getEntityManager()->flush();
        $this->addFlash('success', 'Depense supprimmée !');
        return $this->redirectToRoute('Depense.showDepenses');
    }

    /**
     * @Route("/edit/{id}/depense", name="Depense.editDepense", methods={"GET"}), requirements={id<\d+>?1)}
     */
    public function editDepense(Request $request, Environment $twig, RegistryInterface $doctrine, $id) {

        $depense=$doctrine->getRepository(Depense::class)->findOneBy(['id'=>$id]);
        $doctrine->getEntityManager()->remove($depense);
        $donnees['id']=$id;
        $donnees['montant']=$depense->getMontant();
        $donnees['description']=$depense->getDescription();
        $donnees['dateDepense']=$depense->getDateDepense();
        $donnees['categorieDepense_id']=$depense->getCategorieDepense();
        $categorieDepenses=$doctrine->getRepository(CategorieDepense::class)->findAll([],['id'=>'ASC']);

        return $this->render('Depense/editDepense.html.twig',['donnees'=>$donnees,'categorieDepenses'=>$categorieDepenses]);
    }

    /**
     * @Route("/validEdit/depense", name="Depense.validEditDepense", methods={"POST"})
     */
    public function validEditDepense(Request $request, Environment $twig, RegistryInterface $doctrine)
    {

        if(!$this->isCsrfTokenValid('edit_valid', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $id=htmlspecialchars($_POST['Id']);
        $donnees['montant']=htmlspecialchars($_POST['montant']);
        $donnees['description']=htmlspecialchars($_POST['description']);
        $donnees['dateDepense']=htmlspecialchars($_POST['dateDepense']);
        $donnees['categorieDepense_id']=htmlentities($request->request->get('categorieDepense_id'));


        $erreurs=$this->validDonnees($donnees);

        if(! empty($erreurs))
        {
            $categorieDepenses=$doctrine->getRepository(CategorieDepense::class)->findAll([],['id'=>'ASC']);
            return $this->render('Depense/addDepense.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs,'categorieDepenses'=> $categorieDepenses]);
        }else {

            $depense = $doctrine->getRepository(Depense::class)->findOneBy(['id' => $id]);
            $depense->setMontant($donnees['montant']);
            $depense->setDescription($donnees['description']);
            $depense->setDateDepense(new \DateTime($donnees['dateDepense']));
            $categorie = $doctrine->getRepository(CategorieDepense::class)->findOneBy(['id' => $donnees['categorieDepense_id']]);
            $depense->setCategorieDepense($categorie);
            $doctrine->getEntityManager()->persist($depense);
            $doctrine->getEntityManager()->flush();

            $this->addFlash('success', 'Depense modifiée !');
            return $this->redirectToRoute('Depense.showDepenses');
        }
        return $this->redirectToRoute('Depense.showDepenses');
    }

    private function validDonnees($donnees) {
        $erreurs = array();
        if (!is_numeric($donnees['montant']))
            $erreurs['montant'] = 'saisissez une valeur numérique';
        if (!is_numeric(strtotime($donnees['dateDepense'])))
            $erreurs['dateDepense']= 'saisissez une date correct selon le format Y-m-d';
        if (!is_numeric($donnees['categorieDepense_id']))
            $erreurs['categorideDepense_id'] = 'veuillez saisir une valeur';
        return $erreurs;
    }
}
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

class CategorieDepenseV1Controller extends AbstractController
{

    /**
     * @Route("/CategorieDepense", name="CategorieDepense.index",methods={"GET"})
     */
    public function index()
    {

        return $this->redirectToRoute('CategorieDepense.showCategorieDepenses');
    }

    /**
     * @Route("/show/CategorieDepenses", name="CategorieDepense.showCategorieDepenses", methods={"GET"})
     */
    public function showCategorieDepenses(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        $categorieDepenses=NULL;
        $categorieDepenses=$doctrine->getRepository(CategorieDepense::class)->findAll([],['id'=>'ASC']);
        dump($categorieDepenses);
        return new Response($twig->render('TypeDepense/showCategorieDepenses.html.twig', ['categorieDepenses' => $categorieDepenses]));
    }
}
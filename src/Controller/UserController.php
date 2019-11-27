<?php


namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Twig\Environment;                            // template TWIG
use Symfony\Bridge\Doctrine\RegistryInterface;   // ORM Doctrine
use Symfony\Component\HttpFoundation\Request;    // objet REQUEST
use Symfony\Component\HttpFoundation\Response;    // objet RESPONSE

class UserController extends AbstractController
{
    /**
     * @Route("/admin/show/user", name="User.showUser", methods={"GET"})
     */
    public function showUser(Request $request, Environment $twig, RegistryInterface $doctrine) {

        $user=$doctrine->getRepository(User::class)->findAll([],['id'=>'ASC']);
        if($_SESSION['role']=='ROLE_ADMIN')
            return new Response($twig->render('User/showUser.html.twig',['users'=>$user]));
        else  return $this->redirectToRoute('Evenement.showEvenement');
    }

    /**
     * @Route("/admin/add/User", name=user.add", methods={"GET})
     */
    public function addUser(Request $request, Environment $twig, RegistryInterface $doctrine) {


    }
}

?>

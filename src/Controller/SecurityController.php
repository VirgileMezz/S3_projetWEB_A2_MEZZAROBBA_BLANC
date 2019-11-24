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

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="Security.login",methods={"GET"})
     */
   public function login(Request $request, Environment $twig, RegistryInterface $doctrine) {

       return new Response($twig->render('login.html.twig'));
   }

    /**
     * @Route("/validLogin", name="Security.validLogin",methods={"POST"})
     */
    public function validLogin(Request $request, Environment $twig, RegistryInterface $doctrine)
    {

        if (!$this->isCsrfTokenValid('formulaire_login_valide', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }

        $donnees['username'] = htmlspecialchars($_POST['username']);
        $donnees['password'] = htmlspecialchars($_POST['password']);

        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $donnees['username']]);

        if(empty($user)) {
            $erreurs['username']='Incorrect username';
            return $this->render('login.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs]);
        }
        if($user->getPassword()!=$donnees['password']) {
            $erreurs['password']='Incorrect password';
            return $this->render('login.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs]);
        }

        $_SESSION['username']=$user->getUsername();
        $_SESSION['role']=$user->getRole();


        return $this->redirectToRoute('Evenement.showEvenement');
    }
    /**
     * @Route("/logout", name="Security.logout",methods={"GET"})
     */
    public function logout(Request $request, Environment $twig, RegistryInterface $doctrine) {

        $_SESSION['username']=null;
        $_SESSION['role']=null;
        return $this->redirectToRoute('Evenement.showEvenement');
    }

}
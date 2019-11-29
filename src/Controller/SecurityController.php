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

       return new Response($twig->render('User/login.html.twig'));
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
            return $this->render('User/login.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs]);
        }
        if($user->getPassword()!=$donnees['password']) {
            $erreurs['password']='Incorrect password';
            return $this->render('User/login.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs]);
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

    /**
     * @Route("/register", name="Security.register",methods={"GET"})
     */
    public function register(Request $request, Environment $twig, RegistryInterface $doctrine) {

        return new Response($twig->render('User/register.html.twig'));
    }

    /**
     * @Route("/validRegister", name="Security.validRegister",methods={"POST"})
     */
    public function validRegister(Request $request, Environment $twig, RegistryInterface $doctrine)
    {

        if (!$this->isCsrfTokenValid('formulaire_register_valide', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $donnees['email'] = htmlspecialchars($_POST['email']);
        $donnees['username'] = htmlspecialchars($_POST['username']);
        $donnees['password'] = htmlspecialchars($_POST['password']);
        $donnees['rPassword'] = htmlspecialchars($_POST['rPassword']);

        $erreurs=$this->validDonnees($donnees);
        if(! empty($erreurs))
        {
            $categorie=$doctrine->getRepository(User::class)->findAll([],['id'=>'ASC']);
            $this->addFlash('error', 'des champs sont mal renseignés !');
            return $this->render('User/register.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs,'categorie'=> $categorie]);
        }else {
            $user=new User();
            $user->setRole('ROLE_CLIENT');
            $user->setEmail($donnees['email']);
            $user->setUsername($donnees['username']);
            $user->setPassword($donnees['password']);
            $user->setIsActive(1);
            $doctrine->getEntityManager()->persist($user);
            $doctrine->getEntityManager()->flush();
            $this->addFlash('success', 'client ajouté !');
            $_SESSION['username']=$user->getUsername();
            $_SESSION['role']=$user->getRole();
            return $this->redirectToRoute('Evenement.showEvenement');
        }
    }
    private function validDonnees($donnees) {

        $erreurs = array();
        if($donnees['email']==null)
            $erreurs['email']="Saisissez une adresse email";
        if($donnees['username']==null)
                $erreurs['username']="Saisissez un nom d'utilisateur";
        if($donnees['password']==null or $donnees['rPassword']==null)
            $erreurs['password']="Saisissez un mot de passe";
        if($donnees['password']!=$donnees['rPassword'])
            $erreurs['password']="Les deux mots de passe ne sont pas identiques";
        // a faire : si l'email ou le nom d'utilisateur existe déjà, fail
        return $erreurs;
    }
}
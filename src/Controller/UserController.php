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

        if($_SESSION)
            $twig->addGlobal('session', $_SESSION);
        $user=$doctrine->getRepository(User::class)->findAll([],['id'=>'ASC']);
        if($_SESSION['role']=='ROLE_ADMIN')
            return new Response($twig->render('User/showUser.html.twig',['users'=>$user]));
        else  return $this->redirectToRoute('Evenement.showEvenement');
    }

    /**
     * @Route("/admin/addUsre", name="user.addUser",methods={"GET"})
     */
    public function addUser(Request $request, Environment $twig, RegistryInterface $doctrine) {

        return new Response($twig->render('User/addUser.html.twig'));
    }

    /**
     * @Route("/validAddUser", name="User.validAddUser",methods={"POST"})
     */
    public function validAddUser(Request $request, Environment $twig, RegistryInterface $doctrine)
    {

        if (!$this->isCsrfTokenValid('formulaire_add_valide', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $donnees['email'] = htmlspecialchars($_POST['email']);
        $donnees['username'] = htmlspecialchars($_POST['username']);
        $donnees['password'] = htmlspecialchars($_POST['password']);
        $donnees['rPassword'] = htmlspecialchars($_POST['rPassword']);
        $donnees['role'] = htmlspecialchars($_POST['role']);

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
            $user->setRole($donnees['role']);
            $user->setIsActive(1);
            $doctrine->getEntityManager()->persist($user);
            $doctrine->getEntityManager()->flush();
            $this->addFlash('success', 'client ajouté !');
            $_SESSION['username']=$user->getUsername();
            $_SESSION['role']=$user->getRole();
            return $this->redirectToRoute('User.showUser');
        }
    }

    /**
     * @Route("/admin/edit/{id}/User", name="user.editUser", methods={"GET"})
     */
    public function editUser(Request $request, Environment $twig, RegistryInterface $doctrine, $id) {

        $user=$doctrine->getRepository(User::class)->findAll([],['id'=>'ASC']);
        if($_SESSION['role']=='ROLE_ADMIN') {
            $user=$doctrine->getRepository(User::class)->findOneBy(['id'=>$id]);
            $doctrine->getEntityManager()->remove($user);
            $donnees['id']=$id;
            $donnees['username']=$user->getUsername();
            $donnees['password']=$user->getPassword();
            $donnees['email']=$user->getEmail();
            $donnees['role']=$user->getRole();
            $donnees['isActive']=$user->getIsActive();

            return new Response($twig->render('User/editUserAdmin.html.twig',['donnees'=>$donnees]));
        }
        else  return $this->redirectToRoute('Evenement.showEvenement');
    }

    /**
     * @Route("/validEdit/User", name="User.validEditUser", methods={"POST"})
     */
    public function validEditUser(Request $request, Environment $twig, RegistryInterface $doctrine) {

        if(!$this->isCsrfTokenValid('edit_valid', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $donnees['id']=htmlspecialchars($_POST['id']);
        $donnees['username']=htmlspecialchars($_POST['username']);
        $donnees['password']=htmlspecialchars($_POST['password']);
        $donnees['email']=htmlspecialchars($_POST['email']);
        $donnees['role']=htmlspecialchars($_POST['role']);
        $donnees['isActive']=htmlspecialchars($_POST['isActive']);

        $erreurs=$this->validDonnees($donnees);
        if(! empty($erreurs))
        {
            $this->addFlash('error', 'des champs sont mal renseignés !');
            return $this->render('Evenement/editEvent.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs]);
        }else {
            $user = $doctrine->getRepository(User::class)->findOneBy(['id' => $donnees['id']]);
            $user->setUsername($donnees['username']);
            $user->setEmail($donnees['email']);
            $user->setPassword($donnees['password']);
            $doctrine->getEntityManager()->persist($user);
            $doctrine->getEntityManager()->flush();

            $this->addFlash('success', 'Evenement modifié !');
            return $this->redirectToRoute('User.showUser');
        }
    }

    /**
     * @Route("/admin/delete/{id}/User", name="user.deleteUser", methods={"DELETE"})
     */
    public function deleteUser(Request $request, Environment $twig, RegistryInterface $doctrine, $id) {

        $user=$doctrine->getRepository(User::class)->findAll([],['id'=>'ASC']);
        if($_SESSION['role']=='ROLE_ADMIN') {
            $user=$doctrine->getRepository(User::class)->findOneBy(['id'=>$id]);
            $doctrine->getEntityManager()->remove($user);
            $doctrine->getEntityManager()->flush();
            $this->addFlash('success', 'Utilisateur supprimé !');
            return $this->redirectToRoute('User.showUser');
        }
        else  return $this->redirectToRoute('Evenement.showEvenement');
    }

    public function validDonnees($donnees) {

    }
}

?>

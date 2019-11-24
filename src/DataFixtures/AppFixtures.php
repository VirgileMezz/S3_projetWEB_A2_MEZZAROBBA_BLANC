<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\Evenement;
use App\Entity\Categorie;
use App\Entity\User;
//use App\Entity\Commentaire;
//use App\Entity\Etat;
//use App\Entity\Panier;
//use App\Entity\Commande;
//use App\Entity\LigneCommande;

class AppFixtures extends Fixture
{
    // ...https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $this->loadCategories($manager);
        $this->loadUsers($manager);
        $this->loadEvenements($manager);

//        $this->loadEtatsCommandes($manager);
    }





    // fin TD
    public function loadCategories(objectManager $manager){
        // les types de evenements
        $Categories = [
            ['id' => 1,'libelle' => 'conference'],
            ['id' => 2,'libelle' => 'concert'],
            ['id' => 3,'libelle' => 'théatre'],
            ['id' => 4,'libelle' => 'danse'],
            ['id' => 5,'libelle' => 'divers'],
        ];
        foreach ($Categories as $type)
        {
            echo $type['libelle']."\n";
            $type_new = new Categorie();
            $type_new->setLibelle($type['libelle']);
            $manager->persist($type_new);
            $manager->flush();
        }
    }

    public function loadEvenements(objectManager $manager){
        echo " \n\nles evenements : \n";
        // les evenements

        $evenements = [
            ['id' => 1,'nom' => 'Symfony Conference', 'description' => 'présentation de la conférence sur Symfony ', 'date' => '2019-2-20', 'prix' => '10.5', 'categorie' =>'conference','image' =>'symfony.png'],
            ['id' => 2,'nom' => 'Laravel Conference', 'description' => 'présentation de la conférence sur Laravel ', 'date' => '2019-3-2', 'prix' => NULL, 'categorie' =>'conference','image' =>'laravel.jpg'],
            ['id' => 3,'nom' => 'Django Conference', 'description' => 'présentation de la conférence sur Django ', 'date' => '2019-3-25', 'prix' => '20', 'categorie' =>'conference'],
            ['id' => 4,'nom' => 'JAVA2EE Conference', 'description' => 'présentation de la conférence sur Java J2EE ', 'date' => '2019-4-2', 'prix' => '30', 'categorie' =>'conference'],
            ['id' => 5,'nom' => 'Rails Conference', 'description' => 'présentation de la conférence sur Ruby on Rails ', 'date' => '2019-4-26', 'prix' => '12', 'categorie' =>'conference'],
            ['id' => 6,'nom' => 'Concert tri yan', 'description' => 'concert tri yan axonne ', 'date' => '2019-4-26', 'prix' => '30', 'categorie' =>'concert','image' =>'TriYann2019.jpg'],
            ['id' => 7,'nom' => 'Le Lac Des Cygnes', 'description' => 'danse le lac des Cygnes', 'date' => '2019-4-26', 'prix' => '30', 'categorie' =>'danse','image' =>'LacDesCygnes2020.jpg'],
        ];

        foreach ($evenements as $evenement)
        {
            echo $evenement['nom']." - ".$evenement['prix']." € - ".$evenement['categorie']."\n";
            $new_evenement = new Evenement();
            $new_evenement->setNom($evenement['nom']);
            $new_evenement->setDescription($evenement['description']);
            $new_evenement->setPrix($evenement['prix']);
            $new_evenement->setDate(new \DateTime($evenement['date']));
            $new_evenement->setNombrePlaces(3);
            $new_evenement->setDisponible(True);
            if(isset($evenement['image']))
                $new_evenement->setPhoto($evenement['image']);
            else
                $new_evenement->setPhoto('noImage.jpg');
            $categorie=$manager->getRepository(Categorie::class)->findOneBy(['libelle'=>$evenement['categorie']]);
            $new_evenement->setCategorie($categorie);

            $manager->persist($new_evenement);
            $manager->flush();
        }
    }

    public function loadUsers(objectManager $manager){
        // les utilisateurs

        echo " \n\nles utilisateurs : \n";

        $admin = new User();
        $password = 'admin';
        $admin->setPassword($password);
        $admin->setRole('ROLE_ADMIN')
            ->setUsername('admin')->setEmail('admin@example.com')->setIsActive('1');
        $manager->persist($admin);
        echo $admin."\n";

        $client = new User();
        $client->setRole('ROLE_CLIENT')
            ->setUsername('client')->setEmail('client@example.com')->setIsActive('1');
        $password = 'client';
        $client->setPassword($password);
        $manager->persist($client);
        echo $client."\n";
        $client2 = new User();
        $client2->setRole('ROLE_CLIENT')
            ->setUsername('client2')->setEmail('client2@example.com')->setIsActive('1');
        $password = 'client2';
        $client2->setPassword($password);
        $manager->persist($client2);
        echo $client2."\n";
        $manager->flush();

    }
//    public function loadEtatsCommandes(objectManager $manager)
//    {
//        // état de la commande
//        $etat1 = new Etat();
//        $etat1->setNom('A préparer');
//        $manager->persist($etat1);
//        $etat2 = new Etat();
//        $etat2->setNom('Expédié');
//        $manager->persist($etat2);
//        $manager->flush();
//    }

}

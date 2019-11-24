<?php

namespace App\DataFixtures;

use App\Entity\CategorieDepense;
use App\Entity\Depense;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;

class Fixtures extends Fixture
{
    public function load(ObjectManager $manager) {
        $this->addCategorieDepenses($manager);
        $this->addDepenses($manager);
        $this->addUsers($manager);
        $manager->flush();
    }

    private function addDepenses(ObjectManager $manager) {
        $depenses=[

            ['montant' => '35','description' => 'PÃ©ages Belfort-Lyon', 'date_depense' => '2014-04-20' , 'categorieDepense_id' => 'Autoroute'],
            ['montant' => '410.47','description' => 'ComitÃ© de direction', 'date_depense' => '2014-07-03' , 'categorieDepense_id' => 'Repas'],
            ['montant' => '120','description' => 'rencontre Acrobtic', 'date_depense' =>'2014-08-18','categorieDepense_id' => 'Autoroute'],
            ['montant' => '25.5','description' => 'PÃ©ages Paris-Nantes', 'date_depense' => '2014-07-28' , 'categorieDepense_id' => 'Autoroute'],
            ['montant' => '45','description' => 'Sans plomb 95 35L', 'date_depense' => '2014-04-14' , 'categorieDepense_id' => 'Carburant'],
            ['montant' => '842','description' => 'Hotel Hilton Paris', 'date_depense' => '2014-01-06' , 'categorieDepense_id' => 'Hebergement'],
            ['montant' => '42.00','description' => 'PÃ©ages Belfort-Paris', 'date_depense' => '2014-12-07' , 'categorieDepense_id' => 'Hebergement'],
            ['montant' => '75','description' => 'Diezel 60L', 'date_depense' => '2014-10-31' , 'categorieDepense_id' => 'Carburant'],
        ];
        foreach ($depenses as $depense)
        {
            $new_depense=new Depense();
            $new_depense->setMontant($depense['montant']);
            $new_depense->setDescription($depense['description']);
            $new_depense->setDateDepense(new \DateTime($depense['date_depense']));
            $categorie=$manager->getRepository(CategorieDepense::class)->findOneBy(['libelle' => $depense['categorieDepense_id']]);
            $new_depense->setCategorieDepense($categorie);
            $manager->persist($new_depense);
            $manager->flush();
        }
    }

    private function addCategorieDepenses(ObjectManager $manager) {

        $categorieDepenses=[
            [ 'libelleCategorie' => 'Autoroute'],
            [ 'libelleCategorie' => 'Carburant'],
            [ 'libelleCategorie' => 'Repas'],
            [ 'libelleCategorie' => 'Hebergement'],
        ];

        foreach ($categorieDepenses as $categorieDepense) {

            $new_categorieDepense=new CategorieDepense();
            $new_categorieDepense->setLibelle($categorieDepense['libelleCategorie']);
            echo $new_categorieDepense->__toString();
            $manager->persist($new_categorieDepense);
            $manager->flush();
        }
    }

    private function addUsers(ObjectManager $manager) {

        $users=[
            [ 'username' => 'eblanc','password'=>'admin123','role'=>'ADMIN'],
            [ 'username' => 'mblanc','password'=>'m1234','role'=>'USER'],
            [ 'username' => 'qcant','password'=>'q1234','role'=>'USER'],
            [ 'username' => 'cdeloye','password'=>'c1234','role'=>'USER'],
        ];

        foreach ($users as $user) {

            $new_user=new User();
            $new_user->setUsername($user['username']);
            $new_user->setPassword($user['password']);
            $new_user->setRole($user['role']);
            $manager->persist($new_user);
            $manager->flush();
        }
    }
}

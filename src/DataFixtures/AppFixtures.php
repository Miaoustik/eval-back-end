<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use App\Entity\Candidature;
use App\Entity\CreateAccount;
use App\Entity\ProfilCandidat;
use App\Entity\ProfilRecruteur;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {

        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@admin.fr')
            ->setPassword($this->hasher->hashPassword($admin, 'test'))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $manager->flush();
    }

    public function fake(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@admin.fr')
            ->setPassword($this->hasher->hashPassword($admin, 'test'))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        for ($i = 1; $i <= 50; $i++) {

            $consultant = (new User())
                ->setRoles(['ROLE_CONSULTANT'])
                ->setEmail("consultant$i@consultant.fr");
            $consultant->setPassword($this->hasher->hashPassword($consultant, 'test'));
            $manager->persist($consultant);

            $recruteur = (new User())
                ->setEmail("recruteur$i@test.fr")
                ->setRoles(['ROLE_RECRUTEUR']);
            $recruteur->setPassword($this->hasher->hashPassword($recruteur, 'test'));
            $manager->persist($recruteur);

            $candidat = (new User())
                ->setEmail("candidat$i@test.fr")
                ->setRoles(['ROLE_CANDIDAT']);
            $candidat->setPassword($this->hasher->hashPassword($candidat, 'test'));
            $manager->persist($candidat);

            $guest = (new User())
                ->setEmail("test$i@test.fr")
                ->setRoles(['ROLE_GUEST']);

            $guest->setPassword($this->hasher->hashPassword($guest, "test"));
            $manager->persist($guest);

            $profilCandidat = (new ProfilCandidat())
                ->setUser($candidat)
                ->setCvName('Evaluation-5-Creer-et-administrer-une-base-de-donnees-docx-1-63af5449576a1.pdf')
                ->setFirstname("Candidat$i")
                ->setLastname("test$i");
            $manager->persist($profilCandidat);

            $profilRecruteur = (new ProfilRecruteur())
                ->setAddress("$i rue de Test")
                ->setPostalCode('81000')
                ->setCity('Albi')
                ->setSocietyName("Societe$i");
            $profilRecruteur->setUser($recruteur);
            $manager->persist($profilRecruteur);

            $annonce = (new Annonce())
                ->setTitle("Titre$i")
                ->setDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed gravida nunc ex, at dictum massa tincidunt vel. Pellentesque congue sollicitudin lectus, vel tincidunt nisi molestie quis. Curabitur posuere lectus ac elementum dapibus. Sed nunc lectus, lacinia nec dignissim quis, tempor quis justo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent ut accumsan turpis, sed suscipit dui. Nulla sagittis id erat ut feugiat. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Quisque bibendum, ipsum sed aliquam viverra, ligula orci volutpat lectus, non bibendum leo magna quis diam.")
                ->setHours(35)
                ->setSalary('35 000 brut');
            $annonce->setProfilRecruteur($profilRecruteur);
            $manager->persist($annonce);

            $createAccount = new CreateAccount();
            $createAccount->setUser($guest);
            $manager->persist($createAccount);



            if ($i % 2 === 0) {
                $annonce->setAllowed(false);
                $createAccount->setRole('ROLE_RECRUTEUR');
            } else {
                $annonce->setAllowed(true);
                $candidature = new Candidature($profilCandidat, $annonce);
                $manager->persist($candidature);
                $createAccount->setRole('ROLE_CANDIDAT');
            }
        }
        $manager->flush();
    }
}

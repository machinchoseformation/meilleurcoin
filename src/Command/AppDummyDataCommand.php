<?php

namespace App\Command;

use App\Entity\Ad;
use App\Entity\Category;
use App\Entity\Picture;
use App\Entity\User;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppDummyDataCommand extends Command
{
    protected static $defaultName = 'app:dummy-data';
    private $em;
    private $encoder;

    public function __construct(?string $name = null, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->encoder = $encoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Insert dummy data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $faker = \Faker\Factory::create('fr_FR');

        //vide les tables
        try {
            $this->em->getConnection()->exec("set foreign_key_checks=0; truncate picture");
            $this->em->getConnection()->exec("set foreign_key_checks=0; truncate user");
            $this->em->getConnection()->exec("set foreign_key_checks=0; truncate category");
            $this->em->getConnection()->exec("set foreign_key_checks=0; truncate ad");
            $this->em->getConnection()->exec("set foreign_key_checks=1;");
        }
        catch (DBALException $e){
            echo $e->getMessage();
        }

        $progressBar = new ProgressBar($output, 211);
        $progressBar->start();

        //crée les catégories
        $categoryNames = ["Emploi", "Véhicules", "Immobilier", "Vacances", "Multimédia",
            "Loisirs", "Matériel professionnel", "Services", "Maison", "Autres"];

        $categories = [];
        foreach($categoryNames as $n){
            $category = new Category();
            $category->setName($n);
            $this->em->persist($category);

            $categories[] = $category;
            $progressBar->advance();
        }
        $this->em->flush();

        //crée des users
        $users = [];

        //un admin
        $admin = new User();
        $admin->setEmail("yo@yo.com");
        $admin->setUsername("yo");
        $admin->setRoles(["ROLE_ADMIN"]);

        $hash = $this->encoder->encodePassword($admin, "yoyoyo");
        $admin->setPassword($hash);

        $admin->setDateRegistered($faker->dateTimeBetween('- 6 months'));
        $this->em->persist($admin);
        $progressBar->advance();

        //100 users
        //les mots de passe == username
        for($i=0; $i < 100; $i++){
            $user = new User();
            $user->setEmail($faker->unique()->email);
            $user->setUsername($faker->unique()->userName);
            $user->setRoles(["ROLE_USER"]);

            $hash = $this->encoder->encodePassword($user, $user->getUsername());
            $user->setPassword($hash);

            $user->setDateRegistered($faker->dateTimeBetween('- 6 months'));
            $this->em->persist($user);
            $users[] = $user;
            $progressBar->advance();
        }
        $this->em->flush();

        $ads = [];
        for($i=0; $i < 100; $i++){
            $ad = new Ad();
            $ad->setTitle($faker->catchPhrase);
            $ad->setDescription($faker->text);
            $ad->setDateCreated($faker->dateTimeBetween('- 6 months'));
            $ad->setPrice($faker->numberBetween(0,10000));
            $ad->setZip($faker->numberBetween(10000, 98999));
            $ad->setCity($faker->city);

            shuffle($categories); //mélange le tableau des catégories
            $ad->setCategory($categories[0]);

            //associe un user au hasard
            shuffle($users);
            $ad->setCreator($users[0]);

            $this->em->persist($ad);
            $ads[] = $ad;
            $progressBar->advance();
        }

        $this->em->flush();

        foreach($ads as $ad){
            $pic = new Picture();
            $pic->setFilename(mt_rand(1,37) . ".jpg");
            $pic->setDateCreated($faker->dateTimeBetween('- 6 months'));
            $pic->setAd($ad);

            $this->em->persist($pic);
        }
        $this->em->flush();


        $progressBar->finish();

        $io->note("admin username : yo, password : yoyoyo");

        $io->success('Done!');
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;    
    }

    public function load(ObjectManager $manager)
    {
        
        $faker = Factory::create('fr-FR');


        // Gestion des rôles
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Jean')
                  ->setLastName('Claude')
                  ->setEmail('jean@claude.fr')
                  ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                  ->setPicture("http://placeholder.it/400x400")
                  ->setIntroduction($faker->sentence())
                  ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                  ->addUserRole($adminRole);
        
        $manager->persist($adminUser);

        // Gestion des utilisateurs
        $users = [];
        $genres = ['male', 'female'];

        for($i = 0; $i <=10; $i++) {
            $user = new User();
            
            // https://randomuser.me/api/portraits/men/99.jpg

            $genre = $faker->randomElement($genres);
            $picture = "https://randomuser.me/api/portraits/";
            $pictureGenre = $genre == 'male' ? 'men' : 'women';
            $pictureId = $faker->numberBetween(0,99);
    
            $picture .= $pictureGenre . '/' . $pictureId . '.jpg';
            $hash = $this->encoder->encodePassword($user, 'password'); // ici je passe l'entité $user juste pour que l'encoder sache quel algo il faut utiliser (et que j'ai définit dans le security.yaml, où je précise que pour l'entité User, j'utilise bcrypt)

            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                 ->setHash($hash)
                 ->setPicture($picture);
            
            $manager->persist($user);
            $users[] = $user;

        }        


        // Gestion des annonces
        for($i = 0; $i < 30; $i++) {

            $ad = new Ad();
            $title = $faker->sentence(5);
            $coverImage = $faker->imageUrl(1000,350);
            $content ='<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
            $introduction = $faker->paragraph(2);
            $userId = $users[mt_rand(0,count($users) - 1)]; 
        
            $ad->setTitle($title)
               ->setCoverImage($coverImage)
               ->setContent($content)
               ->setPrice(mt_rand(40,200))
               ->setIntroduction($introduction)
               ->setRooms(mt_rand(1,5))
               ->setAuthor($userId);
            
            for($j = 0; $j < mt_rand(2,5); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl(500,300));
                $image->setCaption($faker->sentence());
                $image->setAd($ad);

                $manager->persist($image);

            }
            
            $manager->persist($ad);

        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Panel;
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

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstName('Usr')->setLastName('Temp')->setEmail('usr@email.com')->setPassword($this->hasher->hashPassword($user, '1234'));
        $manager->persist($user);

        $panel = new Panel();
        $panel->setUser($user)->setName('panel name')->setDomaine('domaine name');
        $manager->persist($panel);

        $manager->flush();
    }
}

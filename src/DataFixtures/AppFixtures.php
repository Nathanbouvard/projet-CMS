<?php

namespace App\DataFixtures;

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
        // 1. Super Admin
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setPseudo('BigBoss');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin'));
        $manager->persist($admin);

        // 2. Auteur
        $author = new User();
        $author->setEmail('auteur@test.com');
        $author->setPseudo('Victor Hugo');
        $author->setRoles(['ROLE_AUTHOR']);
        $author->setPassword($this->hasher->hashPassword($author, 'password'));
        $manager->persist($author);

        // 3. Éditeur
        $editor = new User();
        $editor->setEmail('editeur@test.com');
        $editor->setPseudo('Chief Editor');
        $editor->setRoles(['ROLE_EDITOR']);
        $editor->setPassword($this->hasher->hashPassword($editor, 'password'));
        $manager->persist($editor);

        // 4. Fournisseur de données
        $provider = new User();
        $provider->setEmail('data@test.com');
        $provider->setPseudo('Data Guy');
        $provider->setRoles(['ROLE_PROVIDER']);
        $provider->setPassword($this->hasher->hashPassword($provider, 'password'));
        $manager->persist($provider);

        // 5. Designer
        $designer = new User();
        $designer->setEmail('design@test.com');
        $designer->setPseudo('Picasso');
        $designer->setRoles(['ROLE_DESIGNER']);
        $designer->setPassword($this->hasher->hashPassword($designer, 'password'));
        $manager->persist($designer);

        $manager->flush();
    }
}
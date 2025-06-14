<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminUserCommand extends Command
{
    protected static $defaultName = 'app:create-admin-user';

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure()
    {
        $this->setName('app:create-admin-user')
            ->setDescription('Crée un utilisateur admin avec email admin@example.com et mot de passe password123.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setNom('Admin');      // Obligatoire
        $user->setPrenom('Super');  // Obligatoire aussi
        $user->setActif(true);      // Si la colonne ne peut pas être nulle
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));


        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Admin user créé avec succès (email: admin@example.com / mdp: password123)');

        return Command::SUCCESS;
    }
}
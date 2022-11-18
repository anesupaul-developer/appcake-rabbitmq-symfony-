<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utilities\UserTypes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SeedUsersCommand extends Command
{
    protected static $defaultName = 'seed:users';

    private EntityManagerInterface $entityManager;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;

        $this->passwordHasher = $passwordHasher;

        parent::__construct();
    }

    private array $defaultUsers = [
        [
            'firstname'     => 'Alice',
            'lastname'      => 'Wonderland',
            'username'      => 'admin@appcake.com',
            'password'      => 'admin@2022',
            'roles'         => [UserTypes::ADMINISTRATOR],
        ],
        [
            'firstname'     => 'Peter',
            'lastname'      => 'Pan',
            'username' => 'moderator@appcake.com',
            'password' => 'moderator@2022',
            'roles' => [UserTypes::MODERATOR]
        ]
    ];

    protected function configure(): void
    {
        $this->setDescription('Seed default user records');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /**
         * @var UserRepository $userRepository
         */
        $userRepository = $this->entityManager->getRepository(User::class);

        foreach ($this->defaultUsers as $defaultUser) {
            $existingUser = $userRepository->findOneBy(['username' => $defaultUser['username']]);
            if (empty($existingUser) === true) {
                $this->createNewUser($defaultUser);
            }

            $this->entityManager->flush();

            $io->success('New user username: '.$defaultUser['username'].' and password: '.$defaultUser['password'].PHP_EOL);
        }

        return Command::SUCCESS;
    }

    private function getEncryptedPassword(User $user, string $password): string
    {
        return $this->passwordHasher->hashPassword($user, $password);
    }

    private function createNewUser($defaultUser): void
    {
        $user = new User();
        $user->setFirstname($defaultUser['firstname']);
        $user->setLastname($defaultUser['lastname']);
        $user->setUsername($defaultUser['username']);
        $user->setPassword($this->getEncryptedPassword($user, $defaultUser['password']));
        $user->setRoles($defaultUser['roles']);

        $this->entityManager->persist($user);
    }
}

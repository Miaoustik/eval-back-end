<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:add-admin',
    description: 'Creates a new admin',
)]
class AddAdminCommand extends Command
{

    private EntityManagerInterface $manager;
    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher, EntityManagerInterface $manager, ValidatorInterface $validator, string $name = null)
    {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->hasher = $hasher;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to create an admin.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);

        $emailConstraint = new EmailConstraint();
        $emailConstraint->message = 'This email is not valid.';
        $emailAsk = new Question("Enter the email of the new admin user : [admin@admin.fr]\nEmail: ", 'admin@admin.fr');
        $passwordAsk = new Question('Enter the plain text password :');

        do {
            $email = $helper->ask($input, $output, $emailAsk);
            $errors = $this->validator->validate($email, $emailConstraint);
            $count = count($errors);
            if ($count !== 0) {
                foreach ($errors as $error) {
                    $io->note($error);
                }
            }
        } while ($count !== 0);

        do {
            $password = $helper->ask($input, $output, $passwordAsk);
            if (!$password) {
                $io->note('Enter at least 1 chars.');
            }
        } while ($password === null);

        $admin = new User();
        $admin->setEmail($email)
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($admin, $password));
        $this->manager->persist($admin);

        try {
            $this->manager->flush();
            $io->success("Admin user '$email' created.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            if ($e->getCode() == 1062) {
                $io->error('Email already taken.');
            } else {
                $io->error('Error with database. Code: ' . $e->getCode() . '. ' . $e->getMessage());
            }
            return Command::FAILURE;
        }
    }
}

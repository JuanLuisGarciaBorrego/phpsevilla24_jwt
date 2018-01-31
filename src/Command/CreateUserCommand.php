<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:jwt:create-user')
            ->setDescription('Creación de usuarios')
            ->addArgument('username', null, InputArgument::REQUIRED, 'Usuario')
            ->addArgument('password', null, InputArgument::REQUIRED, 'Clave')
            ->addArgument('email', null, InputArgument::REQUIRED, 'Email')
            ->addArgument('firstname', null, InputArgument::REQUIRED, 'Nombre')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $helper = $this->getHelper('question');

        $passwordQuestion = new Question("Clave: ");

        $username = $helper->ask($input, $output, new Question("Nombre de usuario: "));
        $password = $helper->ask($input, $output, $passwordQuestion->setHidden(true));

        $em = $this->getContainer()->get('doctrine')->getManager();
        $encoder = $this->getContainer()->get('security.password_encoder');

        $user = new User($username, "alex@alexdw.com","test");
        $user->setUsername($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $em->persist($user);
        $em->flush($user);


        $output->writeln('Usuario creado');
    }

}

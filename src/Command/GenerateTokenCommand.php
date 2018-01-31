<?php

namespace App\Command;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GenerateTokenCommand extends ContainerAwareCommand
{

    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:jwt:generate-token')
            ->setDescription('Genera un token para un usuario dado')
            ->addArgument('username', null, InputArgument::REQUIRED, 'Usuario')
            ->addArgument('password', null, InputArgument::REQUIRED, 'Clave')
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

        $user = $em->getRepository("App:User")->findOneByUsername($username);
        $isValid = $encoder->isPasswordValid($user,$password);

        if ($isValid){
            $token = $this->jwtManager->create($user);
            $output->writeln('<success>Usuario correcto</success>');
            $output->writeln("Token: {$token}");
        }else{
            $output->writeln('<error>Usuario incorrecto</error>');
        }
    }

}

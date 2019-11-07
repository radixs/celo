<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class FrontEndController extends AbstractController
{
    /**
     * @Route("/front/end", name="front_end")
     */
    public function index(LoggerInterface $logger)
    {
        $logger->info('I just got the logger');
        $logger->error('An error occurred');

        $logger->critical('I left the oven on!', [
            // include extra "context" info in your logs
            'cause' => 'in_hurry',
        ]);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/FrontEndController.php',
        ]);
    }
}

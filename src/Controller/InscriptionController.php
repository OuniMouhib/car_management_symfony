<?php // src/Controller/InscriptionController.php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class InscriptionController extends AbstractController
{
    #[Route('/number',name:"Accueil")]
public function number()
{

$number = random_int(0, 100);
return $this->render('Inscription/accueil.html.twig', [
    'number' => $number,
    ]);
}
}
?>
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Job;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Image;
use App\Entity\Candidature;
USE Doctrine\ORM\EntityManager;


class JobController extends AbstractController
{
    /** 
     *@Route("/job", name="job")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {

        $job = new Job();
        $job->setType('Architecte');
        $job->setCompany('OffShoreBox');
        $job->setDescription('Genie logiciel ');
        $job->setExpiresAt(new \DateTimeImmutable());
        $job->setEmail('haykel@gmail.com');
        $image = new Image();
        $image->setUrl('https://cdn.pixabay.com/photo/2015/10/30/10/03/gold-1013618_960_720.jpg');
        $image->setAlt('Job de rêves');
        $job->setImage($image);
        $candidature1=new Candidature();
        $candidature1->setCandidat("rhaiem");
        $candidature1->setContenu("formation J2EE");
$candidature1->setDate(new \DateTime());
$candidature2=new Candidature();
$candidature2->setCandidat("Salima"); $candidature2->setContenu("formation Symfony");
$candidature2->setDate(new \DateTime());
$candidature1->setJob ($job); $candidature2->setJob ($job); 
        $entityManager->persist($job->getImage());
        $entityManager->persist($job);
        $entityManager ->persist($candidature1);
        $entityManager ->persist($candidature2);

        $entityManager->flush();
        return $this->render('job/index.html.twig', [
            'id' => $job->getId(),
        ]);
    }
    /**
     * @Route("/job/{id}", name="job_show")
     */
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {

        $job = $entityManager->getRepository(job::class)->find($id);
        $listCandidatures=$entityManager->getRepository(Candidature::class)->findBy(['Job'=>$job]);
       
        if (!$job) {
            throw $this->createNotFoundException(
                'No job found for id ' . $id
            );
        }
        
        return $this->render('job/show.html.twig', [
        'job' => $job,
         'listCandidatures' => $listCandidatures]);
        
}
/** 
 * @Route("/Ajouter" , name="ajouter")
*/
public function ajouter(EntityManagerInterface $entityManager ,Request $request)
{
$candidat = new Candidature();
$fb = $this->createFormBuilder($candidat)
->add('candidat', TextType::class)
->add('contenu', TextType::class, array("label" => "Contenu"))
->add('date', DateType::class)
->add('job', EntityType::class, [ 'class' => Job::class,

'choice_label' => 'type',
])
->add('Valider', SubmitType::class);
// générer le formulaire à partir du FormBuilder
$form = $fb->getForm();
$form->handleRequest($request);
if ($form->isSubmitted()) {
$entityManager->persist($candidat);
$entityManager->flush();
return $this->redirectToRoute('Accueil');
}
return $this ->render('job/ajouter.html.twig',['f' => $form->createView()]);
}
/**
*@Route("/add",name="ajout_job")
**/
public function ajouter2(EntityManagerInterface $entityManager,Request $request){

    $job = new Job();
    $form = $this->createForm("App\Form\JobType", $job);
    $form->handleRequest($request);
    if ($form->isSubmitted())
    {
    $entityManager->persist($job);
    $entityManager->flush();
    return $this->redirectToRoute('Accueil');
    }


 return $this->render('job/ajouter.html.twig',
['f' => $form->createView()] );
}

/** 
*@Route("/", name="home")
**/
public function home(EntityManagerInterface $entityManager ,Request $request)
{
    $form = $this->createFormBuilder()
    ->add("critere",TextType::class)
    ->add("valider", SubmitType::class)
    ->getForm();
    $form ->handleRequest($request);

     $repo = $entityManager->getRepository (Candidature::class); $lesCandidats = $repo->findAll();
     if ($form->isSubmitted())
     {
     $data=$form->getData();
    $lesCandidats= $repo->recherche($data['critere']);
     }
return $this->render('job/home.html.twig', ['lesCandidats' => $lesCandidats,'form' =>$form->createView()]);
}
/**
*@Route("/supp/{id}", name="cand_delete")
**/
public function delete (EntityManagerInterface $entityManager,Request $request, $id): Response
{
$c=$entityManager->getRepository (Candidature::class)->find($id);
if (!$c) {

throw $this->createNotFoundException('No job·found for id· '.$id);
}
$entityManager->remove($c);
$entityManager->flush();
return $this->redirectToRoute('home');
}
/**
* @Route("/editU/{id}", name="edit_user")
* Method({"GET","POST"})
*/
public function edit(EntityManagerInterface $entityManager , Request $request, $id)
{ $candidat = new Candidature();
$candidat = $entityManager->getRepository(Candidature::class)
->find($id);
if (!$candidat) {
throw $this->createNotFoundException(
'No candidat found for id '.$id
);
}
$fb = $this->createFormBuilder($candidat)
->add('candidat', TextType::class)
->add('contenu', TextType::class, array("label" => "Contenu"))
->add('date', DateType::class)
->add('job', EntityType::class, [
'class' => Job::class,
'choice_label' => 'type',
])
->add('Valider', SubmitType::class);
// générer le formulaire à partir du FormBuilder
$form = $fb->getForm();
$form->handleRequest($request);
if ($form->isSubmitted()) {

$entityManager->flush();
return $this->redirectToRoute('home');
}
return $this->render('job/ajouter.html.twig',
['f' => $form->createView()] );
}
/** 
*@Route("/", name="listjob")
**/
public function listeJobs(EntityManagerInterface $entityManager)
{
     $repo = $entityManager->getRepository (Job::class); $job= $repo->findAll();
return $this->render('job/listjob.html.twig', ['lesjobs' => $job]);
}

}

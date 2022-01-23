<?php

namespace App\Controller;


use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/homeAdd', name: 'home')]
    public function index(Request $request,ManagerRegistry $managerRegistry): Response
    {
      $article=new Product();
        $form= $this->createForm(ProductFormType::class,$article);

        $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()){
          // $file=$home->getImage();
          /*  $fileName =md5(uniqid()).'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch(FileException $e){
            }*/

          $brochureFile = $form->get('image')->getData();

          // this condition is needed because the 'brochure' field is not required
          // so the PDF file must be processed only when a file is uploaded
          if ($brochureFile) {
              $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
              // this is needed to safely include the file name as part of the URL
              $safeFilename = $originalFilename;
              $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

              // Move the file to the directory where brochures are stored
              try {
                  $brochureFile->move(
                      $this->getParameter('images_directory'),
                      $newFilename
                  );
              } catch (FileException $e) {
                  // ... handle exception if something happens during file upload
              }

              // updates the 'brochureFilename' property to store the PDF file name
              // instead of its contents
              $article->setImage($newFilename);
          }
          $em = $managerRegistry->getManager();
          // $home->setImage($fileName);
          $em->persist($article);
          $em->flush();
          $this->addFlash(
              'success',
              "Bien Enregistrer"
          );
          return $this->redirectToRoute('home');


      }

        return $this->render('home/index.html.twig',[
            'form' => $form->createView()
        ]);
    }

   #[Route('/afficher', name: 'h')]
    public function Afficher(ProductRepository $repo){
       // $Listarticles=$this->getDoctrine()->getRepository(ArticleRepository::class)->findAll();
        $Listarticles= $repo->findAll();
        return $this->render('home/afficher.html.twig', [
            'Listarticles'=> $Listarticles
        ]);

    }




}

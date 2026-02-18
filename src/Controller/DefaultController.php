<?php

namespace App\Controller;

use App\Form\CSVType;
use App\Form\TestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(CSVType::class);
        $form->handleRequest($request);

        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('file')->getData();

            if ($file instanceof UploadedFile) {

                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';

                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($uploadDir, $fileName);

                $finalPath =  $uploadDir . '/' . $fileName;

                $projectDir = $this->getParameter('kernel.project_dir');

                $python = "python3";//$projectDir.'/venv/bin/python3.12';
                $script = $projectDir.'/python/kaggle.py';

                $command = "$python $script -f $finalPath 2>&1";
                $result = shell_exec($command);
            }
        }

        return $this->render('default/index.html.twig', [
            'form' => $form,
            'result'=> $result,
        ]);
    }

    #[Route('/test', name: 'app_test')]
    public function test(Request $request): Response
    {
        $form = $this->createForm(TestType::class);
        $form->handleRequest($request);

        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {

            $sepalLengthCm = $form->get('sepalLengthCm')->getData();
            $sepalWidthCm = $form->get('sepalWidthCm')->getData();
            $petalLengthCm = $form->get('petalLengthCm')->getData();
            $petalWidthCm = $form->get('petalWidthCm')->getData();
            

            $projectDir = $this->getParameter('kernel.project_dir');

            $python =  $python = "python3";//$projectDir.'/venv/bin/python3.12';
            $script = $projectDir.'/python/kaggle.py';

            $command = "$python $script -n $sepalLengthCm $sepalWidthCm $petalLengthCm $petalWidthCm";
            $result = shell_exec($command);
        }

        return $this->render('default/test.html.twig', [
            'form' => $form,
            'result'=> $result,
        ]);
    }

    #[Route('/train', name: 'app_train', methods: ['POST'])]
    public function train(): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');

        $python =  $python = "python3";//$projectDir.'/venv/bin/python3.12';
        $script = $projectDir.'/python/kaggle.py';

        $result = shell_exec("$python $script -t");

        return new Response($result ?? 'Entraînement terminé');
    }

     #[Route('/delete', name: 'app_delete', methods: ['POST'])]
    public function delete(): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');

        $python =  $python = "python3";//$projectDir.'/venv/bin/python3.12';
        $script = $projectDir.'/python/kaggle.py';

        $result = shell_exec("$python $script -d");

        return new Response($result ?? 'Base vidée');
    }
}

<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\ProjectImage;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="project_index", methods={"GET"})
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="project_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectImages = $form->get('project_image')->getData();

            foreach ($projectImages as $projectImage) {
                $file = md5(uniqid()).'.'.$projectImage->guessExtension();
                $projectImage->move($this->getParameter('project_directory'), $file);

                $projectImage = new ProjectImage();
                $projectImage->setUrl($file);
                $project->addProjectImage($projectImage);
            }

            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="project_show", methods={"GET"})
     */
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $images = $project->getProjectImages();
        foreach($images as $images) {
            $names[] = $images->getUrl();
            
        }
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach($names as $name) {
                unlink($this->getParameter('project_directory').'/'.$name);
            }

            $projectImages = $form->get('project_image')->getData();

            foreach ($projectImages as $projectImage) {
                $file = md5(uniqid()).'.'.$projectImage->guessExtension();
                $projectImage->move($this->getParameter('project_directory'), $file);

                $projectImage = new ProjectImage();
                $projectImage->setUrl($file);
                $project->addProjectImage($projectImage);
            }
            $entityManager->flush();

            return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="project_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $images = $project->getProjectImages();
        foreach($images as $images) {
            $names[] = $images->getUrl();
            
        }

        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            foreach($names as $name) {
                if(file_exists($this->getParameter('project_directory').'/'.$name)) {
                    unlink($this->getParameter('project_directory').'/'.$name);
                }
            }
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
    }
}

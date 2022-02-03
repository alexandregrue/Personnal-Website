<?php

namespace App\Controller;

use App\Entity\Resume;
use App\Form\ResumeType;
use App\Repository\ResumeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/resume")
 */
class ResumeController extends AbstractController
{
    /**
     * @Route("/", name="resume_index", methods={"GET"})
     */
    public function index(ResumeRepository $resumeRepository): Response
    {
        return $this->render('resume/index.html.twig', [
            'resumes' => $resumeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="resume_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $resume = new Resume();
        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $resumeFile = $form->get('resume')->getData();

            $file = md5(uniqid()).'.'.$resumeFile->guessExtension();
            $resumeFile->move($this->getParameter('resume_directory'), $file);

            $resume->setName($file);


            $entityManager->persist($resume);
            $entityManager->flush();

            return $this->redirectToRoute('resume_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('resume/new.html.twig', [
            'resume' => $resume,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="resume_show", methods={"GET"})
     */
    public function show(Resume $resume): Response
    {
        return $this->render('resume/show.html.twig', [
            'resume' => $resume,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="resume_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Resume $resume, EntityManagerInterface $entityManager): Response
    {
        $name = $resume->getName();
        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            unlink($this->getParameter('resume_directory').'/'.$name);
            $resumeFile = $form->get('resume')->getData();

            $file = md5(uniqid()).'.'.$resumeFile->guessExtension();
            $resumeFile->move($this->getParameter('resume_directory'), $file);

            $resume->setName($file);

            $entityManager->flush();

            return $this->redirectToRoute('resume_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('resume/edit.html.twig', [
            'resume' => $resume,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="resume_delete", methods={"POST"})
     */
    public function delete(Request $request, Resume $resume, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resume->getId(), $request->request->get('_token'))) {
            $entityManager->remove($resume);
            $entityManager->flush();
        }

        return $this->redirectToRoute('resume_index', [], Response::HTTP_SEE_OTHER);
    }
}

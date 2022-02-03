<?php

namespace App\Controller;

use App\Entity\Stack;
use App\Form\StackType;
use App\Repository\StackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class StackController extends AbstractController
{
    /**
     * @Route("/skills", name="skills", methods={"GET"})
     */
    public function index(StackRepository $stackRepository): Response
    {
        return $this->render('stack/index.html.twig', [
            'stacks' => $stackRepository->findAll(),
        ]);
    }

    /**
     * @Route("/stack/new", name="stack_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stack = new Stack();
        $form = $this->createForm(StackType::class, $stack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stackFile = $form->get('stack')->getData();

            $file = md5(uniqid()).'.'.$stackFile->guessExtension();
            $stackFile->move($this->getParameter('stack_directory'), $file);

            $stack->setUrl($file);


            $entityManager->persist($stack);
            $entityManager->flush();

            return $this->redirectToRoute('skills', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stack/new.html.twig', [
            'stack' => $stack,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/stack/{id}/edit", name="stack_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Stack $stack, EntityManagerInterface $entityManager): Response
    {
        $name = $stack->getUrl();
        $form = $this->createForm(StackType::class, $stack);
        $form->handleRequest($request);
        $name = $stack->getUrl();

        if ($form->isSubmitted() && $form->isValid()) {
            unlink($this->getParameter('stack_directory').'/'.$name);

            $stackFile = $form->get('stack')->getData();

            $file = md5(uniqid()).'.'.$stackFile->guessExtension();
            $stackFile->move($this->getParameter('stack_directory'), $file);
            

            $stack->setUrl($file);

            
            $entityManager->flush();

            return $this->redirectToRoute('skills', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stack/edit.html.twig', [
            'stack' => $stack,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/stack/{id}", name="stack_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Stack $stack, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stack->getId(), $request->request->get('_token'))) {
            unlink($this->getParameter('stack_directory').'/'.$stack->getUrl());
            $entityManager->remove($stack);
            $entityManager->flush();
        }

        return $this->redirectToRoute('skills', [], Response::HTTP_SEE_OTHER);
    }
}

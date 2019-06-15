<?php

namespace Libr\CRUDBundle\Controller;

use Libr\CRUDBundle\Entity\Authors;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Author controller.
 *
 * @Route("authors")
 */
class AuthorsController extends Controller
{
    /**
     * Lists all author entities.
     *
     * @Route("/", name="authors_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $authors = $em->getRepository('LibrCRUDBundle:Authors')->findAll();

        return $this->render('authors/index.html.twig', array(
            'authors' => $authors,
        ));
    }

    /**
     * Finds and displays a author entity.
     *
     * @Route("/{authorId}", name="authors_show")
     * @Method("GET")
     */
    public function showAction(Authors $author)
    {

        return $this->render('authors/show.html.twig', array(
            'author' => $author,
        ));
    }

    /**
     * @Route("/changeName")
     * @Method("POST")
     */
    public function changeNameAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $author = $em->getRepository('LibrCRUDBundle:Authors')->find($request->get('authorId'));
        $author->setFirstName($request->get('authorFirstName'));
        $author->setSecondName($request->get('authorSecondName'));
        try {
            $em->persist($author);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(json_encode($e->getMessage()));
        }
        return $this->redirectToRoute('books_index');
    }

}

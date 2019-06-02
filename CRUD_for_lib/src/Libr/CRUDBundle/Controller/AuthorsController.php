<?php

namespace Libr\CRUDBundle\Controller;

use Libr\CRUDBundle\Entity\Authors;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * @Route("/changeName/{authorId}/{authorName}/{authorSecondName}")
     */
    public function changeNameAction($authorId, $authorName, $authorSecondName){
        $em = $this->getDoctrine()->getManager();
        $author = $em->getRepository('LibrCRUDBundle:Authors')->find($authorId);
        $author->setAuthorName($authorName);
        $author->setAuthorSecondName($authorSecondName);
        $em->persist($author);
        $em->flush();
        return $this->redirectToRoute('books_index');
    }

}

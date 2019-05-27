<?php

namespace Libr\CRUDBundle\Controller;

use Libr\CRUDBundle\Entity\Authors;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
}

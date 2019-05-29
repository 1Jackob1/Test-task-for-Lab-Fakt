<?php

namespace Libr\CRUDBundle\Controller;

use Libr\CRUDBundle\Entity\Authors;
use Libr\CRUDBundle\Entity\Books;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;

/**
 * Book controller.
 *
 * @Route("books")
 */
class BooksController extends Controller
{
    /**
     * Lists all book entities.
     *
     * @Route("/", name="books_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $books = $em->getRepository('LibrCRUDBundle:Books')->findAll();

        return $this->render('books/index.html.twig', array(
            'books' => $books,
        ));
    }

    /**
     * Finds and displays a book entity.
     *
     * @Route("/{bookId}", name="books_show")
     * @Method("GET")
     */
    public function showAction(Books $book)
    {

        return $this->render('books/show.html.twig', array(
            'book' => $book,
        ));
    }

    /**
     * @Route("/addAuthor/{bookId}/{authorName}/{authorSecondName}", name="add_Book_Author")
     */
    public function addAuthorAction($bookId, $authorName, $authorSecondName){
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($bookId);
        $author = new Authors();
        $author->setAuthorName($authorName);
        $author->setAuthorSecondName($authorSecondName);
        $em->persist($author);
        $book->addAuthor($author);
        $em->flush();
        return $this->redirectToRoute('books_index');

    }



}

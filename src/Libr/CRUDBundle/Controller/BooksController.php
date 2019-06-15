<?php

namespace Libr\CRUDBundle\Controller;

use DateTime;
use Exception;
use Libr\CRUDBundle\Entity\Authors;
use Libr\CRUDBundle\Entity\Books;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        return $this->render('books/index.html.twig', array(
            'books' => $em->getRepository('LibrCRUDBundle:Books')->findAll(),
            'authors' => $em->getRepository('LibrCRUDBundle:Authors')->findAll()
        ));
    }

    /**
     * @Route("/sort", name="sort_books_by")
     * @Method("GET")
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository('LibrCRUDBundle:Books')->findBy(array(), array($request->query->get('field') => $request->query->get('order')));
        $authors = $em->getRepository('LibrCRUDBundle:Authors')->findAll();
        return $this->render('books/index.html.twig', array(
            'books' => $books,
            'authors' => $authors
        ));
    }

//    /**
//     * Finds and displays a book entity.
//     *
//     * @Route("/{bookId}", name="books_show")
//     * @Method("GET")
//     */
//    public function showAction(Books $book)
//    {
//        return $this->render('books/show.html.twig', array(
//            'book' => $book,
//        ));
//    }

    /**
     * @Route("/addAuthor", name="add_Book_Author")
     * @Method("POST")
     */
    public function addAuthorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($request->request->get('bookId'));
        $author = new Authors();
        $author->setFirstName($request->request->get('authorFirstName'));
        $author->setSecondName($request->request->get('authorSecondName'));
        try {
            $em->persist($author);
            $book->addAuthor($author);
            $em->flush();
        } catch (Exception $exception) {
            return $this->errorProcessAction($exception);
        }

        return $this->redirectToRoute('books_index', array(), 302);

    }

    /**
     * @Route("/form", name="form_process")
     * @Method("POST")
     */
    public function formProcess()
    {
        $book = new Books();
        $book->setDescription($_POST['book_desc']);
        $book->setImgPath('/uploaded_imgs/' . md5(time()) . basename($_FILES['book_img']['name']));
        $book->setTitle($_POST['book_title']);
        $book->setPublicationDate(new DateTime($_POST['book_date']));
        move_uploaded_file($_FILES['book_img']['tmp_name'], '/home/jackob/Desktop/MaProj/Test-task-for-Lab-Fakt/CRUD_for_lib/web' . $book->getImgPath());
        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();

        return $this->redirectToRoute('books_index', array(), 302);
    }

    /**
     * @Route("/registerAuthor/{authorId}/{bookId}", name="attach_author")
     * @Method("POST")
     */
    public function registerAuthorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($request->request->get('bookId'));
        $author = $em->getRepository('LibrCRUDBundle:Authors')->find($request->request->get('authorId'));
        $savedAuthor = null;
        foreach ($book->getAuthor() as $authorBook) {
            if ($author == $authorBook) {
                $savedAuthor = $authorBook;
                break;
            }
        }

        if ($request->request->get('isAttaching') == '1') {
            if ($savedAuthor == null) {
                $book->addAuthor($author);
            }
        } else {
            if ($savedAuthor != null) {
                $book->removeAuthor($savedAuthor);
            }
        }
        try {
            $em->flush();
        } catch (Exception $exception) {
            return $this->errorProcessAction($exception);
        }
        return $this->redirectToRoute('books_index', array(), 302);
    }


    /**
     * @Route("/editBookTitle", name="edit_book_title")
     * @Method("POST")
     */
    public function editBookTitle(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($request->get('bookId'));
        switch ($request->get('field')) {
            case 'title':
                $book->setTitle($request->get('newTitle'));
                break;
            case 'desc':
                $book->setDescription($request->get('newDesc'));
                break;
            case 'date':
                $book->setPublicationDate(new DateTime($request->get('newDate')));
                break;
        }
        try {
            $em->flush();
        } catch (Exception $exception) {
            return $this->errorProcessAction($exception);
        }

        return $this->redirectToRoute('books_index', array(), 302);
    }

    /**
     * @Route("/setNewImg/{bookId}", name="set_new_img")
     */
    public function setNewImg($bookId)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($bookId);
        $book->setImgPath('/uploaded_imgs/' . md5(time()) . basename($_FILES['new_book_img']['name']));
        try {
            $this->container->get('file_loader')->saveFile($_FILES['new_book_img']['tmp_name'],
                $this->container->get('kernel')->getRootDir() . '/../web' . $book->getImgPath());
            $em->flush();
        } catch (Exception $exception) {
            return $this->errorProcessAction($exception);
        }

        return $this->redirectToRoute('books_index', array(), 302);
    }

    /**
     * @Route("/query/native", name="native_query")
     * @Method("GET")
     */
    public function nativeQueryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $sql = "SELECT books.id AS id FROM books_authors 
                INNER JOIN books ON books.id = books_authors.book_id 
                GROUP BY (books.id)
                HAVING COUNT(*) > 1";
        $rsm = $em->getConnection()->prepare($sql);
        $rsm->execute();
        $queryResult = $rsm->fetchAll();
        $booksRep = $em->getRepository('LibrCRUDBundle:Books');
        $books = array();
        foreach ($queryResult as $el)
            $books[] = $booksRep->find($el['id']);
        return $this->render(':books:index.html.twig', array(
            'books' => $books,
            'authors' => $em->getRepository('LibrCRUDBundle:Authors')->findAll()
        ));

    }

    /**
     * @Route("/query/doctrine", name="doctrine_query")
     * @Method("GET")
     */
    public function doctineQueryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();
        $qb->select('b.id')
            ->from('LibrCRUDBundle:Books', 'b')
            ->join('b.author', 'author')
            ->groupBy('b.id')
            ->having($qb->expr()->count('b.id') . '>1');
        $queryResult = $qb->getQuery()->getResult();
        $booksRep = $em->getRepository('LibrCRUDBundle:Books');
        $books = array();
        foreach ($queryResult as $el)
            $books[] = $booksRep->find($el['bookId']);
        return $this->render(':books:index.html.twig', array(
            'books' => $books,
            'authors' => $em->getRepository('LibrCRUDBundle:Authors')->findAll()
        ));
    }

    /**
     * @param Exception $exception
     * @return Response
     */
    public function errorProcessAction(Exception $exception){
        $this->get('logger')->addError($exception->getMessage(), array($exception->getCode(), $exception->getFile(), $exception->getLine()));
        return new Response($exception->getMessage(), 406);
    }
}

<?php

namespace Libr\CRUDBundle\Controller;

use DateTime;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\TextType;
use Exception;
use Libr\CRUDBundle\Entity\Authors;
use Libr\CRUDBundle\Entity\Books;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $book = new Books();
        $form = $this->createFormBuilder($book)
            ->add('title', TextType::TEXT)
            ->add('description', TextType::TEXT)
            ->add('publicationDate', DateType::DATE)
            ->add('imgPath', FileType::class)
            ->add('Save_book.', SubmitType::class, ["label" => "Crate Book"])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form["imgPath"]->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessClientExtension();
            try {
                $file->move($this->get('kernel')->getRootDir() . "/../web/uploaded_imgs", $fileName);

            } catch (FileException $exception) {
                return $this->errorProcessAction($exception);
            }
            $book->setImgPath("/uploaded_imgs/" . $fileName);
            try {
                $em->persist($book);
                $em->flush();
            } catch (Exceptio $exception) {
                return $this->errorProcessAction($exception);
            }
        }

        return $this->render('books/index.html.twig', [
            'books' => $em->getRepository('LibrCRUDBundle:Books')->findAll(),
            'authors' => $em->getRepository('LibrCRUDBundle:Authors')->findAll(),
            'new_book_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/sort", name="sort_books")
     * @Method("GET")
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository('LibrCRUDBundle:Books')->findBy([], [$request->query->get('field') => $request->query->get('order')]);
        $authors = $em->getRepository('LibrCRUDBundle:Authors')->findAll();
        return $this->render('books/index.html.twig', [
            'books' => $books,
            'authors' => $authors
        ]);
    }

    /**
     * @Route("/addAuthor", name="add_book_author")
     * @Method("POST")
     */
    public function addAuthorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($request->get('bookId'));
        $author = new Authors();
        $author->setFirstName($request->get('authorFirstName'));
        $author->setSecondName($request->get('authorSecondName'));
        try {
            $em->persist($author);
            $book->addAuthor($author);
            $em->flush();
        } catch (Exception $exception) {
            return $this->errorProcessAction($exception);
        }

        return $this->redirectToRoute('books_index', [], 302);
    }


    /**
     * @Route("/registerAuthor", name="attach_author")
     * @Method("POST")
     */
    public function registerAuthorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($request->get('bookId'));
        $author = $em->getRepository('LibrCRUDBundle:Authors')->find($request->get('authorId'));
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
        return $this->redirectToRoute('books_index', [], 302);
    }


    /**
     * @Route("/editBookField", name="edit_book_field")
     * @Method("POST")
     */
    public function editBookField(Request $request)
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
            $this->get('file_loader')->saveFile($_FILES['new_book_img']['tmp_name'],
                $this->get('kernel')->getRootDir() . '/../web' . $book->getImgPath());
            $em->flush();
        } catch (Exception $exception) {
            return $this->errorProcessAction($exception);
        }

        return $this->redirectToRoute('books_index', [], 302);
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
        $books = [];
        foreach ($queryResult as $el) {
            $books[] = $booksRep->find($el['id']);
        }
        return $this->render(':books:index.html.twig', [
            'books' => $books,
            'authors' => $em->getRepository('LibrCRUDBundle:Authors')->findAll()
        ]);
    }

    /**
     * @Route("/query/doctrine", name="doctrine_query")
     * @Method("GET")
     */
    public function doctrineQueryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('b.id')
            ->from('LibrCRUDBundle:Books', 'b')
            ->join('b.author', 'author')
            ->groupBy('b.id')
            ->having($qb->expr()->count('b.id') . '>1');
        $queryResult = $qb->getQuery()->getResult();
        $booksRep = $em->getRepository('LibrCRUDBundle:Books');
        $books = array();
        foreach ($queryResult as $el) {
            $books[] = $booksRep->find($el['id']);
        }
        return $this->render(':books:index.html.twig', [
            'books' => $books,
            'authors' => $em->getRepository('LibrCRUDBundle:Authors')->findAll()
        ]);
    }

    /**
     * @param Exception $exception
     * @return Response
     */
    public function errorProcessAction(Exception $exception)
    {
        $this->get('logger')->addError($exception->getMessage(), [$exception->getCode(), $exception->getFile(), $exception->getLine()]);
        return new Response($exception->getMessage(), 406);
    }


}

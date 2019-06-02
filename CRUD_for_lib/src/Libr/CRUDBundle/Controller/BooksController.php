<?php

namespace Libr\CRUDBundle\Controller;

use Doctrine\DBAL\Types\TextType;
use Libr\CRUDBundle\Entity\Authors;
use Libr\CRUDBundle\Entity\Books;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
     * @Route("/{sortBy}", defaults={"sortBy" = "none"}, name="sort_books_by")
     * @Method("GET")
     */
    public function sortByAction($sortBy){
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository('LibrCRUDBundle:Books')->findAll();
        switch ($sortBy){
            case "none":
                break;
            case "title":
                $books = $em->getRepository('LibrCRUDBundle:Books')->findBy(array(), array('bookName' => 'ASC'));
                break;
            case "img":
                $books = $em->getRepository('LibrCRUDBundle:Books')->findBy(array(), array('bookImg' => 'ASC'));
                break;
            case "rel_date":
                $books = $em->getRepository('LibrCRUDBundle:Books')->findBy(array(), array('bookPubDate' => 'ASC'));
                break;
            case "description":
                $books = $em->getRepository('LibrCRUDBundle:Books')->findBy(array(), array('bookDesc' => 'ASC'));
                break;
            case "title_d":
                $books = $em->getRepository('LibrCRUDBundle:Books')->findBy(array(), array('bookName' => 'DESC'));
                break;
            case "img_d":
                $books = $em->getRepository('LibrCRUDBundle:Books')->findBy(array(), array('bookImg' => 'DESC'));
                break;
            case "rel_date_d":
                $books = $em->getRepository('LibrCRUDBundle:Books')->findBy(array(), array('bookPubDate' => 'DESC'));
                break;
            case "description_d":
                $books = $em->getRepository('LibrCRUDBundle:Books')->findBy(array(), array('bookDesc' => 'DESC'));
                break;
            default:
                break;


        }

        $authors = $em->getRepository('LibrCRUDBundle:Authors')->findAll();
        return $this->render('books/index.html.twig', array(
            'books' => $books,
            'authors' => $authors
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
        return $this->redirectToRoute('books_index', array() ,302);

    }

    /**
     * @Route("/form", name="form_process")
     * @Method("POST")
     */
    public function formProcess(){
        $book = new Books();
        $book->setBookDesc($_POST['book_desc']);
        $book->setBookImg('/uploaded_imgs/'.md5(time()).basename($_FILES['book_img']['name']));
        $book->setBookName($_POST['book_title']);

        $book->setBookPubDate(new \DateTime($_POST['book_date']));
        move_uploaded_file($_FILES['book_img']['tmp_name'], '/home/jackob/Desktop/MaProj/Test-task-for-Lab-Fakt/CRUD_for_lib/web'.$book->getBookImg());
        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();

        return $this->redirectToRoute('books_index', array() ,302);
    }

    /**
     * @Route("/attachExistingAuthor/{authorId}/{bookId}", name="attach_author")
     * @Method("GET")
     */
    public function attachExistingAuthor($authorId, $bookId){
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($bookId);
        $author = $em->getRepository('LibrCRUDBundle:Authors')->find($authorId);
        //if(in_array($author, (array) $book->getAuthor()))
        foreach ($book->getAuthor() as $_author)
            if ($author == $_author)
                return $this->redirectToRoute('books_index');
        $book->addAuthor($author);
        $em->flush();
        return $this->redirectToRoute('books_index', array() ,302);
    }

    /**
     * @Route("/unfastenExistingAuthor/{authorId}/{bookId}", name="unfasten_author")
     * @Method("GET")
     */
    public function unfastenExistingAuthor($authorId, $bookId){
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($bookId);
        $author = $em->getRepository('LibrCRUDBundle:Authors')->find($authorId);
        $author_exists = false;
        foreach ($book->getAuthor() as $_author)
            if ($author == $_author)
                $author_exists = true;
        if($author_exists)
           $book->removeAuthor($author);
        $em->flush();
        return $this->redirectToRoute('books_index', array() ,302);
    }

    /**
     * @Route("/editBookTitle/{bookId}/{newBookTitle}", name="edit_book_title")
     */
    public function editBookTitle($bookId, $newBookTitle){
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($bookId);
        $book->setBookName($newBookTitle);
        $em->flush();
        return $this->redirectToRoute('books_index', array() ,302);
    }

    /**
     * @Route("/editBookDesc/{bookId}", name="edit_book_desc")
     * @Method("POST")
     */
    public function editBookDesc($bookId){
        $request = Request::createFromGlobals();
        $text = $request->request->get('desc');
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($bookId);
        $book->setBookDesc($text);
        $em->flush();
        return $this->redirectToRoute('books_index', array() ,302);
    }

    /**
     * @Route("/setNewImg/{bookId}", name="set_new_img")
     */
    public function setNewImg($bookId){
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($bookId);
        $book->setBookImg('/uploaded_imgs/'.md5(time()).basename($_FILES['new_book_img']['name']));
        move_uploaded_file($_FILES['new_book_img']['tmp_name'], '/home/jackob/Desktop/MaProj/Test-task-for-Lab-Fakt/CRUD_for_lib/web'.$book->getBookImg());
        $em->flush();
        return $this->redirectToRoute('books_index', array() ,302);
    }

    /**
     * @Route("/changePubDate/{bookId}", name="change_book_pub_date")
     * @Method("POST")
     */
    public function changeBookPubDate($bookId){
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('LibrCRUDBundle:Books')->find($bookId);
        $book->setBookPubDate(new \DateTime($_POST['new_book_date']));
        $em->flush();
        /*return $this->render('books/index.html.twig', array(
            'books' => $em->getRepository('LibrCRUDBundle:Books')->findAll(),
            'authors' => $em->getRepository('LibrCRUDBundle:Authors')->findAll()
        ));*/
        return $this->redirectToRoute('books_index', array() ,302);
    }

}

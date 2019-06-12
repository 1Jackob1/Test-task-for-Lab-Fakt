<?php


namespace Libr\CRUDBundle\Controller;
require_once '/home/jackob/Desktop/MaProj/Test-task-for-Lab-Fakt/CRUD_for_lib/vendor/fzaninotto/faker/src/autoload.php';

use Fakerino\Fakerino;
use Libr\CRUDBundle\Entity\Authors;
use Libr\CRUDBundle\Entity\Books;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('LibrCRUDBundle:Default:index.html.twig');
    }

    /**
     * @Route("/generateFakeData")
     * @Method("GET")
     */
    public function generateFakeDataAction()
    {
        $faker = Factory::create();
        $count_of_records = 0;
        $em = $this->getDoctrine()->getManager();
        $img_path = '/home/jackob/Desktop/MaProj/Test-task-for-Lab-Fakt/CRUD_for_lib/web/uploaded_imgs';
        for ($i = 0; $i < $count_of_records; $i++) {
            /*
             * Generate Books
             * */
            $book = new Books();
            $book->setBookPubDate($faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now'));
            $book->setBookDesc($faker->text());
            $book->setBookName($faker->name());
            $splited_path = preg_split("(/)", $faker->image($img_path, 640, 480));
            $book->setBookImg('/uploaded_imgs/' . $splited_path[sizeof($splited_path) - 1]);
            /*******************************************/

            /*
             * Generate Authors
             * */
            $author = new Authors();
            $author->setAuthorName($faker->firstName);
            $author->setAuthorSecondName($faker->lastName);

            $em->persist($book);
            $em->persist($author);

        }
        $em->flush();
        $books = $em->getRepository('LibrCRUDBundle:Books')->findAll();
        $authors = $em->getRepository('LibrCRUDBundle:Authors')->findAll();
        $authors_size = sizeof($authors);
        foreach ($books as &$book) {
            if(sizeof($book->getAuthor()) > 0)
                continue;
            $book->addAuthor($authors[mt_rand(0, (int)($authors_size / 2))]);
            $book->addAuthor($authors[mt_rand(((int)($authors_size / 2))+1, $authors_size-1)]);

        }
        $em->flush();
        return new Response('Success');
    }
}

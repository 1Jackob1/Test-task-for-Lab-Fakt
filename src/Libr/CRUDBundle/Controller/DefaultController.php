<?php


namespace Libr\CRUDBundle\Controller;
require __DIR__.'/../../../../vendor/fzaninotto/faker/src/autoload.php';

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
     * @Route("/generateFakeData/{recordCount}")
     * @Method("GET")
     */
    public function generateFakeDataAction($recordCount)
    {
        if($recordCount == 0){
            return new Response('Nothing to create');
        }
        $faker = Factory::create();
        $em = $this->getDoctrine()->getManager();
        $img_path = $this->get('kernel')->getRootDir().'/../web/uploaded_imgs';
        for ($i = 0; $i < $recordCount; $i++) {
            /*
             * Generate Books
             * */
            $book = new Books();
            $book->setPublicationDate($faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now'));
            $book->setDescription($faker->text());
            $book->setTitle($faker->name());
            $splited_path = preg_split("(/)", $faker->image($img_path, 640, 480));
            $book->setImgPath('/uploaded_imgs/' . $splited_path[sizeof($splited_path) - 1]);
            /*******************************************/

            /*
             * Generate Authors
             * */
            $author = new Authors();
            $author->setFirstName($faker->firstName);
            $author->setSecondName($faker->lastName);

            $em->persist($book);
            $em->persist($author);

        }
        $em->flush();
        $books = $em->getRepository('LibrCRUDBundle:Books')->findAll();
        $authors = $em->getRepository('LibrCRUDBundle:Authors')->findAll();
        $authors_size = sizeof($authors);
        foreach ($books as &$book) {
            if (sizeof($book->getAuthor()) > 0) {
                continue;
            }
            $book->addAuthor($authors[mt_rand(0, (int)($authors_size / 2))]);
            $book->addAuthor($authors[mt_rand(((int)($authors_size / 2)) + 1, $authors_size - 1)]);

        }
        $em->flush();
        return new Response('Success');
    }
}

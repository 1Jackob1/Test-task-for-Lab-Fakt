<?php

namespace Libr\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;

/**
 * Booksauthors
 *
 * @ORM\Table(name="BooksAuthors", indexes={@ORM\Index(name="IDX_647997BA16A2B381", columns={"book_id"}), @ORM\Index(name="IDX_647997BAF675F31B", columns={"author_id"})})
 * @ORM\Entity
 */
class Booksauthors
{
    /**
     * @var \Authors
     * @Id
     * @ORM\ManyToOne(targetEntity="Authors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="author_id", referencedColumnName="author_id")
     * })
     */
    private $author;

    /**
     * @var \Books
     * @Id
     * @ORM\ManyToOne(targetEntity="Books")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="book_id", referencedColumnName="book_id")
     * })
     */
    private $book;




    /**
     * Set author
     *
     * @param \Libr\CRUDBundle\Entity\Authors $author
     *
     * @return Booksauthors
     */
    public function setAuthor(\Libr\CRUDBundle\Entity\Authors $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \Libr\CRUDBundle\Entity\Authors
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set book
     *
     * @param \Libr\CRUDBundle\Entity\Books $book
     *
     * @return Booksauthors
     */
    public function setBook(\Libr\CRUDBundle\Entity\Books $book = null)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return \Libr\CRUDBundle\Entity\Books
     */
    public function getBook()
    {
        return $this->book;
    }
}

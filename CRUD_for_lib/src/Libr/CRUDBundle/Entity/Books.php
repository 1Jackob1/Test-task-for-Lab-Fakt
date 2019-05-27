<?php

namespace Libr\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Books
 *
 * @ORM\Table(name="Books")
 * @ORM\Entity
 */
class Books
{
    /**
     * @var integer
     *
     * @ORM\Column(name="book_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $bookId;

    /**
     * @var string
     *
     * @ORM\Column(name="book_name", type="string", length=45, nullable=false)
     */
    private $bookName;

    /**
     * @var integer
     *
     * @ORM\Column(name="book_author", type="integer", nullable=false)
     */
    private $bookAuthor;

    /**
     * @var string
     *
     * @ORM\Column(name="book_desc", type="text", length=16777215, nullable=false)
     */
    private $bookDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="book_img", type="blob", length=65535, nullable=false)
     */
    private $bookImg;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="book_pub_date", type="datetime", nullable=false)
     */
    private $bookPubDate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Authors", inversedBy="book")
     * @ORM\JoinTable(name="booksauthors",
     *   joinColumns={
     *     @ORM\JoinColumn(name="book_id", referencedColumnName="book_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="author_id", referencedColumnName="author_id")
     *   }
     * )
     */
    private $author;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->author = new \Doctrine\Common\Collections\ArrayCollection();
    }

}


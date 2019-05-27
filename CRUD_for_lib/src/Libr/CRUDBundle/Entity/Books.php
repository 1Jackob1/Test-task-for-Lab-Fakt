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


    /**
     * Get bookId
     *
     * @return integer
     */
    public function getBookId()
    {
        return $this->bookId;
    }

    /**
     * Set bookName
     *
     * @param string $bookName
     *
     * @return Books
     */
    public function setBookName($bookName)
    {
        $this->bookName = $bookName;

        return $this;
    }

    /**
     * Get bookName
     *
     * @return string
     */
    public function getBookName()
    {
        return $this->bookName;
    }

    /**
     * Set bookAuthor
     *
     * @param integer $bookAuthor
     *
     * @return Books
     */
    public function setBookAuthor($bookAuthor)
    {
        $this->bookAuthor = $bookAuthor;

        return $this;
    }

    /**
     * Get bookAuthor
     *
     * @return integer
     */
    public function getBookAuthor()
    {
        return $this->bookAuthor;
    }

    /**
     * Set bookDesc
     *
     * @param string $bookDesc
     *
     * @return Books
     */
    public function setBookDesc($bookDesc)
    {
        $this->bookDesc = $bookDesc;

        return $this;
    }

    /**
     * Get bookDesc
     *
     * @return string
     */
    public function getBookDesc()
    {
        return $this->bookDesc;
    }

    /**
     * Set bookImg
     *
     * @param string $bookImg
     *
     * @return Books
     */
    public function setBookImg($bookImg)
    {
        $this->bookImg = $bookImg;

        return $this;
    }

    /**
     * Get bookImg
     *
     * @return string
     */
    public function getBookImg()
    {
        return $this->bookImg;
    }

    /**
     * Set bookPubDate
     *
     * @param \DateTime $bookPubDate
     *
     * @return Books
     */
    public function setBookPubDate($bookPubDate)
    {
        $this->bookPubDate = $bookPubDate;

        return $this;
    }

    /**
     * Get bookPubDate
     *
     * @return \DateTime
     */
    public function getBookPubDate()
    {
        return $this->bookPubDate;
    }

    /**
     * Add author
     *
     * @param \Libr\CRUDBundle\Entity\Authors $author
     *
     * @return Books
     */
    public function addAuthor(\Libr\CRUDBundle\Entity\Authors $author)
    {
        $this->author[] = $author;

        return $this;
    }

    /**
     * Remove author
     *
     * @param \Libr\CRUDBundle\Entity\Authors $author
     */
    public function removeAuthor(\Libr\CRUDBundle\Entity\Authors $author)
    {
        $this->author->removeElement($author);
    }

    /**
     * Get author
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthor()
    {
        return $this->author;
    }
}

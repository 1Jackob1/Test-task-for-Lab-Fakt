<?php

namespace Libr\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Authors
 *
 * @ORM\Table(name="Authors")
 * @ORM\Entity
 */
class Authors
{
    /**
     * @var integer
     *
     * @ORM\Column(name="author_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $authorId;

    /**
     * @var string
     *
     * @ORM\Column(name="author_name", type="string", length=45, nullable=false)
     */
    private $authorName;

    /**
     * @var string
     *
     * @ORM\Column(name="author_second_name", type="string", length=45, nullable=false)
     */
    private $authorSecondName;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Books", mappedBy="author")
     */
    private $book;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->book = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get authorId
     *
     * @return integer
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * Set authorName
     *
     * @param string $authorName
     *
     * @return Authors
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;

        return $this;
    }

    /**
     * Get authorName
     *
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * Set authorSecondName
     *
     * @param string $authorSecondName
     *
     * @return Authors
     */
    public function setAuthorSecondName($authorSecondName)
    {
        $this->authorSecondName = $authorSecondName;

        return $this;
    }

    /**
     * Get authorSecondName
     *
     * @return string
     */
    public function getAuthorSecondName()
    {
        return $this->authorSecondName;
    }

    /**
     * Add book
     *
     * @param \Libr\CRUDBundle\Entity\Books $book
     *
     * @return Authors
     */
    public function addBook(\Libr\CRUDBundle\Entity\Books $book)
    {
        $this->book[] = $book;

        return $this;
    }

    /**
     * Remove book
     *
     * @param \Libr\CRUDBundle\Entity\Books $book
     */
    public function removeBook(\Libr\CRUDBundle\Entity\Books $book)
    {
        $this->book->removeElement($book);
    }

    /**
     * Get book
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBook()
    {
        return $this->book;
    }
}

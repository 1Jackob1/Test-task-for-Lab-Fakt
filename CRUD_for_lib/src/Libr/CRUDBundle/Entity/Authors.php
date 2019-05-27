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

}


<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="restaurant")
 * @ORM\Entity(repositoryClass="App\Repository\RestaurantRepository")
 */
class Restaurant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value = 0
     * )
     */
    private $max_active_tables;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;
    
     /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="restaurants")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;
    
    /**
     * @var ArrayCollection $tables
     *
     * @ORM\OneToMany(targetEntity="RestaurantTable", mappedBy="restaurantId", cascade={"remove"})
     */
    private $tables;
    
    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->tables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getMaxActiveTables(): ?int
    {
        return $this->max_active_tables;
    }

    public function setMaxActiveTables(int $max_active_tables): self
    {
        $this->max_active_tables = $max_active_tables;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
    
    /**
     * @return Collection|RestaurantTable[]
     */
    public function getTables()
    {
        return $this->tables;
    }
}

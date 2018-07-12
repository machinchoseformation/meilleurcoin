<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity("email", message="Cet email est déjà inscrit ici !")
 * @UniqueEntity("username", message="Ce pseudo est déjà utilisé !")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    //Symfony nous force à implémenter ces méthodes inutiles pour nous
    public function getSalt(){return null;}
    public function eraseCredentials(){}

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Veuillez choisir un pseudo!")
     * @Assert\Length(max="50", maxMessage="Pseudo trop long! 50 caractères max!")
     *
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @Assert\Email(message="Votre email n'est pas valide!")
     * @Assert\NotBlank(message="Veuillez renseigner votre email!")
     * @Assert\Length(max="255", maxMessage="Email trop long! 255 caractères max!")
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank(message="Veuillez renseigner votre mot de passe!")
     * @Assert\Length(min="6", minMessage="Votre mot de passe doit comporter au moins 6 caractères!")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateRegistered;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ad",
     *     mappedBy="creator", cascade={"remove"})
     * @ORM\OrderBy({"dateCreated" = "DESC"})
     */
    private $publications;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBanned;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Ad",
     *     inversedBy="users", cascade={"remove"})
     */
    private $favorites;

    public function __construct()
    {
        $this->publications = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getDateRegistered(): ?\DateTimeInterface
    {
        return $this->dateRegistered;
    }

    public function setDateRegistered(\DateTimeInterface $dateRegistered): self
    {
        $this->dateRegistered = $dateRegistered;

        return $this;
    }

    /**
     * @return Collection|Ad[]
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Ad $ad): self
    {
        if (!$this->publications->contains($ad)) {
            $this->publications[] = $ad;
            $ad->setCreator($this);
        }

        return $this;
    }

    public function removePublication(Ad $ad): self
    {
        if ($this->publications->contains($ad)) {
            $this->publications->removeElement($ad);
            // set the owning side to null (unless already changed)
            if ($ad->getCreator() === $this) {
                $ad->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ad[]
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Ad $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
        }

        return $this;
    }

    public function removeFavorite(Ad $favorite): self
    {
        if ($this->favorites->contains($favorite)) {
            $this->favorites->removeElement($favorite);
        }

        return $this;
    }

    public function getIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }
}

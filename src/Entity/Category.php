<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 *
 * Validation:
 * @UniqueEntity(fields={"name"}, message="Cette catégorie existe déjà")
 */
class Category
{
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     *
     * Validation : La valeur ne doit pas ëtre vide
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(max="20", min="2",
     *     maxMessage="Le nom ne doit pas faire plus de {{ limit }} caractères",
     *     minMessage="Le nom ne doit pas faire moins de {{ limit }} caractères")
     */
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}

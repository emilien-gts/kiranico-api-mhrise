<?php

namespace App\Entity\Skill;

use App\Entity\Weapon\Weapon;
use App\Repository\Skill\SkillLevelRepository;
use App\Trait\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkillLevelRepository::class)]
class SkillLevel
{
    use IdTrait;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $level = null;

    #[ORM\Column(length: 255)]
    private ?string $effect = null;

    #[ORM\ManyToOne(inversedBy: 'levels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Skill $skill = null;

    /**
     * @var Collection<int, Weapon>
     */
    #[ORM\ManyToMany(targetEntity: Weapon::class, mappedBy: 'skills')]
    private Collection $weapons;

    public function __construct()
    {
        $this->weapons = new ArrayCollection();
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getEffect(): ?string
    {
        return $this->effect;
    }

    public function setEffect(string $effect): static
    {
        $this->effect = $effect;

        return $this;
    }

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): static
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * @return Collection<int, Weapon>
     */
    public function getWeapons(): Collection
    {
        return $this->weapons;
    }

    public function addWeapon(Weapon $weapon): static
    {
        if (!$this->weapons->contains($weapon)) {
            $this->weapons->add($weapon);
            $weapon->addSkill($this);
        }

        return $this;
    }

    public function removeWeapon(Weapon $weapon): static
    {
        if ($this->weapons->removeElement($weapon)) {
            $weapon->removeSkill($this);
        }

        return $this;
    }
}

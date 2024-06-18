<?php

namespace App\Entity\Weapon;

use App\Enum\StatusEffect;
use App\Repository\Weapon\WeaponStatusRepository;
use App\Trait\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeaponStatusRepository::class)]
class WeaponStatus
{
    use IdTrait;

    #[ORM\Column(length: 255, enumType: StatusEffect::class)]
    private ?StatusEffect $status = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $value = null;

    #[ORM\ManyToOne(inversedBy: 'statuses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Weapon $weapon = null;

    public function getStatus(): ?StatusEffect
    {
        return $this->status;
    }

    public function setStatus(?StatusEffect $status): void
    {
        $this->status = $status;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getWeapon(): ?Weapon
    {
        return $this->weapon;
    }

    public function setWeapon(?Weapon $weapon): static
    {
        $this->weapon = $weapon;

        return $this;
    }
}
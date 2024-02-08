<?php

namespace App\Entity;

use App\Repository\FamilyStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FamilyStatusRepository::class)]
class FamilyStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $familyStatusName = null;

    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'familyStatus')]
    private Collection $clients;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFamilyStatusName(): ?string
    {
        return $this->familyStatusName;
    }

    public function setFamilyStatusName(string $familyStatusName): static
    {
        $this->familyStatusName = $familyStatusName;

        return $this;
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): static
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setFamilyStatus($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getFamilyStatus() === $this) {
                $client->setFamilyStatus(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\CitizenshipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CitizenshipRepository::class)]
class Citizenship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $citizenshipName = null;

    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'citizenship')]
    private Collection $clients;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCitizenshipName(): ?string
    {
        return $this->citizenshipName;
    }

    public function setCitizenshipName(string $citizenshipName): static
    {
        $this->citizenshipName = $citizenshipName;

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
            $client->setCitizenship($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getCitizenship() === $this) {
                $client->setCitizenship(null);
            }
        }

        return $this;
    }
}

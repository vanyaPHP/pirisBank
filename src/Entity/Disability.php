<?php

namespace App\Entity;

use App\Repository\DisabilityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisabilityRepository::class)]
class Disability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $disabilityName = null;

    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'disability')]
    private Collection $clients;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisabilityName(): ?string
    {
        return $this->disabilityName;
    }

    public function setDisabilityName(string $disabilityName): static
    {
        $this->disabilityName = $disabilityName;

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
            $client->setDisability($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getDisability() === $this) {
                $client->setDisability(null);
            }
        }

        return $this;
    }
}

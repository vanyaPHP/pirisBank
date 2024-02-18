<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $firstName = null;

    #[ORM\Column(length: 20)]
    private ?string $middleName = null;

    #[ORM\Column(length: 20)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column]
    private ?bool $sex = null;

    #[ORM\Column(length: 2)]
    private ?string $passportSeries = null;

    #[ORM\Column(length: 7)]
    private ?string $passportNum = null;

    #[ORM\Column(length: 30)]
    private ?string $passportProvider = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $passportReleaseDate = null;

    #[ORM\Column(length: 14)]
    private ?string $passportId = null;

    #[ORM\Column(length: 50)]
    private ?string $birthPlace = null;

    #[ORM\Column(length: 50)]
    private ?string $currentLiveAddress = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $homePhone = null;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $mobilePhone = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    private ?string $registrationAddress = null;

    #[ORM\Column]
    private ?bool $isPensioner = null;

    #[ORM\Column(nullable: true)]
    private ?int $monthSalary = null;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $liveCity = null;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FamilyStatus $familyStatus = null;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Citizenship $citizenship = null;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Disability $disability = null;

    #[ORM\OneToMany(targetEntity: Deposit::class, mappedBy: 'client')]
    private Collection $deposits;

    #[ORM\OneToMany(targetEntity: Credit::class, mappedBy: 'client')]
    private Collection $credits;

    public function __construct()
    {
        $this->deposits = new ArrayCollection();
        $this->credits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(string $middleName): static
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function isSex(): ?bool
    {
        return $this->sex;
    }

    public function setSex(bool $sex): static
    {
        $this->sex = $sex;

        return $this;
    }

    public function getPassportSeries(): ?string
    {
        return $this->passportSeries;
    }

    public function setPassportSeries(string $passportSeries): static
    {
        $this->passportSeries = $passportSeries;

        return $this;
    }

    public function getPassportNum(): ?string
    {
        return $this->passportNum;
    }

    public function setPassportNum(string $passportNum): static
    {
        $this->passportNum = $passportNum;

        return $this;
    }

    public function getPassportProvider(): ?string
    {
        return $this->passportProvider;
    }

    public function setPassportProvider(string $passportProvider): static
    {
        $this->passportProvider = $passportProvider;

        return $this;
    }

    public function getPassportReleaseDate(): ?\DateTimeInterface
    {
        return $this->passportReleaseDate;
    }

    public function setPassportReleaseDate(\DateTimeInterface $passportReleaseDate): static
    {
        $this->passportReleaseDate = $passportReleaseDate;

        return $this;
    }

    public function getPassportId(): ?string
    {
        return $this->passportId;
    }

    public function setPassportId(string $passportId): static
    {
        $this->passportId = $passportId;

        return $this;
    }

    public function getBirthPlace(): ?string
    {
        return $this->birthPlace;
    }

    public function setBirthPlace(string $birthPlace): static
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    public function getCurrentLiveAddress(): ?string
    {
        return $this->currentLiveAddress;
    }

    public function setCurrentLiveAddress(string $currentLiveAddress): static
    {
        $this->currentLiveAddress = $currentLiveAddress;

        return $this;
    }

    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    public function setHomePhone(?string $homePhone): static
    {
        $this->homePhone = $homePhone;

        return $this;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone(?string $mobilePhone): static
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getRegistrationAddress(): ?string
    {
        return $this->registrationAddress;
    }

    public function setRegistrationAddress(string $registrationAddress): static
    {
        $this->registrationAddress = $registrationAddress;

        return $this;
    }

    public function isIsPensioner(): ?bool
    {
        return $this->isPensioner;
    }

    public function setIsPensioner(bool $isPensioner): static
    {
        $this->isPensioner = $isPensioner;

        return $this;
    }

    public function getMonthSalary(): ?int
    {
        return $this->monthSalary;
    }

    public function setMonthSalary(?int $monthSalary): static
    {
        $this->monthSalary = $monthSalary;

        return $this;
    }

    public function getLiveCity(): ?City
    {
        return $this->liveCity;
    }

    public function setLiveCity(?City $liveCity): static
    {
        $this->liveCity = $liveCity;

        return $this;
    }

    public function getFamilyStatus(): ?FamilyStatus
    {
        return $this->familyStatus;
    }

    public function setFamilyStatus(?FamilyStatus $familyStatus): static
    {
        $this->familyStatus = $familyStatus;

        return $this;
    }

    public function getCitizenship(): ?Citizenship
    {
        return $this->citizenship;
    }

    public function setCitizenship(?Citizenship $citizenship): static
    {
        $this->citizenship = $citizenship;

        return $this;
    }

    public function getDisability(): ?Disability
    {
        return $this->disability;
    }

    public function setDisability(?Disability $disability): static
    {
        $this->disability = $disability;

        return $this;
    }

    /**
     * @return Collection<int, Deposit>
     */
    public function getDeposits(): Collection
    {
        return $this->deposits;
    }

    public function addDeposit(Deposit $deposit): static
    {
        if (!$this->deposits->contains($deposit)) {
            $this->deposits->add($deposit);
            $deposit->setClient($this);
        }

        return $this;
    }

    public function removeDeposit(Deposit $deposit): static
    {
        if ($this->deposits->removeElement($deposit)) {
            // set the owning side to null (unless already changed)
            if ($deposit->getClient() === $this) {
                $deposit->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Credit>
     */
    public function getCredits(): Collection
    {
        return $this->credits;
    }

    public function addCredit(Credit $credit): static
    {
        if (!$this->credits->contains($credit)) {
            $this->credits->add($credit);
            $credit->setClient($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): static
    {
        if ($this->credits->removeElement($credit)) {
            // set the owning side to null (unless already changed)
            if ($credit->getClient() === $this) {
                $credit->setClient(null);
            }
        }

        return $this;
    }
}

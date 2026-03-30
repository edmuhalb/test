<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\MedTeam\MedTeam;
use App\Repository\AdministratorReportRepository;
use App\State\AdministratorReport\PostProcessor;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: AdministratorReportRepository::class)]
#[ORM\Table(name: 'administrator_reports')]
#[ApiResource(
    operations: [
        new GetCollection(
            routePrefix: '/api/v1',
            shortName: 'TransportReport'
        ),
        new Post(
            routePrefix: '/api/v1',
            shortName: 'TransportReport'
        ),
        new Get(
            routePrefix: '/api/v1',
            shortName: 'TransportReport'
        ),
        new Patch(
            routePrefix: '/api/v1',
            shortName: 'TransportReport'
        ),
        new Delete(
            routePrefix: '/api/v1',
            shortName: 'TransportReport'
        ),

        new GetCollection(
            routePrefix: '/api',
            openapi: false,
        ),
        new Post(
            routePrefix: '/api',
            openapi: false,
            processor: PostProcessor::class
        ),
        new Get(
            routePrefix: '/api',
            openapi: false,
            normalizationContext: ['groups' => [
                'administrator_report:read',
                'administrator_report:detail:read',
                'media_object:read',
                'user:item:read',
                'phone:read',
                'car:read',
                'base:read',
            ]],
        ),
        new Put(
            routePrefix: '/api',
            openapi: false,
            processor: PostProcessor::class,
        ),
        new Delete(
            routePrefix: '/api',
            openapi: false,
        ),
    ],
    normalizationContext: ['groups' => ['administrator_report:read', 'media_object:read', 'user:item:read']],
    denormalizationContext: ['groups' => ['administrator_report:write', 'media_object:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true
)]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt'], arguments: ['orderParameterName' => 'order'])]
#[ApiFilter(SearchFilter::class, properties: ['shift.admin.id' => 'exact'])]
#[ApiFilter(
    DateFilter::class,
    properties: [
        'createdAt' => DateFilterInterface::EXCLUDE_NULL,
        'updatedAt' => DateFilterInterface::EXCLUDE_NULL,
    ]
)]
class AdministratorReport
{
    use BlameableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['administrator_report:read', 'v1:shift:item:read',])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Groups([
        'administrator_report:read',
        'administrator_report:write',
    ])]
    // #[Assert\NotBlank]
    private ?MedTeam $team = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'administrator_report:read',
        'administrator_report:write',
        'v1:shift:item:read',
        'v1:shift:read',
        'v1:shift:write',
    ])]
    private ?int $mileage = null;

    #[ORM\ManyToMany(targetEntity: MediaObject::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'administrator_reports_mileage_receipts')]
    #[ORM\JoinColumn(name: 'administrator_report_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'media_object_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups(['administrator_report:detail:read', 'administrator_report:write'])]
    private Collection $mileageReceipts;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'administrator_report:read',
        'administrator_report:write',
        'v1:shift:item:read',
        'v1:shift:read',
        'v1:shift:write',
    ])]
    private ?int $toolRoad = null;

    #[ORM\ManyToMany(targetEntity: MediaObject::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'administrator_reports_toll_road_receipts')]
    #[ORM\JoinColumn(name: 'administrator_report_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'media_object_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups(['administrator_report:detail:read', 'administrator_report:write'])]
    private Collection $tollRoadReceipts;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'administrator_report:read',
        'administrator_report:write',
        'v1:shift:read',
        'v1:shift:write',
        'v1:shift:item:read',
    ])]
    private ?int $parkingFees = null;

    #[ORM\ManyToMany(targetEntity: MediaObject::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'administrator_reports_parking_fees_receipts')]
    #[ORM\JoinColumn(name: 'administrator_report_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'media_object_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups(['administrator_report:detail:read', 'administrator_report:write'])]
    private Collection $parkingFeesReceipts;

    /**
     * @var Collection<int, FileObject>
     */
    #[ORM\ManyToMany(targetEntity: FileObject::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'administrator_reports_files')]
    #[ORM\JoinColumn(name: 'administrator_report_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'file_object_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups([
        'v1:shift:read',
        'v1:shift:write',
    ])]
    private Collection $files;

    #[Groups(['administrator_report:read'])]
    #[ORM\OneToOne(mappedBy: 'transportReport', cascade: ['persist', 'remove'])]
    private ?MedTeam $shift = null;

    public function __construct()
    {
        $this->mileageReceipts = new ArrayCollection();
        $this->parkingFeesReceipts = new ArrayCollection();
        $this->tollRoadReceipts = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): ?MedTeam
    {
        return $this->team;
    }

    public function setTeam(?MedTeam $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(?int $mileage): self
    {
        $this->mileage = $mileage;

        return $this;
    }

    public function getToolRoad(): ?int
    {
        return $this->toolRoad;
    }

    public function setToolRoad(?int $toolRoad): self
    {
        $this->toolRoad = $toolRoad;

        return $this;
    }

    public function getParkingFees(): ?int
    {
        return $this->parkingFees;
    }

    public function setParkingFees(?int $parkingFees): self
    {
        $this->parkingFees = $parkingFees;

        return $this;
    }

    #[Groups(['administrator_report:read'])]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['administrator_report:read'])]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd.m.Y H:m'])]
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, MediaObject>
     */
    public function getMileageReceipts(): Collection
    {
        return $this->mileageReceipts;
    }

    public function addMileageReceipt(MediaObject $mileageReceipt): self
    {
        if (!$this->mileageReceipts->contains($mileageReceipt)) {
            $this->mileageReceipts->add($mileageReceipt);
        }

        return $this;
    }

    public function removeMileageReceipt(MediaObject $mileageReceipt): self
    {
        $this->mileageReceipts->removeElement($mileageReceipt);

        return $this;
    }

    /**
     * @return Collection<int, MediaObject>
     */
    public function getTollRoadReceipts(): Collection
    {
        return $this->tollRoadReceipts;
    }

    public function addTollRoadReceipt(MediaObject $toLlRoadReceipt): self
    {
        if (!$this->tollRoadReceipts->contains($toLlRoadReceipt)) {
            $this->tollRoadReceipts->add($toLlRoadReceipt);
        }

        return $this;
    }

    public function removeTollRoadReceipt(MediaObject $toLlRoadReceipt): self
    {
        $this->tollRoadReceipts->removeElement($toLlRoadReceipt);

        return $this;
    }

    /**
     * @return Collection<int, MediaObject>
     */
    public function getParkingFeesReceipts(): Collection
    {
        return $this->parkingFeesReceipts;
    }

    public function addParkingFeesReceipt(MediaObject $mediaObject): self
    {
        if (!$this->parkingFeesReceipts->contains($mediaObject)) {
            $this->parkingFeesReceipts->add($mediaObject);
        }

        return $this;
    }

    public function removeParkingFeesReceipt(MediaObject $mediaObject): self
    {
        $this->parkingFeesReceipts->removeElement($mediaObject);

        return $this;
    }

    /**
     * @return Collection<int, FileObject>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(FileObject $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
        }

        return $this;
    }

    public function removeFile(FileObject $file): static
    {
        $this->files->removeElement($file);

        return $this;
    }

    public function getShift(): ?MedTeam
    {
        return $this->shift;
    }

    public function setShift(?MedTeam $shift): static
    {
        if ($shift === null && $this->shift !== null) {
            $this->shift->setTransportReport(null);
        }
        if ($shift !== null && $shift->getTransportReport() !== $this) {
            $shift->setTransportReport($this);
        }
        $this->shift = $shift;

        return $this;
    }
}

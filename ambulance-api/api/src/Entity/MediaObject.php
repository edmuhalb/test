<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity]
#[ApiResource(
    types: ['https://schema.org/MediaObject'],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            inputFormats: ['multipart' => ['multipart/form-data']],
        ),
    ],
    outputFormats: ['jsonld' => ['application/ld+json']],
    routePrefix: '/api',
    normalizationContext: ['groups' => ['media_object:read']],
    openapi: false,
)]
class MediaObject
{
    #[ApiProperty(writable: false, types: ['https://schema.org/contentUrl'])]
    #[Groups(['media_object:read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: 'media_object', fileNameProperty: 'filePath')]
    #[Assert\NotNull]
    public ?File $file = null;

    #[ApiProperty(writable: false)]
    #[ORM\Column(nullable: true)]
    #[Groups(['media_object:read'])]
    public ?string $filePath = null;

    #[Groups(['media_object:write'])]
    public ?string $base64content = null;
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups(['media_object:read', 'media_object:write'])]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\FileObject\GetAction;
use ArrayObject;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity]
#[ApiResource(
    shortName: 'File',
    types: ['https://schema.org/MediaObject'],
    operations: [
        new Get(
            routePrefix: '/api/v1',
            controller: GetAction::class,
        ),
        new Get(
            routePrefix: '/exchange',
            controller: GetAction::class,
        ),
        new GetCollection(
            routePrefix: '/api/v1',
        ),
        new Post(
            inputFormats: ['multipart' => ['multipart/form-data']],
            routePrefix: '/api/v1',
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                            ],
                        ],
                    ])
                )
            )
        ),
        new Delete(
            routePrefix: '/api/v1',
        ),
    ],
    outputFormats: [
        'json' => ['application/json'],
    ],
    normalizationContext: ['groups' => ['media_object:read:image']]
)]
class FileObject
{
    #[ApiProperty(writable: false, types: ['https://schema.org/contentUrl'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: 'media_object', fileNameProperty: 'filePath')]
    #[Assert\NotNull]
    public ?File $file = null;

    #[ApiProperty(writable: false)]
    #[ORM\Column(nullable: true)]
    public ?string $filePath = null;
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups(['media_object:read:image', 'exchange_calling:read'])]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['media_object:read:image', 'exchange_calling:read'])]
    #[SerializedName('url')]
    public function getIri(): string
    {
        return '/api/v1/files/' . $this->id;
    }
}

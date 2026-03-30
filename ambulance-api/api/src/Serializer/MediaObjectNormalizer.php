<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\FileObject;
use ArrayObject;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class MediaObjectNormalizer implements NormalizerInterface
{
    private const string ALREADY_CALLED = 'MEDIA_OBJECT_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        #[Autowire(service: 'api_platform.serializer.normalizer.item')]
        private readonly NormalizerInterface $normalizer,
        private readonly StorageInterface $storage
    ) {}

    public function normalize($object, ?string $format = null, array $context = []): null|array|ArrayObject|bool|float|int|string
    {
        $context[self::ALREADY_CALLED] = true;

        $object->contentUrl = $this->storage->resolveUri($object, 'file');

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof FileObject;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            FileObject::class => true,
        ];
    }
}

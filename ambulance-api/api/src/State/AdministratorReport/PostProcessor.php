<?php

declare(strict_types=1);

namespace App\State\AdministratorReport;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\MediaObject;
use App\Services\File\UploadedBase64File;

readonly class PostProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $images = $data->getTollRoadReceipts();

        /** @var MediaObject $image */
        foreach ($images as $image) {
            if ($image->base64content) {
                $imageFile = new UploadedBase64File($image->base64content, 'toll_road_receipt.png');
                $image->file = $imageFile;
            }
        }

        $images = $data->getParkingFeesReceipts();

        /** @var MediaObject $image */
        foreach ($images as $image) {
            if ($image->base64content) {
                $imageFile = new UploadedBase64File($image->base64content, 'parking_fees_receipt.png');
                $image->file = $imageFile;
            }
        }

        $images = $data->getMileageReceipts();

        /** @var MediaObject $image */
        foreach ($images as $image) {
            if ($image->base64content) {
                $imageFile = new UploadedBase64File($image->base64content, 'mileage_receipt.png');
                $image->file = $imageFile;
            }
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}

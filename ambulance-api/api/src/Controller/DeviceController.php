<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Device;
use App\Flusher;
use App\Repository\DeviceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeviceController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly DeviceRepository $devices,
        private readonly Flusher $flusher
    ) {}

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/api/devices/{id}', name: 'device_create_or_update', methods: ['POST'])]
    public function createOrUpdate(string $id): Response
    {
        $device = $this->devices->find($id);

        $user = $this->users->findOneByPhone($this->getUser()->getUserIdentifier());

        if (!$device) {
            $device = new Device($id, $user);
            $this->devices->save($device);
        } else {
            $device->setUser($user);
        }

        $this->flusher->flush();

        return $this->json([]);
    }

    #[Route('/api/devices/{id}', name: 'device_delete', methods: ['DELETE'])]
    public function delete(string $id): Response
    {
        $device = $this->devices->find($id);
        if (!$device) {
            return $this->json([]);
        }
        $this->devices->remove($device);

        $this->flusher->flush();

        return $this->json([]);
    }
}

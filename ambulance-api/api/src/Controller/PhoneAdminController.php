<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PhoneAdminController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly UserRepository $users,
    ) {}

    #[Route('/api/phone/admins.xml', name: 'phone.admin', methods: ['GET'])]
    public function phoneAdmin(): Response
    {
        $users = $this->users->findAllByPermission('can_be-admin');

        $data = [
            'CiscoIPPhoneDirectory' => [
                'DirectoryEntry' => [
                ],
            ],
        ];
        /** @var User $user */
        foreach ($users as $user) {
            $data['CiscoIPPhoneDirectory']['DirectoryEntry'][] = [
                'Name' => $user->getName(),
                'Telephone' => '+' . $user->getPhone(),
            ];
        }

        $users = $this->users->findAllByPermission('can_be-doctor');

        /** @var User $user */
        foreach ($users as $user) {
            $data['CiscoIPPhoneDirectory']['DirectoryEntry'][] = [
                'Name' => $user->getName(),
                'Telephone' => '+' . $user->getPhone(),
            ];
        }


        $users = $this->users->findAllByPermission('can_be-smp_paramedic');

        /** @var User $user */
        foreach ($users as $user) {
            $data['CiscoIPPhoneDirectory']['DirectoryEntry'][] = [
                'Name' => $user->getName(),
                'Telephone' => '+' . $user->getPhone(),
            ];
        }

        $users = $this->users->findAllByPermission('can_be-smp_doctor');

        /** @var User $user */
        foreach ($users as $user) {
            $data['CiscoIPPhoneDirectory']['DirectoryEntry'][] = [
                'Name' => $user->getName(),
                'Telephone' => '+' . $user->getPhone(),
            ];
        }

        $xml = $this->serializer->serialize($data, 'xml');

        $response = new Response($xml);
        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }
}

<?php

namespace App\Controller;

use App\Attribute\SerializedResponse;
use App\DTO\AccessRoleDTO;
use App\DTO\UserDTO;
use App\DTO\CreatePasswordDTO;
use App\DTO\ResetPasswordDTO;
use App\DTO\Search\UserSearchDTO;
use App\Entity\AccessRole;
use App\Entity\User;
use App\Service\UserService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Annotation\Serialize;

class UserController extends BaseController
{
    #[Route('/api/user', name: 'get_users', methods: ['GET'])]
    public function index(
        UserRepository $userRepository,
        #[MapQueryString] ?UserSearchDTO $userSearchDTO = new UserSearchDTO()
    ): JsonResponse
    {
        $users = $userRepository->search($userSearchDTO);

        return $this->getSerializedResponse($users, "get_user");
    }

    #[Route('/api/user', name: 'user_create', methods: ['POST'])]
    public function create(
        UserDTO $userDTO,
        UserService $userService,
    ): JsonResponse
    {
        $user = $this->createFromDTO(User::class, $userDTO);
        $userService->createPasswordCreationToken($user);

        $this->em->persist($user);
        $this->em->flush();
        
        return $this->getSerializedResponse($user, "get_user");
    }

    #[Route('/api/me', name: 'user_me', methods: ['GET'])]
    public function me(
    ): JsonResponse
    {
        return $this->getSerializedResponse($this->getUser(), "get_user");
    }

    #[Route('/api/user/reset-password', name: 'user_reset_password', methods: ['POST'])]
    public function resetPassword(
        ResetPasswordDTO $resetPasswordDTO,
        UserService $userService,
    ): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->findOneByEmail($resetPasswordDTO->email);

        if (!$user) {
            throw new \Exception("User not found");
        }
        
        $user->setPassword(null);
        $userService->createPasswordCreationToken($user);

        $this->em->persist($user);
        $this->em->flush();
        
        return $this->getSerializedResponse([], "get_user");
    }

    #[Route('/api/user/create-password', name: 'user_create_password', methods: ['POST'])]
    public function createPassword(
        CreatePasswordDTO $createPasswordDTO,
        UserService $userService,
    ): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->findOneByPasswordCreationToken($createPasswordDTO->token);

        if (!$user) {
            throw new \Exception("User not found");
        }

        if (!$user->getPasswordCreationDateExpiration() || $user->getPasswordCreationDateExpiration() < new \DateTime()) {
            throw new \Exception("Token is expired");
        }

        $userService->setPassword($user, $createPasswordDTO->password);

        $this->em->persist($user);
        $this->em->flush();
        
        return $this->getSerializedResponse($user, "get_user");
    }
    
    #[Route('/api/user/{id:user}', name: 'user_update', methods: ['PUT'])]
    public function updateCurrentUser(
        User $user,
        UserDTO $userDTO,
    )
    {
        $this->updateEntityFromDTO($user, $userDTO);

        $this->em->flush();

        return $this->getSerializedResponse($user, "get_user");
    }  

    #[Route('/api/user/{id:user}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();

        return new JsonResponse(['status' => 'User deleted successfully']);
    }
}

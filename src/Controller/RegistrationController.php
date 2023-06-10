<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;


#[Route('/api', name: 'registration_api_')]
class RegistrationController extends AbstractController
{

    /**
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/users', name: 'user_list', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $users = $doctrine->getRepository(User::class)->findAll();
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            ];
        }
        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHashes
     * @return JsonResponse
     * @TODO Need to know URL generator and how to pass email under the queue
     */
    #[Route('/registration', name: 'user_registration', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHashes, MailerInterface $mailer, RouterInterface $router): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $user = new User();
        $password = $passwordHashes->hashPassword($user, $request->request->get('password'));
        $user->setName($request->request->get('name'));
        $user->setEmail($request->request->get('email'));
        $user->setPassword($password);
        $user->setStatus(1);
        $entityManager->persist($user);
        $entityManager->flush();

        /**Send email for verify account**/
//        $email = (new Email())->from('rk@gmail.com')
//            ->to($request->request->get('email'))
//            ->subject('Account Verification')
//            ->text('Please verify your account');

        $email = (new TemplatedEmail())
            ->from('rony@gmail.com')
            ->to($request->request->get('email'))
            ->subject('Account Verification')
            ->htmlTemplate('email/account-verification.html.twig')
            ->context([
                'name' => $request->request->get('name'),
                'active_url' => 'http://127.0.0.1:44593/api/verify/account'
//                'active_url' => $router->generate('account_verify')
            ]);

        $mailer->send($email);

        $data = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword()
        ];

        return $this->json($data, Response::HTTP_CREATED);

    }


    /**
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/users/{id}', name: 'user_details', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json('User not found for id ' . $id, Response::HTTP_NOT_FOUND);
        }
        $data = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ];

        return $this->json($data, Response::HTTP_OK);
    }


    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/users/{id}', name: 'user_update', methods: ['PUT', 'PATCH'])]
    public function update(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json('User not found for id ' . $id, Response::HTTP_NOT_FOUND);
        }
        $user->setName($request->request->get('name'));
        $entityManager->flush();
        $data = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ];

        return $this->json($data, Response::HTTP_OK);



    }

    /**
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/users/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function destroy(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json('User not found for id ' . $id, Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json('Successfully deleted user id for ' . $id, Response::HTTP_OK);
    }

    #[Route('/verify/account', name: 'account_verify', methods: ['GET'])]
    public function verifyAccount(): Response
    {
        return new Response('ok');
    }


}

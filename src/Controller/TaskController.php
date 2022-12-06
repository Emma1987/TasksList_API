<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/task')]
class TaskController extends AbstractController
{
    private TaskRepository $repository;
    private SerializerInterface $serializer;

    public function __construct(TaskRepository $repository, SerializerInterface $serializer)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    #[Route(name: 'task_get', methods: ['GET'])]
    public function getTasks(): JsonResponse
    {
        $tasks = $this->repository->getAllTasks();
        $json = $this->serializer->serialize($tasks, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(name: 'task_post', methods: ['POST'])]
    public function addTask(Request $request): JsonResponse
    {
        $body = $request->toArray();

        if (!array_key_exists('content', $body)) {
            return new JsonResponse('Missing param content', Response::HTTP_BAD_REQUEST, [], true);
        }

        $task = $this->repository->createTask($body);
        $json = $this->serializer->serialize($task, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(name: 'task_put', methods: ['PUT'])]
    public function updateTask(Request $request): JsonResponse
    {
        $body = $request->toArray();

        if (!array_key_exists('id', $body) || !array_key_exists('user', $body)) {
            return new JsonResponse('Missing param id', Response::HTTP_BAD_REQUEST, [], true);
        }

        if (!$task = $this->repository->findOneBy(['uuid' => $body['id']])) {
            return new JsonResponse('Task not found', Response::HTTP_BAD_REQUEST, [], true);
        }

        $task->setUser($body['user']);
        $task = $this->repository->updateTask($task);
        $json = $this->serializer->serialize($task, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(name: 'task_delete', methods: ['DELETE'])]
    public function deleteTask(Request $request): JsonResponse
    {
        if (empty($request->query->get('id'))) {
            return new JsonResponse('Missing param id', Response::HTTP_BAD_REQUEST, [], true);
        }

        if (!$task = $this->repository->findOneBy(['uuid' => $request->query->get('id')])) {
            return new JsonResponse('Task not found', Response::HTTP_BAD_REQUEST, [], true);
        }

        $task = $this->repository->removeTask($task);
        $json = $this->serializer->serialize($task, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}

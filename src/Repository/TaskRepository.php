<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return array Returns an array of Task arrays
     */
    public function getAllTasks():array
    {
        $tasks = $this->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return array_map(function (Task $task) {
            return $this->getTaskAsArray($task);
        }, $tasks);
    }

    /**
     * @return array Returns an array of one Task array
     */
    public function createTask(array $task): array
    {
        $task = (new Task())
            ->setContent($task['content']);

        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();

        return $this->getTaskAsArray($task);
    }

    /**
     * @return array Returns an array of one Task array
     */
    public function updateTask(Task $task): array
    {
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();

        return $this->getTaskAsArray($task);
    }

    /**
     * @return array Returns an array of one Task array
     */
    public function removeTask(Task $task): array
    {
        $this->getEntityManager()->remove($task);
        $this->getEntityManager()->flush();

        return $this->getTaskAsArray($task);
    }

    /**
     *
     * PRIVATE
     *
     */

    private function getTaskAsArray(Task $task): array
    {
        return [
            'id' => $task->getUuid(),
            'content' => $task->getContent(),
            'user' => $task->getUser(),
        ];
    }
}

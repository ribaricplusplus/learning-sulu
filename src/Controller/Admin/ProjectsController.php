<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactoryInterface;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Rest\RestHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @RouteResource("project")
 */
class ProjectsController extends AbstractController implements ClassResourceInterface
{
    public function __construct(
        private ViewHandlerInterface $viewHandler,
        private FieldDescriptorFactoryInterface $fieldDescriptorFactory,
        private DoctrineListBuilderFactoryInterface $doctrineListBuilderFactory,
        private RestHelperInterface $restHelper,
        private EntityManagerInterface $entityManager,
    ) {}

    public function cgetAction(): Response {
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors(Project::RESOURCE_KEY);
        $listBuilder = $this->doctrineListBuilderFactory->create(Project::class);
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);

        $listRepresentation = new PaginatedRepresentation(
            $listBuilder->execute(),
            Project::RESOURCE_KEY,
            $listBuilder->getCurrentPage(),
            $listBuilder->getLimit(),
            $listBuilder->count()
        );

        return $this->viewHandler->handle(View::create($listRepresentation));
    }

    public function getAction(Request $request, int $id): Response {
        $project = $this->entityManager->find(Project::class, $id);

        if (empty($project)) {
            return $this->viewHandler->handle(View::create(null, 404));
        }

        $data = [
            'id' => $project->getId(),
            'title' => $project->getTitle(),
        ];

        return $this->viewHandler->handle(View::create($data));
    }

    public function putAction(Request $request, int $id): Response {
        $project = $this->entityManager->find(Project::class, $id);
        if (empty($project)) {
            throw new NotFoundHttpException();
        }

        $title = $request->get("title");
        $project->setTitle($title);
        $this->entityManager->flush();
        return $this->viewHandler->handle(View::create($this->getEntityData($project)));
    }

    public function postAction(Request $request): Response {
        $project = new Project();
        $project->setTitle($request->get("title"));
        $this->entityManager->persist($project);
        $this->entityManager->flush();
        return $this->viewHandler->handle(View::create($this->getEntityData($project)));
    }

    public function deleteAction(Request $request, int $id): Response {
        $project = $this->entityManager->find(Project::class, $id);

        if (empty($project)) {
            throw new NotFoundHttpException();
        }

        $this->entityManager->remove($project);
        $this->entityManager->flush();
        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }

    protected function getEntityData(Project $project): array {
        return [
            'id' => $project->getId(),
            'title' => $project->getTitle(),
        ];
    }

}
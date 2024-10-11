<?php

namespace App\Admin;

use App\Entity\Project;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;

class ProjectAdmin extends Admin
{

    const PROJECT_LIST_VIEW = 'app.projects_list';
    const PROJECT_FORM_KEY = 'project_details';
    const PROJECT_ADD_FORM_VIEW = 'app.project_add_form';
    const PROJECT_EDIT_FORM_VIEW = 'app.project_edit_form';
    const LIST_KEY = 'projects';


    public function __construct(private ViewBuilderFactoryInterface $viewBuilderFactory) {}

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void {
        $navigationItem = new NavigationItem('app.projects');
        $navigationItem->setView(static::PROJECT_LIST_VIEW);
        $navigationItem->setPosition(30);
        $navigationItemCollection->add($navigationItem);
    }

    public function configureViews(ViewCollection $viewCollection): void {
        $listView = $this->viewBuilderFactory->createListViewBuilder(static::PROJECT_LIST_VIEW, '/projects')
        ->setResourceKey(Project::RESOURCE_KEY)
        ->setListKey(static::LIST_KEY)
        ->addListAdapters(['table'])
        ->setAddView(static::PROJECT_ADD_FORM_VIEW)
        ->setEditView(static::PROJECT_EDIT_FORM_VIEW)
        ->addToolbarActions([new ToolbarAction('sulu_admin.add'), new ToolbarAction('sulu_admin.delete')]);

        $viewCollection->add($listView);

        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::PROJECT_ADD_FORM_VIEW, '/projects/add')
            ->setResourceKey(Project::RESOURCE_KEY)
            ->setBackView(static::PROJECT_LIST_VIEW);

        $viewCollection->add($addFormView);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::PROJECT_ADD_FORM_VIEW . '.details', '/projects/details')
            ->setResourceKey(Project::RESOURCE_KEY)
            ->setFormKey(static::PROJECT_FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->setEditView(static::PROJECT_EDIT_FORM_VIEW)
            ->addToolbarActions([new ToolbarAction('sulu_admin.save'), new ToolbarAction('sulu_admin.delete')])
            ->setParent(static::PROJECT_ADD_FORM_VIEW);

        $viewCollection->add($addDetailsFormView);

        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::PROJECT_EDIT_FORM_VIEW, '/projects/:id')
            ->setResourceKey(Project::RESOURCE_KEY)
            ->setBackView(static::PROJECT_LIST_VIEW);

        $viewCollection->add($editFormView);

        $editDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::PROJECT_EDIT_FORM_VIEW . '.details', '/details')
            ->setResourceKey(Project::RESOURCE_KEY)
            ->setFormKey(static::PROJECT_FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions([new ToolbarAction('sulu_admin.save'), new ToolbarAction('sulu_admin.delete')])
            ->setParent(static::PROJECT_EDIT_FORM_VIEW);

        $viewCollection->add($editDetailsFormView);
    }


}
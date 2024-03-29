<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2012 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
     * details.
     *
     * You should have received a copy of the GNU General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 113 McHenry Road Suite 207,
     * Buffalo Grove, IL 60089, USA. or at email address contact@zurmo.com.
     ********************************************************************************/

    /**
     * Default controller for ByTimeWorkflowInQueue actions
      */
    class WorkflowsDefaultTimeQueueController extends ZurmoBaseController
    {
        const ZERO_MODELS_CHECK_FILTER_PATH = 'application.modules.workflows.controllers.filters.WorkflowZeroModelsCheckControllerFilter';

        public static function getListBreadcrumbLinks()
        {
            $title = Zurmo::t('WorkflowsModule', 'Time Queue');
            return array($title);
        }

        public function filters()
        {
            return array_merge(parent::filters(),
                array(
                    array(
                        static::ZERO_MODELS_CHECK_FILTER_PATH . ' + list, index',
                        'controller' => $this,
                        'activeActionElementType' => 'ByTimeWorkflowInQueuesLink',
                        'breadcrumbLinks'         => static::getListBreadcrumbLinks(),
                    ),
                )
            );
        }

        public function actionIndex()
        {
            $this->actionList();
        }

        public function actionList()
        {
            $pageSize                       = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                                              'listPageSize', get_class($this->getModule()));
            $activeActionElementType        = 'ByTimeWorkflowInQueuesLink';
            $model                          = new ByTimeWorkflowInQueue(false);
            $searchForm                     = new ByTimeWorkflowInQueuesSearchForm($model);
            $dataProvider                   = $this->resolveSearchDataProvider($searchForm, $pageSize, null,
                                              'ByTimeWorkflowInQueuesSearchView');
            $breadcrumbLinks                = static::getListBreadcrumbLinks();
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view')
            {
                $mixedView = $this->makeListView(
                    $searchForm,
                    $dataProvider,
                    'ByTimeWorkflowInQueuesListView'
                );
                $view = new WorkflowsPageView($mixedView);
            }
            else
            {
                $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider,
                             'SecuredActionBarForWorkflowsSearchAndListView', 'ByTimeWorkflowInQueues', $activeActionElementType);
                $view = new WorkflowsPageView(ZurmoDefaultAdminViewUtil::
                                              makeViewWithBreadcrumbsForCurrentUser(
                                              $this, $mixedView, $breadcrumbLinks, 'WorkflowBreadCrumbView'));
            }
            echo $view->render();
        }
    }
?>

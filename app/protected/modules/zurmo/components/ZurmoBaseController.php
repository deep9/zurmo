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

    abstract class ZurmoBaseController extends Controller
    {
        const RIGHTS_FILTER_PATH = 'application.modules.zurmo.controllers.filters.RightsControllerFilter';

        const REQUIRED_ATTRIBUTES_FILTER_PATH = 'application.modules.zurmo.controllers.filters.RequiredAttributesControllerFilter';

        public function filters()
        {
            $moduleClassName = get_class($this->getModule());
            $filters = array();
            if (is_subclass_of($moduleClassName, 'SecurableModule'))
            {
                $filters[] = array(
                        self::getRightsFilterPath(),
                        'moduleClassName' => $moduleClassName,
                        'rightName' => $moduleClassName::getAccessRight(),
                );
                $filters[] = array(
                        self::getRightsFilterPath() . ' + create, createFromRelation, inlineCreateSave',
                        'moduleClassName' => $moduleClassName,
                        'rightName' => $moduleClassName::getCreateRight(),
                );
                $filters[] = array(
                        self::getRightsFilterPath() . ' + delete',
                        'moduleClassName' => $moduleClassName,
                        'rightName' => $moduleClassName::getDeleteRight(),
                );
            }
            $filters[] = array(
                self::getRightsFilterPath() . ' + massEdit, massEditProgressSave',
                'moduleClassName' => 'ZurmoModule',
                'rightName' => ZurmoModule::RIGHT_BULK_WRITE,
            );
            $filters[] = array(
                self::getRightsFilterPath() . ' + massDelete, massDeleteProgress',
                'moduleClassName' => 'ZurmoModule',
                'rightName' => ZurmoModule::RIGHT_BULK_DELETE,
            );
            return $filters;
        }

        public function __construct($id, $module = null)
        {
            parent::__construct($id, $module);
        }

        /**
         * Override if the module is a nested module such as groups or roles.
         */
        public function resolveAndGetModuleId()
        {
            return $this->getModule()->getId();
        }

        public static function getRightsFilterPath()
        {
            return static::RIGHTS_FILTER_PATH;
        }

        protected function makeActionBarSearchAndListView($searchModel, $dataProvider,
                                                          $actionBarViewClassName = 'SecuredActionBarForSearchAndListView',
                                                          $viewPrefixName = null, $activeActionElementType = null)
        {
            assert('is_string($actionBarViewClassName)');
            assert('is_string($viewPrefixName) || $viewPrefixName == null');
            assert('is_string($activeActionElementType) || $activeActionElementType == null');
            if($viewPrefixName == null)
            {
                $viewPrefixName = $this->getModule()->getPluralCamelCasedName();
            }
            $listModel = $searchModel->getModel();
            return new ActionBarSearchAndListView(
                $this->getId(),
                $this->getModule()->getId(),
                $searchModel,
                $listModel,
                $viewPrefixName,
                $dataProvider,
                GetUtil::resolveSelectedIdsFromGet(),
                $actionBarViewClassName,
                $activeActionElementType
            );
        }

        protected function makeListView(SearchForm $searchForm, $dataProvider, $listViewClassName = null)
        {
            assert('is_string($listViewClassName) || $listViewClassName == null');
            $listModel           = $searchForm->getModel();
            if($listViewClassName == null)
            {
                $listViewClassName   = $this->getModule()->getPluralCamelCasedName() . 'ListView';
            }
            $listView            = new $listViewClassName(
                                       $this->getId(),
                                       $this->getModule()->getId(),
                                       get_class($listModel),
                                       $dataProvider,
                                       GetUtil::resolveSelectedIdsFromGet(),
                                       null,
                                       array(),
                                       $searchForm->getListAttributesSelector());
            return $listView;
        }

        protected function resolveSearchDataProvider(
            $searchModel,
            $pageSize,
            $stateMetadataAdapterClassName = null,
            $stickySearchKey = null,
            $setSticky       = true)
        {
            assert('$searchModel instanceof RedBeanModel || $searchModel instanceof ModelForm');
            assert('$stickySearchKey == null || is_string($stickySearchKey)');
            assert('is_bool($setSticky)');
            $listModelClassName = get_class($searchModel->getModel());
            static::resolveToTriggerOnSearchEvents($listModelClassName);
            $dataCollection = $this->makeDataCollectionAndResolveSavedSearch($searchModel, $stickySearchKey, $setSticky);
            $dataProvider   = $this->makeRedBeanDataProviderByDataCollection(
                $searchModel,
                $pageSize,
                $stateMetadataAdapterClassName,
                $dataCollection);
            return $dataProvider;
        }

        private function makeDataCollectionAndResolveSavedSearch($searchModel, $stickySearchKey = null, $setSticky = true)
        {
            $dataCollection = new SearchAttributesDataCollection($searchModel);
            if ($searchModel instanceof SavedDynamicSearchForm)
            {
                $getData = GetUtil::getData();
                if ($stickySearchKey != null && isset($getData['clearingSearch']) && $getData['clearingSearch'])
                {
                    StickySearchUtil::clearDataByKey($stickySearchKey);
                }
                if ($stickySearchKey != null && null != $stickySearchData = StickySearchUtil::getDataByKey($stickySearchKey))
                {
                    SavedSearchUtil::resolveSearchFormByStickyDataAndModel($stickySearchData, $searchModel);
                    $dataCollection = new SavedSearchAttributesDataCollection($searchModel);
                }
                else
                {
                    SavedSearchUtil::resolveSearchFormByGetData(GetUtil::getData(), $searchModel);
                    if ($searchModel->savedSearchId != null)
                    {
                        $dataCollection = new SavedSearchAttributesDataCollection($searchModel);
                    }
                }
                if ($stickySearchKey != null && $setSticky)
                {
                    if($stickySearchData == null)
                    {
                        $stickySearchData = array();
                    }
                    SavedSearchUtil::setDataByKeyAndDataCollection($stickySearchKey, $dataCollection, $stickySearchData);
                }
                $searchModel->loadSavedSearchUrl = Yii::app()->createUrl($this->getModule()->getId() . '/' . $this->getId() . '/list/');
            }
            return $dataCollection;
        }

        protected function resolveToTriggerOnSearchEvents($listModelClassName)
        {
            $pageVariableName = $listModelClassName . '_page';
            if (isset($_GET[$pageVariableName]) && $_GET[$pageVariableName] == null)
            {
                Yii::app()->gameHelper->triggerSearchModelsEvent($listModelClassName);
            }
        }

        protected function getDataProviderByResolvingSelectAllFromGet(
            $searchModel,
            $pageSize,
            $userId,
            $stateMetadataAdapterClassName = null,
            $stickySearchKey = null
            )
        {
            assert('$searchModel instanceof RedBeanModel || $searchModel instanceof ModelForm');
            assert('is_string($stickySearchKey) || $stickySearchKey == null');
            if ($_GET['selectAll'])
            {
                if (!isset($_GET[get_class($searchModel)]) && $stickySearchKey != null)
                {
                    $resolvedStickySearchKey = $stickySearchKey;
                }
                else
                {
                    $resolvedStickySearchKey = null;
                }
                return $this->resolveSearchDataProvider(
                    $searchModel,
                    $pageSize,
                    $stateMetadataAdapterClassName,
                    $resolvedStickySearchKey);
            }
            else
            {
                return null;
            }
        }

        /**
         * This method is called after a mass edit form is first submitted.
         * It is called from the actionMassEdit.
         * @see actionMassEdit in the module default controllers.
         */
        protected function processMassEdit(
            $pageSize,
            $activeAttributes,
            $selectedRecordCount,
            $pageViewClassName,
            $listModel,
            $title,
            $dataProvider = null
            )
        {
            // TODO: @Shoaibi/@Jason: Low: Deprecated
            // trigger_error('Deprecated');
            assert('$dataProvider == null || $dataProvider instanceof CDataProvider');
            $modelClassName = get_class($listModel);
            $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            if (isset($_POST[$modelClassName]))
            {
                PostUtil::sanitizePostForSavingMassEdit($modelClassName);
                //Generically test that the changes are valid before attempting to save on each model.
                $sanitizedPostData = PostUtil::sanitizePostByDesignerTypeForSavingModel(new $modelClassName(false), $_POST[$modelClassName]);
                $sanitizedOwnerPostData = PostUtil::sanitizePostDataToJustHavingElementForSavingModel($sanitizedPostData, 'owner');
                $sanitizedPostDataWithoutOwner = PostUtil::removeElementFromPostDataForSavingModel($sanitizedPostData, 'owner');
                $massEditPostDataWithoutOwner = PostUtil::removeElementFromPostDataForSavingModel($_POST['MassEdit'], 'owner');
                $listModel->setAttributes($sanitizedPostDataWithoutOwner);
                if ($listModel->validate(array_keys($massEditPostDataWithoutOwner)))
                {
                    $passedOwnerValidation = true;
                    if ($sanitizedOwnerPostData != null)
                    {
                        $listModel->setAttributes($sanitizedOwnerPostData);
                        $passedOwnerValidation = $listModel->validate(array('owner'));
                    }
                    if ($passedOwnerValidation)
                    {
                        MassEditInsufficientPermissionSkipSavingUtil::clear($modelClassName);
                        Yii::app()->gameHelper->triggerMassEditEvent(get_class($listModel));
                        $this->saveMassEdit(
                            get_class($listModel),
                            $modelClassName,
                            $selectedRecordCount,
                            $dataProvider,
                            $_GET[$modelClassName . '_page'],
                            $pageSize
                        );
                        //cancel diminish of save scoring
                        if ($selectedRecordCount > $pageSize)
                        {
                            $view = new $pageViewClassName(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this,
                                $this->makeMassEditProgressView(
                                    $listModel,
                                    1,
                                    $selectedRecordCount,
                                    1,
                                    $pageSize,
                                    $title,
                                    null)
                            ));
                            echo $view->render();
                            Yii::app()->end(0, false);
                        }
                        else
                        {
                            $skipCount = MassEditInsufficientPermissionSkipSavingUtil::getCount($modelClassName);
                            $successfulCount = MassEditInsufficientPermissionSkipSavingUtil::resolveSuccessfulCountAgainstSkipCount(
                                $selectedRecordCount, $skipCount);
                            MassEditInsufficientPermissionSkipSavingUtil::clear($modelClassName);
                            $notificationContent = Zurmo::t('ZurmoModule', 'Successfully updated') . ' ' .
                                                    $successfulCount . ' ' .
                                                    LabelUtil::getUncapitalizedRecordLabelByCount($successfulCount) .
                                                    '.';
                            if ($skipCount > 0)
                            {
                                $notificationContent .= ' ' .
                                    MassEditInsufficientPermissionSkipSavingUtil::getSkipCountMessageContentByModelClassName(
                                        $skipCount, $modelClassName);
                            }
                            Yii::app()->user->setFlash('notification', $notificationContent);
                            $this->redirect(array('default/'));
                            Yii::app()->end(0, false);
                        }
                    }
                }
            }
            return $listModel;
        }

        /**
         * Called only during a mulitple phase save from mass edit. This occurs if the quantity of models to save
         * is greater than the pageSize. This signals a save that must be conducted in phases where each phase
         * updates a quantity of models no greater than the page size.
         */
        protected function processMassEditProgressSave(
            $modelClassName,
            $pageSize,
            $title,
            $dataProvider = null)
        {
            // TODO: @Shoaibi/@Jason: Low: Deprecated
            // trigger_error('Deprecated');
            assert('$dataProvider == null || $dataProvider instanceof CDataProvider');
            $listModel = new $modelClassName(false);
            $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            PostUtil::sanitizePostForSavingMassEdit($modelClassName);
            $this->saveMassEdit(
                get_class($listModel),
                $modelClassName,
                $selectedRecordCount,
                $dataProvider,
                $_GET[$modelClassName . '_page'],
                $pageSize
            );
            $view = $this->makeMassEditProgressView(
                $listModel,
                $_GET[$modelClassName . '_page'],
                $selectedRecordCount,
                $this->getMassEditProgressStartFromGet(
                    $modelClassName,
                    $pageSize
                ),
                $pageSize,
                $title,
                MassEditInsufficientPermissionSkipSavingUtil::getCount($modelClassName)
            );
            echo $view->renderRefreshJSONScript();
        }

        protected function makeMassEditProgressView(
            $model,
            $page,
            $selectedRecordCount,
            $start,
            $pageSize,
            $title,
            $skipCount)
        {
            // TODO: @Shoaibi/@Jason: Low: Deprecated
            // trigger_error('Deprecated');
            assert('$skipCount == null || is_int($skipCount)');
            return new MassEditProgressView(
                $this->getId(),
                $this->getModule()->getId(),
                $model,
                $selectedRecordCount,
                $start,
                $pageSize,
                $page,
                'massEditProgressSave',
                $title,
                $skipCount
            );
        }

        /**
         * Called either from a mass edit save, or a mass edit progress save.
         */
        protected function saveMassEdit($modelClassName, $postVariableName, $selectedRecordCount, $dataProvider, $page, $pageSize)
        {
            // TODO: @Shoaibi/@Jason: Low: Deprecated
            // trigger_error('Deprecated');
            Yii::app()->gameHelper->muteScoringModelsOnSave();
            $modelsToSave = $this->getModelsToSave($modelClassName, $dataProvider, $selectedRecordCount, $page, $pageSize);
            foreach ($modelsToSave as $modelToSave)
            {
                if (ControllerSecurityUtil::doesCurrentUserHavePermissionOnSecurableItem($modelToSave, Permission::WRITE))
                {
                    $sanitizedPostData = PostUtil::sanitizePostByDesignerTypeForSavingModel($modelToSave, $_POST[$modelClassName]);
                    $sanitizedOwnerPostData = PostUtil::sanitizePostDataToJustHavingElementForSavingModel($sanitizedPostData, 'owner');
                    $sanitizedPostDataWithoutOwner = PostUtil::removeElementFromPostDataForSavingModel($sanitizedPostData, 'owner');
                    $modelToSave->setAttributes($sanitizedPostDataWithoutOwner);
                    if ($sanitizedOwnerPostData != null)
                    {
                        $modelToSave->setAttributes($sanitizedOwnerPostData);
                    }
                    $modelToSave->save(false);
                }
                else
                {
                    MassEditInsufficientPermissionSkipSavingUtil::setByModelIdAndName(
                        $modelClassName, $modelToSave->id, $modelToSave->name);
                }
            }
            Yii::app()->gameHelper->unmuteScoringModelsOnSave();
        }

        /**
         * This method is called after a mass delete form is first submitted.
         * It is called from the actionMassDelete.
         * @see actionMassDelete in the module default controllers.
         */
        protected function processMassDelete(
            $pageSize,
            $activeAttributes,
            $selectedRecordCount,
            $pageViewClassName,
            $listModel,
            $title,
            $dataProvider = null
            )
        {
            // TODO: @Shoaibi/@Jason: Low: Deprecated
            // trigger_error('Deprecated');
            assert('$dataProvider == null || $dataProvider instanceof CDataProvider');
            $modelClassName = get_class($listModel);
            $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            if (isset($_POST['selectedRecordCount']))
            {
                        $this->doMassDelete(
                            get_class($listModel),
                            $modelClassName,
                            $selectedRecordCount,
                            $dataProvider,
                            $_GET[$modelClassName . '_page'],
                            $pageSize
                        );

                        // Cancel diminish of save scoring
                        if ($selectedRecordCount > $pageSize)
                        {
                            $view = new $pageViewClassName( ZurmoDefaultViewUtil::
                                                            makeStandardViewForCurrentUser($this,
                                                            $this->makeMassDeleteProgressView(
                                                            $listModel,
                                                            1,
                                                            $selectedRecordCount,
                                                            1,
                                                            $pageSize,
                                                            $title,
                                                            null)
                            ));
                            echo $view->render();
                            Yii::app()->end(0, false);
                        }
                        else
                        {
                            $skipCount = MassDeleteInsufficientPermissionSkipSavingUtil::getCount($modelClassName);
                            $successfulCount = MassDeleteInsufficientPermissionSkipSavingUtil::resolveSuccessfulCountAgainstSkipCount(
                                $selectedRecordCount, $skipCount);
                            MassDeleteInsufficientPermissionSkipSavingUtil::clear($modelClassName);
                            $notificationContent =  $successfulCount . ' ' .
                                                    LabelUtil::getUncapitalizedRecordLabelByCount($successfulCount) .
                                                    ' ' . Zurmo::t('ZurmoModule', 'successfully deleted') . '.';
                            if ($skipCount > 0)
                            {
                                $notificationContent .= ' ' .
                                    MassDeleteInsufficientPermissionSkipSavingUtil::getSkipCountMessageContentByModelClassName(
                                        $skipCount, $modelClassName);
                            }
                            Yii::app()->user->setFlash('notification', $notificationContent);
                            $this->redirect(array('default/'));
                            Yii::app()->end(0, false);
                        }
                    }
            return $listModel;
        }

        protected function processMassDeleteProgress(
            $modelClassName,
            $pageSize,
            $title,
            $dataProvider = null)
        {
            // TODO: @Shoaibi/@Jason: Low: Deprecated
            // trigger_error('Deprecated');
            assert('$dataProvider == null || $dataProvider instanceof CDataProvider');
            $listModel = new $modelClassName(false);

            $postData = PostUtil::getData();
            $selectedRecordCount = ArrayUtil::getArrayValue($postData, 'selectedRecordCount');

            $this->doMassDelete(
                get_class($listModel),
                $modelClassName,
                $selectedRecordCount,
                $dataProvider,
                $_GET[$modelClassName . '_page'],
                $pageSize
            );
            $view = $this->makeMassDeleteProgressView(
                $listModel,
                $_GET[$modelClassName . '_page'],
                $selectedRecordCount,
                $this->getMassDeleteProgressStartFromGet(
                    $modelClassName,
                    $pageSize
                ),
                $pageSize,
                $title,
                MassDeleteInsufficientPermissionSkipSavingUtil::getCount($modelClassName)
            );
            echo $view->renderRefreshJSONScript();
        }

        protected function makeMassDeleteProgressView(
            $model,
            $page,
            $selectedRecordCount,
            $start,
            $pageSize,
            $title,
            $skipCount)
        {
            // TODO: @Shoaibi/@Jason: Low: Deprecated
            // trigger_error('Deprecated');
            assert('$skipCount == null || is_int($skipCount)');
            return new MassDeleteProgressView(
                $this->getId(),
                $this->getModule()->getId(),
                $model,
                $selectedRecordCount,
                $start,
                $pageSize,
                $page,
                'massDeleteProgress',
                $title,
                $skipCount
            );
        }

        protected function doMassDelete($modelClassName, $postVariableName, $selectedRecordCount, $dataProvider, $page, $pageSize)
        {
            // TODO: @Shoaibi/@Jason: Low: Deprecated
            // trigger_error('Deprecated');
            Yii::app()->gameHelper->muteScoringModelsOnDelete();
            $modelsToDelete = $this->getModelsToDelete($modelClassName, $dataProvider, $selectedRecordCount, $page, $pageSize);
            foreach ($modelsToDelete as $modelToDelete)
            {
                if (ControllerSecurityUtil::doesCurrentUserHavePermissionOnSecurableItem($modelToDelete, Permission::DELETE))
                {
                    $modelToDelete->delete(false);
                }
                else
                {
                    MassDeleteInsufficientPermissionSkipSavingUtil::setByModelIdAndName(
                        $modelClassName, $modelToDelete->id, $modelToDelete->name);
                }
            }
            Yii::app()->gameHelper->unmuteScoringModelsOnDelete();
        }

        protected static function resolvePageValueForMassAction($modelClassName)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            return intval(Yii::app()->request->getQuery($modelClassName . '_page'));
        }

        protected static function resolveReturnUrlForMassAction()
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            return Yii::app()->createUrl('/' . Yii::app()->getController()->getModule()->getId() . '/' .
                                                                            Yii::app()->getController()->getId() . '/');
        }

        protected static function resolveViewIdByMassActionId($actionId, $returnProgressViewName, $moduleName = null)
        {
            if (strpos($actionId, 'massEdit') === 0 || strpos($actionId, 'massDelete') === 0)
            {
                $viewNameSuffix    = (!$returnProgressViewName)? 'View': 'ProgressView';
                $viewNamePrefix    = static::resolveMassActionId($actionId, true);
                if (strpos($actionId, 'massEdit') === 0)
                {
                    $viewNamePrefix = $moduleName . $viewNamePrefix;
                }
                return $viewNamePrefix . $viewNameSuffix;
            }
            else
            {
                throw new NotSupportedException();
            }
        }


        protected static function resolveTitleByMassActionId($actionId)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            if (strpos($actionId, 'massDelete') === 0)
            {
                return Zurmo::t('Core', 'Mass Delete');
            }
            else if (strpos($actionId, 'massEdit') === 0)
            {
                return Zurmo::t('Core', 'Mass Update');
            }
            else
            {
                throw new NotSupportedException();
            }
        }

        /**
         * Check if form is posted. If form is posted attempt to save. If save is complete, confirm the current
         * user can still read the model.  If not, then redirect the user to the index action for the module.
         */
        protected function attemptToSaveModelFromPost($model, $redirectUrlParams = null, $redirect = true)
        {
            assert('$redirectUrlParams == null || is_array($redirectUrlParams) || is_string($redirectUrlParams)');
            $savedSuccessfully   = false;
            $modelToStringValue = null;
            $postVariableName   = get_class($model);
            if (isset($_POST[$postVariableName]))
            {
                $postData = $_POST[$postVariableName];
                $controllerUtil   = static::getZurmoControllerUtil();
                $model            = $controllerUtil->saveModelFromPost($postData, $model, $savedSuccessfully,
                                                                       $modelToStringValue);
            }
            if ($savedSuccessfully && $redirect)
            {
                $this->actionAfterSuccessfulModelSave($model, $modelToStringValue, $redirectUrlParams);
            }
            return $model;
        }

        protected static function getZurmoControllerUtil()
        {
            return new ZurmoControllerUtil();
        }

        protected function actionAfterSuccessfulModelSave($model, $modelToStringValue, $redirectUrlParams = null)
        {
            assert('is_string($modelToStringValue)');
            assert('$redirectUrlParams == null || is_array($redirectUrlParams) || is_string($redirectUrlParams)');
            if (ControllerSecurityUtil::doesCurrentUserHavePermissionOnSecurableItem($model, Permission::READ))
            {
                $this->redirectAfterSaveModel($model->id, $redirectUrlParams);
            }
            else
            {
                $notificationContent = Zurmo::t('ZurmoModule', 'You no longer have permissions to access {modelName}.',
                    array('{modelName}' => $modelToStringValue)
                );
                Yii::app()->user->setFlash('notification', $notificationContent);
                $this->redirect(array($this->getId() . '/index'));
            }
        }

        protected function redirectAfterSaveModel($modelId, $urlParams = null)
        {
            if ($urlParams == null)
            {
                $urlParams = array($this->getId() . '/details', 'id' => $modelId);
            }
            $this->redirect($urlParams);
        }

        protected static function getModelAndCatchNotFoundAndDisplayError($modelClassName, $id)
        {
            assert('is_string($modelClassName)');
            assert('is_int($id)');
            try
            {
                return $modelClassName::getById($id);
            }
            catch (NotFoundException $e)
            {
                $messageContent  = Zurmo::t('ZurmoModule', 'The record you are trying to access does not exist.');
                $messageView     = new ModelNotFoundView($messageContent);
                $view            = new ModelNotFoundPageView($messageView);
                echo $view->render();
                Yii::app()->end(0, false);
            }
        }

        protected static function triggerMassAction($modelClassName, $searchForm, $pageView, $title, $searchView = null, $stateMetadataAdapterClassName = null)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            $actionId               = Yii::app()->getController()->getAction()->getId();
            $pageSize               = static::resolvePageSizeByMassActionId($actionId);
            $model                  = new $modelClassName(false);
            $dataProvider           = Yii::app()->getController()->getDataProviderByResolvingSelectAllFromGet(
                                                                                        new $searchForm($model),
                                                                                        $pageSize,
                                                                                        Yii::app()->user->userModel->id,
                                                                                        $stateMetadataAdapterClassName,
                                                                                        $searchView
                                                                                    );
            if (strpos($actionId, 'Progress') !== false)
            {
                static::massActionProgress($model, $pageSize, $title, $actionId, $dataProvider);
            }
            else
            {
                static::massAction($model, $pageSize, $title, $pageView, $actionId, $dataProvider);
            }
        }

        protected static function massActionProgress($model, $pageSize, $title, $actionId, $dataProvider)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            static::processMassActionProgress(
                                                $model,
                                                $pageSize,
                                                $title,
                                                $actionId,
                                                $dataProvider
                                            );
        }

        protected static function massAction($model, $pageSize, $title, $pageView, $actionId, $dataProvider)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            $activeAttributes       = static::resolveActiveAttributesFromPostForMassAction($actionId);
            $selectedRecordCount    = static::resolveSelectedRecordCountByMassActionId($actionId, $dataProvider, array());
            $model                  = static::processMassAction(
                                                                $pageSize,
                                                                $selectedRecordCount,
                                                                $pageView,
                                                                $model,
                                                                $title,
                                                                $actionId,
                                                                $dataProvider
                                                            );
            $massActionView         = static::makeMassActionView(
                                                                $model,
                                                                $activeAttributes,
                                                                $selectedRecordCount,
                                                                $title,
                                                                $actionId
                                                            );
            $view                   = new $pageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser(
                                                                        Yii::app()->getController(), $massActionView));
            echo $view->render();
        }

        protected static function processMassAction($pageSize, $selectedRecordCount, $pageViewClassName, $listModel, $title,
                                                    $actionId, $dataProvider = null)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            assert('$dataProvider == null || $dataProvider instanceof CDataProvider');
            $postSelectedRecordCount                    = Yii::app()->request->getPost('selectedRecordCount');
            $modelClassName                             = get_class($listModel);
            $postModelClassName                         = Yii::app()->request->getPost($modelClassName);
            if (isset($postSelectedRecordCount) || isset($postModelClassName))
            {

                $page                                   = static::resolvePageValueForMassAction($modelClassName);
                $insufficientPermissionSkipSavingUtil   = static::resolveInsufficientPermissionSkipSavingUtilByMassActionId($actionId);
                $start                                  = ($selectedRecordCount > $pageSize)? 1: $selectedRecordCount;
                $skipCount                              = ($selectedRecordCount > $pageSize)? null: 0;
                $massActionSuccessful                   = static::processModelsForMassAction($listModel,
                                                                                    $modelClassName,
                                                                                    $selectedRecordCount,
                                                                                    $dataProvider,
                                                                                    $page,
                                                                                    $pageSize,
                                                                                    $insufficientPermissionSkipSavingUtil,
                                                                                    $postModelClassName,
                                                                                    $actionId);
                if ($massActionSuccessful)
                {
                    $progressView                           = static::makeMassActionProgressView(
                                                                                                $listModel,
                                                                                                1,
                                                                                                $selectedRecordCount,
                                                                                                $start,
                                                                                                $pageSize,
                                                                                                $title,
                                                                                                $skipCount,
                                                                                                $actionId);
                    if ($selectedRecordCount > $pageSize)
                    {
                        $view                               = new $pageViewClassName(
                                                                    ZurmoDefaultViewUtil::makeStandardViewForCurrentUser(
                                                                                Yii::app()->getController(),$progressView));
                        echo $view->render();
                    }
                    else
                    {
                        $refreshScript = $progressView->renderRefreshScript();
                        Yii::app()->user->setFlash('notification', $refreshScript['message']);
                        Yii::app()->getController()->redirect(static::resolveReturnUrlForMassAction());
                    }
                    Yii::app()->end(0, false);
                }
            }
            return $listModel;
        }

        protected static function processModelsForMassAction($model, $modelClassName, $selectedRecordCount, $dataProvider,
                                                             $page, $pageSize, $insufficientPermissionSkipSavingUtil,
                                                             $postModelClassName, $actionId)
        {
            $doMassActionFunctionName               = 'processModelsForMassActionWithoutScoring';
            $doMassActionFunctionArguments          = array(
                                                            $modelClassName,
                                                            $selectedRecordCount,
                                                            $dataProvider,
                                                            $page,
                                                            $pageSize,
                                                            $insufficientPermissionSkipSavingUtil,
                                                            $actionId
                                                        );
            if (strpos($actionId, 'massEdit') === 0 && strpos($actionId, 'Progress') === false)
            {
                $doMassActionFunctionName           = 'processModelsForMassEditAction';
                array_unshift($doMassActionFunctionArguments, $postModelClassName, $model);

            }
            $doMassActionFunctionName               = 'static::' . $doMassActionFunctionName;
            return call_user_func_array($doMassActionFunctionName, $doMassActionFunctionArguments);
        }

        protected static function processModelsForMassActionWithoutScoring($modelClassName, $selectedRecordCount, $dataProvider,
                                                                            $page, $pageSize, $insufficientPermissionSkipSavingUtil, $actionId)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            $returnValue        = false;
            static::toggleMuteScoringModelValueByMassActionId($actionId, true);
            $modelsToProcess    = static::getModelsToUpdate($modelClassName, $dataProvider, $selectedRecordCount, $page, $pageSize);
            $modelPermission    = static::resolvePermissionOnSecurableItemByMassActionId($actionId);
            foreach ($modelsToProcess as $modelToProcess)
            {
                if (ControllerSecurityUtil::doesCurrentUserHavePermissionOnSecurableItem($modelToProcess, $modelPermission))
                {
                    $function = 'processModelFor' . static::resolveMassActionId($actionId, true);
                    $returnValue = static::$function($modelToProcess);
                }
                else
                {
                    $insufficientPermissionSkipSavingUtil::setByModelIdAndName($modelClassName,
                        $modelToProcess->id,
                        $modelToProcess->name);
                }
            }
            static::toggleMuteScoringModelValueByMassActionId($actionId, false);
            return $returnValue;
        }

        protected static function processModelsForMassEditAction($postModelClassName, $model, $modelClassName, $selectedRecordCount, $dataProvider,
                                                                $page, $pageSize, $insufficientPermissionSkipSavingUtil, $actionId)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            PostUtil::sanitizePostForSavingMassEdit($modelClassName);
            //Generically test that the changes are valid before attempting to save on each model.
            $sanitizedPostData              = PostUtil::sanitizePostByDesignerTypeForSavingModel(new $modelClassName(false), $postModelClassName);
            $sanitizedOwnerPostData         = PostUtil::sanitizePostDataToJustHavingElementForSavingModel($sanitizedPostData, 'owner');
            $sanitizedPostDataWithoutOwner  = PostUtil::removeElementFromPostDataForSavingModel($sanitizedPostData, 'owner');
            $postMassEdit                   = Yii::app()->request->getPost('MassEdit');
            $massEditPostDataWithoutOwner   = PostUtil::removeElementFromPostDataForSavingModel($postMassEdit, 'owner');
            $model->setAttributes($sanitizedPostDataWithoutOwner);
            if ($model->validate(array_keys($massEditPostDataWithoutOwner)))
            {
                $passedOwnerValidation      = true;
                if ($sanitizedOwnerPostData != null)
                {
                    $model->setAttributes($sanitizedOwnerPostData);
                    $passedOwnerValidation  = $model->validate(array('owner'));
                }
                if ($passedOwnerValidation)
                {
                    MassEditInsufficientPermissionSkipSavingUtil::clear($modelClassName);
                    Yii::app()->gameHelper->triggerMassEditEvent($modelClassName);
                    return static::processModelsForMassActionWithoutScoring($modelClassName, $selectedRecordCount, $dataProvider,
                                                $page, $pageSize, $insufficientPermissionSkipSavingUtil, $actionId);
                }
            }
            return false;
        }

        protected static function makeMassActionView(
                                                    $model,
                                                    $activeAttributes,
                                                    $selectedRecordCount,
                                                    $title,
                                                    $actionId)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            $moduleName                 = Yii::app()->getController()->getModule()->getPluralCamelCasedName();
            $moduleClassName            = $moduleName . 'Module';
            $alertMessage               = static::resolveMassActionAlertMessage(get_class($model), $actionId);
            $title                      = static::resolveTitleByMassActionId($actionId) . ': ' . $title;
            $massActionViewClassName    = static::resolveViewIdByMassActionId($actionId, false, $moduleName);
            $view                       = new $massActionViewClassName(Yii::app()->getController()->getId(),
                                                            Yii::app()->getController()->getModule()->getId(),
                                                            $model, $activeAttributes, $selectedRecordCount, $title,
                                                            $alertMessage, $moduleClassName);
            return $view;
        }

        protected static function makeMassActionProgressView(
                                                            $model,
                                                            $page,
                                                            $selectedRecordCount,
                                                            $start,
                                                            $pageSize,
                                                            $title,
                                                            $skipCount,
                                                            $actionId)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            assert('$skipCount == null || is_int($skipCount)');
            $massActionProgressActionName   = static::resolveProgressActionId($actionId);
            $progressViewClassName          = static::resolveViewIdByMassActionId($actionId, true);
            $params                         = static::resolveParamsForMassProgressView();
            return new $progressViewClassName(
                                                Yii::app()->getController()->getId(),
                                                Yii::app()->getController()->getModule()->getId(),
                                                $model,
                                                $selectedRecordCount,
                                                $start,
                                                $pageSize,
                                                $page,
                                                $massActionProgressActionName,
                                                $title,
                                                $skipCount,
                                                $params
                                            );
        }

        protected static function processMassActionProgress(
                                                            $listModel,
                                                            $pageSize,
                                                            $title,
                                                            $actionId,
                                                            $dataProvider = null)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            assert('$dataProvider == null || $dataProvider instanceof CDataProvider');
            $modelClassName                         = get_class($listModel);
            $startPage                              = static::resolvePageValueForMassAction($modelClassName);
            $postData                               = static::resolvePostDataByMassActionId($actionId, $modelClassName);
            $postModelClassName                     = Yii::app()->request->getPost($modelClassName);
            $pageVariableName                       = $modelClassName . '_page';
            $selectedRecordCount                    = static::resolveSelectedRecordCountByMassActionId($actionId, $dataProvider, $postData);
            $insufficientPermissionSkipSavingUtil   = static::resolveInsufficientPermissionSkipSavingUtilByMassActionId($actionId);
            static::processModelsForMassAction($listModel,
                                                $modelClassName,
                                                $selectedRecordCount,
                                                $dataProvider,
                                                $startPage,
                                                $pageSize,
                                                $insufficientPermissionSkipSavingUtil,
                                                $postModelClassName,
                                                $actionId);
            $view                                   = static::makeMassActionProgressView(
                                                        $listModel,
                                                        $startPage,
                                                        $selectedRecordCount,
                                                        static::getMassActionProgressStartFromGet($pageVariableName, $pageSize),
                                                        $pageSize,
                                                        $title,
                                                        $insufficientPermissionSkipSavingUtil::getCount($modelClassName),
                                                        $actionId);
            echo $view->renderRefreshJSONScript();
        }

        protected static function resolvePostDataByMassActionId($actionId, $modelClassName = null)
        {
            if (strpos($actionId, 'massEdit') === 0)
            {
                PostUtil::sanitizePostForSavingMassEdit($modelClassName);
            }
            return PostUtil::getData();
        }

        protected static function resolveSelectedRecordCountByMassActionId($actionId, $dataProvider = null, $postData = array())
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            if (strpos($actionId, 'Progress') !== false && strpos($actionId, 'massEdit') === false)
            {
                $selectedRecordCount = ArrayUtil::getArrayValue($postData, 'selectedRecordCount');
            }
            return $selectedRecordCount;

        }

        protected static function resolveMassActionAlertMessage($postVariableName, $actionId)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            $actionId               = static::resolveMassActionId($actionId, true);
            $alertMessageHandler    = 'resolve' . $actionId . 'AlertMessage';
            return (method_exists(get_called_class(), $alertMessageHandler))?
                static::$alertMessageHandler($postVariableName) : null;
        }


        protected static function resolvePageSizeByMassActionId($actionId)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            $type = 'massEditProgressPageSize';
            if (strpos($actionId, 'massDelete') === 0)
            {
                $type = 'massDeleteProgressPageSize';
            }
            return Yii::app()->pagination->resolveActiveForCurrentUserByType($type);
        }

        protected static function toggleMuteScoringModelValueByMassActionId($actionId, $mute = true)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            $toggle = ($mute)? 'mute' : 'unmute';
            $function = (strpos($actionId, 'massDelete') === 0)? 'Delete' : 'Save';
            $function = $toggle . 'ScoringModelsOn' . $function;
            Yii::app()->gameHelper->$function();
        }

        protected static function resolvePermissionOnSecurableItemByMassActionId($actionId)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            return (strpos($actionId, 'massDelete') === 0) ? Permission::DELETE : Permission::WRITE;
        }

        protected static function processModelForMassDelete(& $model)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            if(!$model->delete(false))
            {
                throw new FailedToDeleteModelException();
            }
            else
            {
                return true;
            }
        }

        protected static function processModelForMassEdit(& $model)
        {
            $postModelClassName             = Yii::app()->request->getPost(get_class($model));
            $sanitizedPostData              = PostUtil::sanitizePostByDesignerTypeForSavingModel($model, $postModelClassName);
            $sanitizedOwnerPostData         = PostUtil::sanitizePostDataToJustHavingElementForSavingModel($sanitizedPostData, 'owner');
            $sanitizedPostDataWithoutOwner  = PostUtil::removeElementFromPostDataForSavingModel($sanitizedPostData, 'owner');
            $model->setAttributes($sanitizedPostDataWithoutOwner);
            if ($sanitizedOwnerPostData != null)
            {
                $model->setAttributes($sanitizedOwnerPostData);
            }
            if(!$model->save(false))
            {
                throw new FailedToSaveModelException();
            }
            else
            {
                return true;
            }
        }

        protected static function resolveInsufficientPermissionSkipSavingUtilByMassActionId($actionId)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            $insufficientPermissionSkipSavingUtil   = 'MassEditInsufficientPermissionSkipSavingUtil';
            if (strpos($actionId, 'massDelete') === 0)
            {
                $insufficientPermissionSkipSavingUtil   = 'MassDeleteInsufficientPermissionSkipSavingUtil';
            }
            return $insufficientPermissionSkipSavingUtil;
        }

        protected static function resolveProgressActionId($actionId)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            $actionId = static::resolveMassActionId($actionId, false);
            $actionId .= 'Progress';
            $actionId = (strpos($actionId, 'massEdit') === 0)? $actionId . 'Save' : $actionId;
            return $actionId;
        }

        protected static function resolveParamsForMassProgressView()
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            return array(
                'insufficientPermissionSkipSavingUtil'  => null,
                'returnUrl'                             => static::resolveReturnUrlForMassAction(),
                'returnMessage'                         => null,
            );
        }

        protected static function resolveMassActionId($actionId, $capitalizeFirst = true)
        {
            // TODO: @Shoaibi/@Jason: Low: Candidate for MassActionController
            $actionId = str_replace(array('Progress', 'Save'), '', $actionId);
            return ($capitalizeFirst)? ucfirst($actionId) : $actionId;
        }
    }
?>

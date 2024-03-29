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

    class HeaderView extends View
    {
        protected $verticalGridView;

        public function __construct($controllerId, $moduleId, $settingsMenuItems, $userMenuItems,
                                    $shortcutsCreateMenuItems,
                                    $moduleNamesAndLabels, $sourceUrl, $applicationName)
        {
            assert('is_string($controllerId)');
            assert('is_string($moduleId)');
            assert('is_array($settingsMenuItems)');
            assert('is_array($userMenuItems)');
            assert('is_array($shortcutsCreateMenuItems)');
            assert('is_array($moduleNamesAndLabels)');
            assert('is_string($sourceUrl)');
            assert('is_string($applicationName) || $applicationName == null');

            $shortcutsCreateMenuView = new ShortcutsCreateMenuView(
                                                                $controllerId,
                                                                $moduleId,
                                                                $shortcutsCreateMenuItems
                                                            );
            $this->verticalGridView   = new GridView(2, 1);
            $this->verticalGridView->setView(
                                        new HeaderLinksView($settingsMenuItems, $userMenuItems,
                                                            $applicationName), 0, 0);
            $globalSearchAndShortcutsCreateMenuView = new GlobalSearchAndShortcutsCreateMenuView($moduleNamesAndLabels,
                                                          $sourceUrl,
                                                          $shortcutsCreateMenuView);
            $horizontalGridView = new GridView(1, 1);
            $horizontalGridView->setView($globalSearchAndShortcutsCreateMenuView, 0, 0);
            $this->verticalGridView->setView($horizontalGridView, 1, 0);

        }

        protected function renderContent()
        {
            $this->renderLoginRequiredAjaxResponse();
            return $this->verticalGridView->render();
        }

        protected function renderLoginRequiredAjaxResponse()
        {
            if (Yii::app()->user->loginRequiredAjaxResponse)
            {
                Yii::app()->clientScript->registerScript('ajaxLoginRequired', '
                    jQuery("body").ajaxSuccess(
                        function(event, request, options)
                        {
                            if (request.responseText == "' . Yii::app()->user->loginRequiredAjaxResponse . '")
                            {
                                window.location.reload(true);
                            }
                        }
                    );
                ');
            }
        }
    }
?>

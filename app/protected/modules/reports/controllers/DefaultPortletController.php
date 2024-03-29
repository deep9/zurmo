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
     * Default portlet controller for all portlet actions for reporting.
     */
    class ReportsDefaultPortletController extends ZurmoPortletController
    {
        public function actionDetails($id, $runReport = false)
        {
            $savedReport             = SavedReport::getById((int)$id);
            ControllerSecurityUtil::resolveCanCurrentUserAccessModule($savedReport->moduleClassName);
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($savedReport, true);
            $report                  = SavedReportToReportAdapter::makeReportBySavedReport($savedReport);
            $portlet                 = Portlet::getById(intval($_GET['portletId']));
            $portlet->params = array(
                    'controllerId'     => 'default',
                    'relationModuleId' => $this->getModule()->getId(),
                    'relationModel'    => $report,
                    'redirectUrl'      => Yii::app()->request->getRequestUri(),
                    'dataProvider'	   => $this->getDataProvider($report, $report->getId(), (bool)$runReport),
            );
            $portletView = $portlet->getView();
            if (!RightsUtil::canUserAccessModule($portletView::getModuleClassName(), Yii::app()->user->userModel))
            {
                $messageView = new AccessFailureView();
                $view        = new AccessFailurePageView($messageView);
                echo $view->render();
                Yii::app()->end(0, false);
            }
            $view            = new AjaxPageView($portletView);
            echo $view->render();
        }

        protected function getDataProvider(Report $report, $stickyKey, $runReport)
        {
            assert('is_string($stickyKey) || is_int($stickyKey)');
            assert('is_bool($runReport)');
            $getData   = GetUtil::getData();
            if (isset($getData['clearRuntimeFilters']) && $getData['clearRuntimeFilters'])
            {
                StickyReportUtil::clearDataByKey($stickyKey);
            }
            if (null != $stickyData = StickyReportUtil::getDataByKey($stickyKey))
            {
                StickyReportUtil::resolveStickyDataToReport($report, $stickyData);
            }
            $pageSize     = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'reportResultsListPageSize', get_class($this->getModule()));
            $dataProvider = ReportDataProviderFactory::makeByReport($report, $pageSize);
            if($runReport)
            {
                $dataProvider->setRunReport($runReport);
            }
            return $dataProvider;
        }
    }
?>

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
     * Api test controller, used to test actions related to ApiTestModelItem2
     */
    class ApiTestModelItem2ApiController extends ZurmoModuleApiController
    {
        public function actionCustomGet()
        {
            $params = Yii::app()->apiRequest->getParams();
            if (!isset($params['id']))
            {
                $message = Zurmo::t('ApiModule', 'The id specified was invalid.');
                throw new ApiException($message);
            }
            $result    =  $this->processRead((int)$params['id']);
            Yii::app()->apiHelper->sendResponse($result);
        }

        public function actionCustomList()
        {
            $params = Yii::app()->apiRequest->getParams();
            $result    =  $this->processList($params);
            Yii::app()->apiHelper->sendResponse($result);
        }

        public function actionCustomPost()
        {
            $params = Yii::app()->apiRequest->getParams();
            if (!isset($params['data']))
            {
                $message = Zurmo::t('ApiModule', 'Please provide data.');
                throw new ApiException($message);
            }
            $result    =  $this->processCreate($params['data']);
            Yii::app()->apiHelper->sendResponse($result);
        }

        public function actionCustomUpdate()
        {
            $params = Yii::app()->apiRequest->getParams();
            if (!isset($params['id']))
            {
                $message = Zurmo::t('ApiModule', 'The id specified was invalid.');
                throw new ApiException($message);
            }
            $result    =  $this->processUpdate((int)$params['id'], $params['data']);
            Yii::app()->apiHelper->sendResponse($result);
        }

        public function actionCustomDelete()
        {
            $params = Yii::app()->apiRequest->getParams();
            if (!isset($params['id']))
            {
                $message = Zurmo::t('ApiModule', 'The id specified was invalid.');
                throw new ApiException($message);
            }
            $result    =  $this->processDelete((int)$params['id']);
            Yii::app()->apiHelper->sendResponse($result);
        }

        protected function getModelName()
        {
            return 'ApiTestModelItem2';
        }

        protected static function getSearchFormClassName()
        {
            return 'ApiTestModelItem2SearchForm';
        }
    }
?>

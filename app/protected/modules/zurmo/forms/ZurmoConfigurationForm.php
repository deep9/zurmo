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
     * Form to all editing and viewing of global configuration values in the user interface.
     */
    class ZurmoConfigurationForm extends ConfigurationForm
    {
        public $applicationName;
        public $timeZone;
        public $listPageSize;
        public $subListPageSize;
        public $modalListPageSize;
        public $dashboardListPageSize;
        public $gamificationModalNotificationsEnabled;
        public $userIdOfUserToRunWorkflowsAs;
        public $realtimeUpdatesEnabled;
        public $logoFileData;

        const DEFAULT_LOGO_THUMBNAIL_HEIGHT = 30;
        const DEFAULT_LOGO_THUMBNAIL_WIDTH  = 65;
        const DEFAULT_LOGO_HEIGHT           = 32;
        const DEFAULT_LOGO_WIDTH            = 107;
        const LOGO_THUMB_FILE_NAME_PREFIX   = 'logoThumb-';

        public function rules()
        {
            return array(
                array('applicationName',                        'type',    'type' => 'string'),
                array('applicationName',                        'length',  'max' => 64),
                array('timeZone',                               'required'),
                array('listPageSize',                           'required'),
                array('listPageSize',                           'type',      'type' => 'integer'),
                array('listPageSize',                           'numerical', 'min' => 1),
                array('subListPageSize',                        'required'),
                array('subListPageSize',                        'type',      'type' => 'integer'),
                array('subListPageSize',                        'numerical', 'min' => 1),
                array('modalListPageSize',                      'required'),
                array('modalListPageSize',                      'type',      'type' => 'integer'),
                array('modalListPageSize',                      'numerical', 'min' => 1),
                array('dashboardListPageSize',                  'required'),
                array('dashboardListPageSize',                  'type',      'type' => 'integer'),
                array('dashboardListPageSize',                  'numerical', 'min' => 1),
                array('gamificationModalNotificationsEnabled',  'boolean'),
                array('realtimeUpdatesEnabled',                 'boolean'),
                array('subListPageSize',                        'type',      'type' => 'integer'),
                array('logoFileData',                           'type',      'type' => 'array'),
                array('userIdOfUserToRunWorkflowsAs',           'type',      'type' => 'integer'),
                array('userIdOfUserToRunWorkflowsAs',           'numerical', 'min'  => 1),
            );
        }

        public function attributeLabels()
        {
            return array(
                'applicationName'                       => Zurmo::t('ZurmoModule', 'Application Name'),
                'timeZone'                              => Zurmo::t('ZurmoModule', 'Time zone'),
                'listPageSize'                          => Zurmo::t('ZurmoModule', 'List page size'),
                'subListPageSize'                       => Zurmo::t('ZurmoModule', 'Sublist page size'),
                'modalListPageSize'                     => Zurmo::t('ZurmoModule', 'Popup list page size'),
                'dashboardListPageSize'                 => Zurmo::t('ZurmoModule', 'Dashboard portlet list page size'),
                'gamificationModalNotificationsEnabled' => Zurmo::t('ZurmoModule', 'Enable game notification popup'),
                'realtimeUpdatesEnabled'                => Zurmo::t('ZurmoModule', 'Enable real-time updates'),
                'userIdOfUserToRunWorkflowsAs'          => Zurmo::t('ZurmoModule', 'User to run workflows as'),
            );
        }
    }
?>
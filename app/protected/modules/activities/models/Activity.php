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
     * Extend this class to make different types of activity models.
     *
     */
    abstract class Activity extends OwnedSecurableItem
    {
        public static function getByName($name)
        {
            return self::getByNameOrEquivalent('name', $name);
        }

        protected static function translatedAttributeLabels($language)
        {
            return array_merge(parent::translatedAttributeLabels($language),
                array(
                    'latestDateTime' => Zurmo::t('ActivitiesModule', 'Latest Date Time',  array(), null, $language),
                    'activityItems'  => Zurmo::t('ActivitiesModule', 'Activity Items',    array(), null, $language),
                )
            );
        }

        public static function canSaveMetadata()
        {
            return false;
        }

        public function onCreated()
        {
            parent::onCreated();
            $this->unrestrictedSet('latestDateTime', DateTimeUtil::convertTimestampToDbFormatDateTime(time()));
        }

        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    'latestDateTime',
                ),
                'relations' => array(
                    'activityItems' => array(RedBeanModel::MANY_MANY, 'Item'),
                ),
                'rules' => array(
                    array('latestDateTime', 'required'),
                    array('latestDateTime', 'readOnly'),
                    array('latestDateTime', 'type', 'type' => 'datetime'),
                ),
                'elements' => array(
                    'activityItems' => 'ActivityItem',
                    'latestDateTime' => 'DateTime'
                ),
                'activityItemsModelClassNames' => array(
                    'Account',
                    'Contact',
                    'Opportunity',
                ),
            );
            return $metadata;
        }

        public static function getModuleClassName()
        {
            return 'ActivitiesModule';
        }

        public static function isTypeDeletable()
        {
            return false;
        }
    }
?>
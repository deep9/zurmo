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
     * Helper class for working with portlets to maintain user configs across multiple sessions
     */
    class PortletPersistentConfigUtil
    {
        // TODO: @Shoaibi: Low: Write unit tests
        /**
         * Set a persistent config value for current user against portletId and keyName.
         * @param $portletId integer Id of the portlet to set value against
         * @param $keyName string Name of the key that should be set
         * @param $value string|integer|boolean Value that should be assigned to keyName config
         */
        public static function setForCurrentUserByPortletIdAndKey($portletId, $keyName, $value)
        {
            assert('is_int($portletId)');
            assert('is_string($portletId)');
            assert('is_string($keyName)');
            $moduleName = static::getModuleName();
            $keyName = static::resolveKeyNameByPortletId($portletId, $keyName);
            ZurmoConfigurationUtil::setForCurrentUserByModuleName($moduleName, $keyName, $value);
            Yii::app()->user->setState($keyName, $value);
        }

        /**
         * Get a persistent config value for current user against portletId and keyName.
         * @param $portletId integer  Id of the portlet to get value against
         * @param $keyName string Name of the key that should be returned
         * @param bool $returnBoolean bool Force return value to be boolean (explicit type casting)
         * @return bool|null|string
         */
        public static function getForCurrentUserByPortletIdAndKey($portletId, $keyName, $returnBoolean = false)
        {
            assert('is_int($portletId)');
\            assert('is_string($portletId)');
            assert('is_string($keyName)');
            $moduleName = static::getModuleName();
            $keyName = static::resolveKeyNameByPortletId($portletId, $keyName);
            $value = ZurmoConfigurationUtil::getForCurrentUserByModuleName($moduleName, $keyName);
            $value = ($returnBoolean)? (boolean) $value: $value;
            return $value;
        }

        protected static function resolveKeyNameByPortletId($portletId, $keyName)
        {
            assert('is_int($portletId)');
            assert('is_string($keyName)');
            return $portletId . '_' . $keyName;
        }

        protected static function getModuleName()
        {
            throw new NotSupportedException();
        }
    }
?>
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

    class A extends RedBeanModel
    {
        private static $testingIssetAndEmpty;

        public static function getByName($name)
        {
            assert('is_string($name)');
            assert('$name != ""');
            $bean = R::findOne('a', "name = :name ", array(':name' => $name));
            assert('$bean === false || $bean instanceof RedBean_OODBBean');
            if ($bean === false)
            {
                throw new NotFoundException();
            }
            return self::makeModel($bean);
        }

        public static function canSaveMetadata()
        {
            return true;
        }

        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    'a',
                    'junk',
                    'uniqueRequiredEmail',
                    'name',
                ),
                'rules' => array(
                    array('a',                   'required'),
                    array('a',                   'boolean'),
                    array('a',                   'default', 'value' => 1),
                    array('uniqueRequiredEmail', 'email'),
                    array('uniqueRequiredEmail', 'required', 'on'   => 'Tuesday'),
                    array('uniqueRequiredEmail', 'unique'),
                    array('name',                'type',     'type' => 'string'),
                ),
            );
            return $metadata;
        }

        public static function isTypeDeletable()
        {
            return true;
        }

        public static function getModuleClassName()
        {
            return 'TestModule';
        }

        public static function setIssetAndEmptyAsEmpty()
        {
            self::$testingIssetAndEmpty[get_called_class()] = array();
        }

        public static function setIssetAndEmptyWithString()
        {
            self::$testingIssetAndEmpty[get_called_class()] = 'string';
        }

        public static function setIssetAndEmptyWithNull()
        {
            unset(self::$testingIssetAndEmpty[get_called_class()]);
        }

        public static function isPrivateStaticIsset()
        {
            if(!isset(self::$testingIssetAndEmpty[get_called_class()]))
            {
                return false;
            }
            return true;
        }

        /**
         * Returns the display name for the model class.
         * @param null | string $language
         * @return dynamic label name based on module.
         */
        protected static function getLabel($language = null)
        {
            return 'A';
        }

        /**
         * Returns the display name for plural of the model class.
         * @param null | string $language
         * @return dynamic label name based on module.
         */
        protected static function getPluralLabel($language = null)
        {
            return 'As';
        }
    }
?>

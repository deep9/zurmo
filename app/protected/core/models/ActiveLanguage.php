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

    class ActiveLanguage extends RedBeanModel
    {
        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    'code',
                    'name',
                    'nativeName',
                    'activationDatetime',
                    'lastUpdateDatetime'
                ),
                'rules' => array(
                    array('code', 'required'),
                    array('code', 'type', 'type' => 'string'),
                    array('code', 'length',  'min'  => 2, 'max' => 16),
                    array('name', 'required'),
                    array('name', 'type', 'type' => 'string'),
                    array('name', 'length',  'min'  => 2, 'max' => 64),
                    array('nativeName', 'required'),
                    array('nativeName', 'type', 'type' => 'string'),
                    array('nativeName', 'length',  'min'  => 2, 'max' => 64),
                    array('activationDatetime', 'required'),
                    array('activationDatetime', 'type', 'type' => 'datetime'),
                    array('lastUpdateDatetime', 'type', 'type' => 'datetime'),
                )
            );
            return $metadata;
        }

        public static function getByCode($languageCode, $modelClassName = null)
        {
            assert('!empty($languageCode)');
            assert('$modelClassName === null || is_string($modelClassName) && $modelClassName != ""');
            if ($modelClassName === null)
            {
                $modelClassName = get_called_class();
            }
            $tableName = self::getTableName($modelClassName);
            $bean = R::findOne(
                $tableName,
                ' code = :code',
                array(
                    ':code' => $languageCode
                )
            );
            assert('$bean === false || $bean instanceof RedBean_OODBBean');
            if (!is_object($bean))
            {
                throw new NotFoundException();
            }
            return self::makeModel($bean, $modelClassName);
        }

        public static function getSourceLanguageModel()
        {
            $model = new self();
            $model->code = 'en';
            $model->name = 'English';
            $model->nativeName = 'English';

            return $model;
        }
    }
?>

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
     * Model for storing sender information about an email message.  Stores specific fromAddress and fromName
     * in case there is no specific 'person' the email is coming from.  Also in case that 'person' changes their
     * information, the integrity of what actual email address/name was used stays intact.
     */
    class EmailMessageSender extends OwnedModel
    {
        public function __toString()
        {
            if (trim($this->fromAddress) == '')
            {
                return Zurmo::t('EmailMessagesModule', '(Unnamed)');
            }
            return $this->fromAddress;
        }

        public static function getModuleClassName()
        {
            return 'EmailMessagesModule';
        }

        public static function canSaveMetadata()
        {
            return false;
        }

        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    'fromAddress',
                    'fromName',
                ),
                'relations' => array(
                    'personOrAccount'      => array(RedBeanModel::HAS_ONE, 'Item',    RedBeanModel::NOT_OWNED,
                                                    RedBeanModel::LINK_TYPE_SPECIFIC, 'personOrAccount')
                ),
                'rules' => array(
                    array('fromAddress', 'required'),
                    array('fromAddress', 'email'),
                    array('fromName',    'type',    'type' => 'string'),
                    array('fromName',    'length',  'max' => 64),
                ),
            );
            return $metadata;
        }

        public static function isTypeDeletable()
        {
            return true;
        }

        /**
         * Returns the display name for the model class.
         * @param null | string $language
         * @return dynamic label name based on module.
         */
        protected static function getLabel($language = null)
        {
            return Zurmo::t('EmailMessagesModule', 'Email Sender', array(), null, $language);
        }

        /**
         * Returns the display name for plural of the model class.
         * @param null | string $language
         * @return dynamic label name based on module.
         */
        protected static function getPluralLabel($language = null)
        {
            return Zurmo::t('EmailMessagesModule', 'Email Senders', array(), null, $language);
        }

        protected static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return array_merge(parent::translatedAttributeLabels($language),
                array(
                    'fromAddress'     => Zurmo::t('EmailMessagesModule', 'From Address',  array(), null, $language),
                    'fromName'        => Zurmo::t('EmailMessagesModule', 'From Name',  array(), null, $language),
                    'personOrAccount' => Zurmo::t('ZurmoModule',         'Person Or AccountsModuleSingularLabel',  $params, null, $language)
                )
            );
        }
    }
?>
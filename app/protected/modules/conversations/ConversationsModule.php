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

    class ConversationsModule extends SecurableModule
    {
        const RIGHT_CREATE_CONVERSATIONS = 'Create Conversations';
        const RIGHT_DELETE_CONVERSATIONS = 'Delete Conversations';
        const RIGHT_ACCESS_CONVERSATIONS = 'Access Conversations Tab';

        public function getDependencies()
        {
            return array(
                'configuration',
                'zurmo',
            );
        }

        public static function getTranslatedRightsLabels()
        {
            $labels                                   = array();
            $labels[self::RIGHT_CREATE_CONVERSATIONS] = Zurmo::t('ContactsModule', 'Create Conversations');
            $labels[self::RIGHT_DELETE_CONVERSATIONS] = Zurmo::t('ContactsModule', 'Delete Conversations');
            $labels[self::RIGHT_ACCESS_CONVERSATIONS] = Zurmo::t('ContactsModule', 'Access Conversations Tab');
            return $labels;
        }

        public function getRootModelNames()
        {
            return array('Conversation', 'ConversationParticipant');
        }

        public static function getDefaultMetadata()
        {
            $metadata = array();
            $metadata['global'] = array(
                'globalSearchAttributeNames' => array(),
                'shortcutsCreateMenuItems' => array(
                    array(
                        'label' => "eval:Zurmo::t('ConversationsModule', 'Conversation')",
                        'url'   => array('/conversations/default/create'),
                        'right' => self::RIGHT_CREATE_CONVERSATIONS,
                    ),
                )
            );
            return $metadata;
        }

        public static function getPrimaryModelName()
        {
            return 'Conversation';
        }

        public static function getAccessRight()
        {
            return self::RIGHT_ACCESS_CONVERSATIONS;
        }

        public static function getCreateRight()
        {
            return self::RIGHT_CREATE_CONVERSATIONS;
        }

        public static function getDeleteRight()
        {
            return self::RIGHT_DELETE_CONVERSATIONS;
        }

        public static function getDemoDataMakerClassName()
        {
            return 'ConversationsDemoDataMaker';
        }

        public static function hasPermissions()
        {
            return true;
        }
    }
?>

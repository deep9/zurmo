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
     * Form to work with dynamic triggered model users for an email message recipient
     */
    class DynamicTriggeredModelUserWorkflowEmailMessageRecipientForm extends WorkflowEmailMessageRecipientForm
    {
        const DYNAMIC_USER_TYPE_CREATED_BY_USER             = 'CreatedByUser';

        const DYNAMIC_USER_TYPE_MANAGER_OF_CREATED_BY_USER  = 'ManagerOfCreatedByUser';

        const DYNAMIC_USER_TYPE_MODIFIED_BY_USER            = 'ModifiedByUser';

        const DYNAMIC_USER_TYPE_MANAGER_OF_MODIFIED_BY_USER = 'ManagerOfModifiedByUser';

        const DYNAMIC_USER_TYPE_OWNER                       = 'Owner';

        const DYNAMIC_USER_TYPE_MANAGER_OF_OWNER            = 'ManagerOfOwner';

        /**
         * @var string
         */
        public $dynamicUserType;

        /**
         * @return string
         */
        public static function getTypeLabel()
        {
            return Zurmo::t('WorkflowsModule', 'A person associated with the triggered record');
        }

        /**
         * @return array
         */
        public function rules()
        {
            return array_merge(parent::rules(), array(
                      array('dynamicUserType',  'type', 'type' =>  'string'),
                      array('dynamicUserType',  'required')));
        }

        /**
         * @return array
         */
        public function getDynamicUserTypesAndLabels()
        {
            $data = array();
            $data[self::DYNAMIC_USER_TYPE_CREATED_BY_USER]             =
                Zurmo::t('WorkflowsModule', 'User who created record');
            $data[self::DYNAMIC_USER_TYPE_MANAGER_OF_CREATED_BY_USER]  =
                Zurmo::t('WorkflowsModule', 'User\'s manager who created record');
            $data[self::DYNAMIC_USER_TYPE_MODIFIED_BY_USER]            =
                Zurmo::t('WorkflowsModule', 'User who last modified record');
            $data[self::DYNAMIC_USER_TYPE_MANAGER_OF_MODIFIED_BY_USER] =
                Zurmo::t('WorkflowsModule', 'User\'s manager who last modified record');
            if(is_subclass_of($this->resolveModelClassName(), 'OwnedSecurableItem'))
            {
                $data[self::DYNAMIC_USER_TYPE_OWNER]                       =
                    Zurmo::t('WorkflowsModule', 'User who owns the record');
                $data[self::DYNAMIC_USER_TYPE_MANAGER_OF_OWNER]            =
                    Zurmo::t('WorkflowsModule', 'User\'s manager who owns the record');
            }
            return $data;
        }

        /**
         * @param RedBeanModel $model
         * @param User $triggeredByUser
         * @return array
         * @throws NotSupportedException
         */
        public function makeRecipients(RedBeanModel $model, User $triggeredByUser)
        {
            if($this->dynamicUserType == self::DYNAMIC_USER_TYPE_CREATED_BY_USER)
            {
                $user = $model->createdByUser;
            }
            elseif($this->dynamicUserType == self::DYNAMIC_USER_TYPE_MANAGER_OF_CREATED_BY_USER)
            {
                if($model->createdByUser->manager->id < 0)
                {
                    return array();
                }
                $user = $model->createdByUser->manager;
            }
            elseif($this->dynamicUserType == self::DYNAMIC_USER_TYPE_MODIFIED_BY_USER)
            {
                $user = $model->modifiedByUser;
            }
            elseif($this->dynamicUserType == self::DYNAMIC_USER_TYPE_MANAGER_OF_MODIFIED_BY_USER)
            {
                if($model->modifiedByUser->manager->id < 0)
                {
                    return array();
                }
                $user = $model->modifiedByUser->manager;
            }
            elseif($this->dynamicUserType == self::DYNAMIC_USER_TYPE_OWNER)
            {
                if(!is_subclass_of(get_class($model), 'OwnedSecurableItem'))
                {
                    return array();
                }
                $user = $model->owner;
            }
            elseif($this->dynamicUserType == self::DYNAMIC_USER_TYPE_MANAGER_OF_OWNER)
            {
                if(!is_subclass_of(get_class($model), 'OwnedSecurableItem') || $model->owner->manager->id < 0)
                {
                    return array();
                }
                $user = $model->owner->manager;
            }
            else
            {
                throw new NotSupportedException();
            }
            $recipients = array();
            if ($user->primaryEmail->emailAddress !== null)
            {
                $recipient                  = new EmailMessageRecipient();
                $recipient->toAddress       = $user->primaryEmail->emailAddress;
                $recipient->toName          = strval($user);
                $recipient->type            = $this->audienceType;
                $recipient->personOrAccount = $user;
                $recipients[]               = $recipient;
            }
            return $recipients;
        }

        /**
         * @return string
         */
        protected function resolveModelClassName()
        {
            return $this->modelClassName;
        }
    }
?>
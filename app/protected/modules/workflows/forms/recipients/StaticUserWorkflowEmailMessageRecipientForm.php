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
     * Form to work with a specific user for an email message recipient
     */
    class StaticUserWorkflowEmailMessageRecipientForm extends WorkflowEmailMessageRecipientForm
    {
        /**
         * Protected so we can attach logic to it on set.
         * @var string
         */
        protected $userId;

        /**
         * @return string
         */
        public static function getTypeLabel()
        {
            return Zurmo::t('WorkflowsModule', 'A specific user');
        }

        /**
         * @param $value
         */
        public function setUserId($value)
        {
            $this->userId = $value;
            $this->stringifiedModelForValue = null;
        }

        /**
         * @return string
         */
        public function getUserId()
        {
            return $this->userId;
        }

        /**
         * @return string
         */
        public function getStringifiedModelForValue()
        {
            if($this->stringifiedModelForValue != null)
            {
                return $this->stringifiedModelForValue;
            }
            if($this->userId != null)
            {
                $user = User::getById((int)$this->userId);
                $this->stringifiedModelForValue = strval($user);
                return $this->stringifiedModelForValue;
            }
        }

        /**
         * @return array
         */
        public function rules()
        {
            return array_merge(parent::rules(), array(
                      array('userId',  'type', 'type' =>  'integer'),
                      array('userId',  'required')));
        }

        /**
         * @param RedBeanModel $model
         * @param User $triggeredByUser
         * @return array
         */
        public function makeRecipients(RedBeanModel $model, User $triggeredByUser)
        {
            try
            {
                $user = User::getById((int)$this->userId);
            }
            catch(NotFoundException $e)
            {
                return array();
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
    }
?>
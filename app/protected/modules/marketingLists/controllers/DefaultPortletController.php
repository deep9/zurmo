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

    class MarketingListsDefaultPortletController extends ZurmoPortletController
    {
        public function actionDelete($id)
        {
            $member = MarketingListMember::GetById(intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserDeleteModel($member);
            $member->delete();
        }

        public function actionToggleUnsubscribed($id)
        {
            $member = MarketingListMember::GetById(intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserDeleteModel($member);
            $member->unsubscribed = (bool)(!$member->unsubscribed);
            if (!$member->unrestrictedSave())
            {
                throw new FailedToSaveModelException();
            }
        }

        public function actionCountMembers($marketingListId)
        {
            $countArray = array(
                            'subscriberCount' => MarketingListMember::getCountByMarketingListIdAndUnsubscribed($marketingListId, false),
                            'unsubscriberCount' => MarketingListMember::getCountByMarketingListIdAndUnsubscribed($marketingListId, true)
                                );
            echo CJSON::encode($countArray);

        }

        public function actionSubscribeContacts($marketingListId, $id, $type)
        {
            assert('is_int($id)');
            assert('$type === "contact" || $type === "report"');
            if (!in_array($type, array('contact', 'report')))
            {
                throw new NotSupportedException();
            }
            $contactIds =  ($type === 'contact') ? array($id) : SavedReport::getContactIdsByReportId($id);
            $subscriberInformation = $this->addNewSubscribers($marketingListId, $contactIds);
            $message = Zurmo::t('MarketingListsModule', '{subscribedCount} subscribed.',
                                                array('{subscribedCount}' => $subscriberInformation['subscribedCount']));
            if (array_key_exists('skippedCount', $subscriberInformation) && $subscriberInformation['skippedCount'])
            {
                $message .= ' ' . Zurmo::t('MarketingListsModule', '{skippedCount} skipped.',
                                                        array('{skippedCount}' => $subscriberInformation['skippedCount']));
            }
            echo CJSON::encode(array('message' => $message, 'type' => 'message'));
        }

        protected function addNewSubscribers($marketingListId, $contactIds)
        {
            $subscriberInformation = array('subscribedCount' => 0, 'skippedCount' => 0);
            foreach ($contactIds as $contactId)
            {
                if (MarketingListMember::memberAlreadyExists($marketingListId, $contactId))
                {
                    $subscriberInformation['skippedCount']++;
                }
                else
                {
                    MarketingListMember::addNewMember($marketingListId, $contactId, false);
                    $subscriberInformation['subscribedCount']++;
                }
            }
            return $subscriberInformation;
        }
    }
?>

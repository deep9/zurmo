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
     * A job for removing old import models after the imports are complete.
     */
    class ImportCleanupJob extends BaseJob
    {
        protected static $pageSize = 200;

        /**
         * @returns Translated label that describes this job type.
         */
        public static function getDisplayName()
        {
           return Zurmo::t('ImportModule', 'Import Cleanup Job');
        }

        /**
         * @return The type of the NotificationRules
         */
        public static function getType()
        {
            return 'ImportCleanup';
        }

        public static function getRecommendedRunFrequencyContent()
        {
            return Zurmo::t('ImportModule', 'Once a week, early in the morning.');
        }

        /**
         * Get all imports where the modifiedDateTime was more than 1 week ago.  Then
         * delete the imports.
         * (non-PHPdoc)
         * @see BaseJob::run()
         */
        public function run()
        {
            $oneWeekAgoTimeStamp = DateTimeUtil::convertTimestampToDbFormatDateTime(time() - 60 * 60 *24 * 7);
            $searchAttributeData = array();
            $searchAttributeData['clauses'] = array(
                1 => array(
                    'attributeName'        => 'modifiedDateTime',
                    'operatorType'         => 'lessThan',
                    'value'                => $oneWeekAgoTimeStamp,
                ),
            );
            $searchAttributeData['structure'] = '1';
            $joinTablesAdapter = new RedBeanModelJoinTablesQueryAdapter('Import');
            $where = RedBeanModelDataProvider::makeWhere('Import', $searchAttributeData, $joinTablesAdapter);
            $importModels = Import::getSubset($joinTablesAdapter, null, self::$pageSize, $where, null);
            foreach ($importModels as $import)
            {
                $import->delete();
            }
            return true;
        }
    }
?>
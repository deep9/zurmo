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
     * Form to help manage the mashable inbox
     */
    class MashableInboxForm extends CFormModel
    {
        /**
         * Value to be used to signal that the filtering is for all models and not a specific one.
         * @var string
         */
        const  FILTERED_BY_ALL = 'all';

        const  FILTERED_BY_UNREAD = 'unread';

        public $searchTerm;

        public $filteredBy = self::FILTERED_BY_ALL;

        public $optionForModel;

        public $selectedIds;

        public $massAction;

        /**
         * Models that implement the CombinedInboxInterface and the current user has
         * rights to see, this array contains the model class names as the indexes and the translated model labels
         * as the values.
         * @var array
         */
        public $mashableModelClassNamesAndDisplayLabels;


        public function rules()
        {
            return array(
                array('searchTerm',          'type',    'type' => 'string'),
                array('filteredBy',          'type',    'type' => 'string'),
                array('optionForModel',      'type',    'type' => 'string'),
                array('selectedIds',         'type',    'type' => 'string'),
                array('massAction',          'type',    'type' => 'string'),
            );
        }
    }
?>
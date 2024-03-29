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
     * Search form for filtering a list of ByTimeWorkflowInQueue models in a list
     */
    class ByTimeWorkflowInQueuesSearchForm extends SearchForm
    {
        /**
         * @var string
         */
        public $workflowName;

        /**
         * @return string
         */
        protected static function getRedBeanModelClassName()
        {
            return 'ByTimeWorkflowInQueue';
        }

        /**
         * @return array
         */
        public function rules()
        {
            return array_merge(parent::rules(), array(
                array('workflowName', 'safe'),
            ));
        }

        /**
         * @return array
         */
        public function attributeLabels()
        {
            return array_merge(parent::attributeLabels(), array(
                'workflowName' => Zurmo::t('WorkflowsModule', 'Workflow Name'),
            ));
        }

        /**
         * @return array
         */
        public function getAttributesMappedToRealAttributesMetadata()
        {
            return array_merge(parent::getAttributesMappedToRealAttributesMetadata(), array(
                'workflowName' => array(
                    array('savedWorkflow',  'name'),
                ),
            ));
        }

        /**
         * Override since the module globalSearchAttributeNames are for SavedWorkflow not the ByTimeWorkflowInQueue
         * models.
         * @param unknown_type $realAttributesMetadata
         */
        public function resolveMixedSearchAttributeMappedToRealAttributesMetadata(& $realAttributesMetadata)
        {
            assert('is_array($realAttributesMetadata)');
            $data = array();
            $data['anyMixedAttributes'][] = array('savedWorkflow', 'name');
            $realAttributesMetadata = array_merge($realAttributesMetadata, $data);
        }

        /**
         * @return array of attributeName and label pairings.  Based on what attributes are used
         * in a mixed attribute search.
         */
        public function getGlobalSearchAttributeNamesAndLabelsAndAll()
        {
            $namesAndLabels = array();
            $namesAndLabels['workflowName'] = $this->getAttributeLabel('workflowName');
            return array_merge(array('All' => Zurmo::t('Core', 'All')), $namesAndLabels);
        }
    }
?>
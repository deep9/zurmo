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
     * Class used by workflow to show available action attribute types in a dropdown.
     */
    class WorkflowActionAttributeTypeStaticDropDownElement extends StaticDropDownFormElement
    {
        /**
         * @return string
         */
        public function getIdForSelectInput()
        {
            return $this->getEditableInputId($this->attribute);
        }

        /**
         * @return array
         * @throws NotSupportedException
         */
        public function getDropDownArray()
        {
            if (isset($this->params['typeValuesAndLabels']))
            {
                return $this->params['typeValuesAndLabels'];
            }
            throw new NotSupportedException();
        }

        /**
         * @return array
         */
        protected function getEditableHtmlOptions()
        {
            $htmlOptions = parent::getEditableHtmlOptions();
            if(isset($htmlOptions['class']))
            {
                $htmlOptions['class'] .= ' actionAttributeType';
            }
            else
            {
                $htmlOptions['class']  = 'actionAttributeType';
            }
            return $htmlOptions;
        }

        /**
         * @return string
         */
        protected function renderControlEditable()
        {
            $content = parent::renderControlEditable();
            $this->renderChangeScript();
            return $content;
        }

        protected function renderChangeScript()
        {

        }

        public static function getValueTypesRequiringFirstInput()
        {
            return array(WorkflowActionAttributeForm::TYPE_STATIC);
        }

        /**
         * @return array
         */
        public static function getValueTypesRequiringSecondInput()
        {
            return array(
                DateTimeWorkflowActionAttributeForm::TYPE_DYNAMIC_FROM_TRIGGERED_DATETIME,
                DateTimeWorkflowActionAttributeForm::TYPE_DYNAMIC_FROM_EXISTING_DATETIME,
                DateWorkflowActionAttributeForm::TYPE_DYNAMIC_FROM_TRIGGERED_DATE,
                DateWorkflowActionAttributeForm::TYPE_DYNAMIC_FROM_EXISTING_DATE,
                DropDownWorkflowActionAttributeForm::TYPE_DYNAMIC_STEP_FORWARD_OR_BACKWARDS,
            );
        }
    }
?>
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
     * Adds specific input id/name/value handling for wizard-based modules filter usage. Reporting and workflow both
     * extend this element
     */
    abstract class MixedDateTypesForWizardElement extends MixedDateTypesElement
    {
        public $editableTemplate = '<th>{label}</th><td colspan="{colspan}">{valueType}{content}{error}</td>';

        public $nonEditableTemplate = '<th>{label}</th><td colspan="{colspan}">{valueType}{content}</td>';

        protected function renderEditable()
        {
            $data = array();
            $data['label']     = $this->renderLabel();
            $data['valueType'] = $this->renderEditableValueTypeContent();
            $data['content']   = $this->renderControlEditable();
            $data['error']     = $this->renderError();
            $data['colspan']   = $this->getColumnSpan();
            return $this->resolveContentTemplate($this->editableTemplate, $data);
        }

        protected function renderEditableValueTypeContent()
        {
            $content = parent::renderEditableValueTypeContent();
            $error   = $this->form->error($this->model, 'valueType',
                                          array('inputID' => $this->getValueTypeEditableInputId()));
            return $content . $error;
        }

        protected function renderEditableFirstDateContent($disabled = null)
        {
            $content = parent::renderEditableFirstDateContent($disabled);
            $error   = $this->form->error($this->model, 'value',
                                          array('inputID' => $this->getValueFirstDateEditableInputId()));
            return $content . $error;
        }

        protected function renderEditableSecondDateContent($disabled = null)
        {
            $content = parent::renderEditableSecondDateContent($disabled);
            $error   = $this->form->error($this->model, 'secondValue',
                                          array('inputID' => $this->getValueSecondDateEditableInputId()));
            return $content . $error;
        }

        protected function getValueTypeEditableInputId()
        {
            return $this->getEditableInputId('valueType');
        }

        protected function getValueFirstDateEditableInputId()
        {
            return $this->getEditableInputId('value');
        }

        protected function getValueSecondDateEditableInputId()
        {
            return $this->getEditableInputId('secondValue');
        }

        protected function getValueTypeEditableInputName()
        {
            return $this->getEditableInputName('valueType');
        }

        protected function getValueFirstDateEditableInputName()
        {
            return $this->getEditableInputName('value');
        }

        protected function getValueSecondDateEditableInputName()
        {
            return $this->getEditableInputName('secondValue');
        }

        protected function getValueFirstDate()
        {
            return ArrayUtil::getArrayValue($this->model, 'value');
        }

        protected function getValueSecondDate()
        {
            return ArrayUtil::getArrayValue($this->model, 'secondValue');
        }

        protected function getValueType()
        {
            return ArrayUtil::getArrayValue($this->model, 'valueType');
        }
    }
?>
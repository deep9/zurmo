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

    class MashableInboxStatusRadioElement extends Element
    {

        protected function renderControlEditable()
        {
            $content = $this->form->radioButtonList(
                $this->model,
                $this->attribute,
                $this->getArray(),
                $this->getEditableHtmlOptions()
            );
            return ZurmoHtml::tag('strong', array(), Zurmo::t('MashableInboxModule', 'Status') . ':' ) . $content;
        }

        protected function renderControlNonEditable()
        {
            throw new NotImplementedException();
        }

        public function getEditableHtmlOptions()
        {
            $htmlOptions = array(
                'name'      => $this->getEditableInputName(),
                'id'        => $this->getEditableInputId(),
                'separator' => '',
                'template'  => '{input}{label}',
            );
            return $htmlOptions;
        }

        private function getArray()
        {
            $getArray = array(
                MashableInboxForm::FILTERED_BY_ALL    => Zurmo::t('MashableInboxModuel', 'All'),
                MashableInboxForm::FILTERED_BY_UNREAD => Zurmo::t('MashableInboxModuel', 'Unread'),
            );
            return $getArray;
        }
    }
?>
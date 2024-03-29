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
     * Helper class for adapting one of an email message's recipients to a set of appropriate Elements
     */
    class WorkflowEmailMessageRecipientToElementAdapter
    {
        /**
         * @var int
         */
        protected $emailMessageRecipientType;

        /**
         * @var WorkflowEmailMessageRecipientForm
         */
        protected $model;

        /**
         * @var WizardActiveForm
         */
        protected $form;

        /**
         * @var array
         */
        protected $inputPrefixData;

        /**
         * @param WorkflowEmailMessageRecipientForm $model
         * @param WizardActiveForm $form
         * @param integer $emailMessageRecipientType
         * @param array $inputPrefixData
         */
        public function __construct(WorkflowEmailMessageRecipientForm $model, WizardActiveForm $form,
                                    $emailMessageRecipientType, $inputPrefixData)
        {

            assert('is_string($emailMessageRecipientType)');
            assert('is_array($inputPrefixData)');
            $this->model                     = $model;
            $this->form                      = $form;
            $this->emailMessageRecipientType = $emailMessageRecipientType;
            $this->inputPrefixData           = $inputPrefixData;
        }

        /**
         * @return string
         */
        public function getContent()
        {
            $this->form->setInputPrefixData($this->inputPrefixData);
            $content = $this->getRecipientContent();
            $this->form->clearInputPrefixData();
            return $content;
        }

        /**
         * @return string
         */
        protected function getRecipientContent()
        {
            $content                             = null;
            ZurmoHtml::resolveDivWrapperForContent($this->model->getTypeLabel(),
                                                   $content, 'dynamic-row-label email-message-recipient-label');
            $content                            .= $this->renderTypeContent();
            $content                            .= $this->renderAudienceTypeContent();
            $content                            .= $this->renderFormAttributesContent();
            return $content;
        }

        protected function renderTypeContent()
        {
            $name        = Element::resolveInputNamePrefixIntoString($this->inputPrefixData) . '[type]';
            $id          = Element::resolveInputIdPrefixIntoString($this->inputPrefixData) . 'type';
            $htmlOptions = array('id' => $id);
            return ZurmoHtml::hiddenField($name, $this->emailMessageRecipientType, $htmlOptions);
        }

        protected function renderAudienceTypeContent()
        {
            $params                 = array('inputPrefix' => $this->inputPrefixData);
            $audienceTypeElement    = new EmailMessageRecipientTypesStaticDropDownElement(
                                          $this->model, 'audienceType', $this->form, $params);
            $audienceTypeElement->editableTemplate  = '{content}{error}';
            return $audienceTypeElement->render();
        }

        protected function renderFormAttributesContent()
        {
            $formType = $this->model->getFormType();
            $params   = array('inputPrefix' => $this->inputPrefixData);
            $content  = null;
            if($formType == 'DynamicTriggeredModelUser')
            {
                $dynamicUserTypeElement   = new DynamicUserTypeForEmailMessageRecipientStaticDropDownElement(
                                            $this->model, 'dynamicUserType', $this->form, $params);
                $dynamicUserTypeElement->editableTemplate    = '<div class="value-data">{content}{error}</div>';
                $content .= $dynamicUserTypeElement ->render();
            }
            elseif($formType == 'DynamicTriggeredModelRelationUser')
            {
                $relationElement        = new ModelRelationForEmailMessageRecipientStaticDropDownElement(
                                          $this->model, 'relation', $this->form, $params);
                $relationElement->editableTemplate    = '<div class="value-data">{content}{error}</div>';
                $dynamicUserTypeElement = new DynamicUserTypeForEmailMessageRecipientStaticDropDownElement(
                                          $this->model, 'dynamicUserType', $this->form, $params);
                $dynamicUserTypeElement->editableTemplate    = '<div class="value-data">{content}{error}</div>';
                $allRelatedDropdowns    = Zurmo::t('WorkflowsModule', '<span>For all related</span> {relationsDropDown}',
                                        array('{relationsDropDown}' => $relationElement->render()));
                $allRelatedDropdowns   .= $dynamicUserTypeElement ->render();
                $content .= ZurmoHtml::tag('div', array('class' => 'all-related-field'), $allRelatedDropdowns);
            }
            elseif($formType == 'DynamictriggeredByUser')
            {
                //nothing to render
            }
            elseif($formType == 'StaticAddress')
            {
                $toNameElement                      = new TextElement($this->model, 'toName', $this->form, $params);
                $toNameElement->editableTemplate    = '<div class="value-data"><span>{label}</span>{content}{error}</div>';
                $toAddressElement                   = new TextElement($this->model, 'toAddress', $this->form, $params);
                $toAddressElement->editableTemplate = '<div class="value-data"><span>{label}</span>{content}{error}</div>';
                $toNameAndAddressElements  = null;
                $toNameAndAddressElements .= $toNameElement->render();
                $toNameAndAddressElements .= $toAddressElement->render();
                $content .= ZurmoHtml::tag('div', array('class' => 'static-address-field'), $toNameAndAddressElements);

            }
            elseif($formType == 'StaticGroup')
            {
                $staticGroupElement = new AllGroupsStaticDropDownElement($this->model, 'groupId', $this->form, $params);
                $staticGroupElement->editableTemplate = '<div class="value-data">{content}{error}</div>';
                $content .= $staticGroupElement->render();
            }
            elseif($formType == 'StaticRole')
            {
                $staticRoleElement = new AllRolesStaticDropDownElement($this->model, 'roleId', $this->form, $params);
                $staticRoleElement->editableTemplate = '<div class="value-data">{content}{error}</div>';
                $content .= $staticRoleElement->render();
            }
            elseif($formType == 'StaticUser')
            {
                $staticUserElement = new UserNameIdElement($this->model, 'userId', $this->form, $params);
                $staticUserElement->setIdAttributeId('userId');
                $staticUserElement->setNameAttributeName('stringifiedModelForValue');
                $staticUserElement->editableTemplate = '<div class="value-data">{content}{error}</div>';
                $content .= $staticUserElement->render();
            }
            else
            {
                throw new NotSupportedException();
            }
            return $content;
        }
    }
?>
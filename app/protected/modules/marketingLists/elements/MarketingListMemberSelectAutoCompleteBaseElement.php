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

    abstract class MarketingListMemberSelectAutoCompleteBaseElement extends AutoCompleteTextElement
    {
        const DISABLE_TEXT_BOX_WHEN_AJAX_IN_PROGRESS        = true;

        const DISABLE_RADIO_BUTTON_WHEN_AJAX_IN_PROGRESS    = true;

        const NOTIFICATION_BAR_ID                           = 'FlashMessageBar';

        public $editableTemplate = '<td colspan="{colspan}">{content}</td>';

        abstract protected function getSelectType();

        protected function getSubscribeUrl()
        {
            return Yii::app()->createUrl('/' . Yii::app()->getController()->getModule()->getId() . '/' .
                    'defaultPortlet/subscribeContacts/');
        }

        protected function getWidgetValue()
        {
            return null;
        }

        protected function getHtmlOptions()
        {
            CMap::mergeArray(parent::getHtmlOptions(), array('onfocus' => '$(this).val("");'));
        }

        /**
         * (non-PHPdoc)
         * @see TextElement::renderControlNonEditable()
         */
        protected function renderControlNonEditable()
        {
            throw new NotSupportedException();
        }

        protected function renderControlEditable()
        {
            $this->registerScripts();
            return parent::renderControlEditable();
        }

        protected function getOptions()
        {
            return array(
                'autoFill' => false,
                'select'    => $this->getWidgetSelectActionJS(),
            );
        }

        protected function getWidgetSelectActionJS()
        {
            return 'js: function(event, ui) {
                            var searchBox           = $(this);
                            var listGridViewId      = "' . $this->getListViewGridId() .'";
                            var notificationBarId   = "' . static::NOTIFICATION_BAR_ID . '";
                            var radioButtonClass    = "' . $this->getRadioButtonClass() . '";
                            var url                 = "' . $this->getSubscribeUrl() . '";
                            var modelId             = "' . $this->getModelId() . '";
                            var selectType          = "' . $this->getSelectType() . '";
                            var disableTextBox      = "' . static::DISABLE_TEXT_BOX_WHEN_AJAX_IN_PROGRESS . '";
                            var disableRadioButton  = "' . static::DISABLE_RADIO_BUTTON_WHEN_AJAX_IN_PROGRESS . '";
                            $.ajax(
                                {
                                    url:        url,
                                    dataType:   "json",
                                    data:       { marketingListId: modelId, id: ui.item.id, type: selectType },
                                    beforeSend: function(request, settings) {
                                                    makeSmallLoadingSpinner(listGridViewId);
                                                    $("#" + listGridViewId).addClass("loading");
                                                    if (disableTextBox == true)
                                                    {
                                                        $(searchBox).attr("disabled", "disabled");
                                                    }
                                                    if (disableRadioButton == true)
                                                    {
                                                        $("." + radioButtonClass).attr("disabled", "disabled");
                                                    }
                                                },
                                    success:    function(data, status, request) {
                                                    $("#" + listGridViewId).find(".pager").find(".refresh").find("a").click();
                                                    updateFlashBar(data, notificationBarId);
                                                },
                                    error:      function(request, status, error) {
                                                    var data = {' . // Not Coding Standard
                                                                '   "message" : "' . Zurmo::t('MarketingListsModule', 'There was an error processing your request'). '",
                                                                    "type"    : "error"
                                                                };
                                                    updateFlashBar(data, notificationBarId);
                                                },
                                    complete:   function(request, status) {
                                                    $(searchBox).removeAttr("disabled");
                                                    $(searchBox).val("");
                                                    $("." + radioButtonClass).removeAttr("disabled");
                                                    $("#" + listGridViewId).removeClass("loading");
                                                    event.preventDefault();
                                                }
                                }
                            );
                        }';
        }

        protected function registerScripts()
        {
            Yii::app()->clientScript->registerScript($this->getListViewGridId() . '-updateFlashBar', '
                function updateFlashBar(data, flashBarId)
                {
                    $("#" + flashBarId).jnotifyAddMessage(
                    {
                        text: data.message,
                        permanent: false,
                        showIcon: true,
                        type: data.type
                    });
                }
            ');
        }


        protected function getModelId()
        {
            $marketingListId = ArrayUtil::getArrayValue($this->params, 'marketingListId');
            if (!isset($marketingListId))
            {
                if (!isset($this->model))
                {
                    throw new NotSupportedException();
                }
                else
                {
                    $marketingListId = $this->model->id;
                }
            }
            return $marketingListId;
        }

        protected function getRadioButtonClass()
        {
            return ArrayUtil::getArrayValueWithExceptionIfNotFound($this->params, 'radioButtonClass');
        }
    }
?>
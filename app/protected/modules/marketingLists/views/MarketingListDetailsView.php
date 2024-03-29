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

    class MarketingListDetailsView extends SecuredDetailsView
    {
        public static function assertModelIsValid($model)
        {
            assert('$model instanceof MarketingList');
        }

        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type'        => 'MarketingListsDetailsLink',
                                'model'                         => 'eval:$this->model',
                                'htmlOptions'                   => array('class' => 'icon-details')),
                            array('type'        => 'MarketingListsOptionsLink',
                                'htmlOptions'                   => array('class' => 'icon-edit')),
                            array('type'        => 'MarketingListsTogglePortletsLink',
                                'htmlOptions'                   => array('class' => 'hasCheckboxes'),
                                'membersPortletClass'           => MarketingListDetailsAndRelationsView::MEMBERS_PORTLET_CLASS,
                                'autorespondersPortletClass'    => MarketingListDetailsAndRelationsView::AUTORESPONDERS_PORTLET_CLASS),
                        ),
                    ),
                ),
            );
            return $metadata;
        }

        public function getTitle()
        {
            return strval($this->model);
        }

        protected function renderContent()
        {
            // TODO: @Shoaibi/@Jason: Low: Do security walkthrough
            $actionElementBarContent        = $this->renderActionElementBar(false);
            $content                        = $this->renderTitleContent();
            $content                       .= ZurmoHtml::tag('div', array('class' => 'view-toolbar-container clearfix'),
                                                ZurmoHtml::tag('div', array('class' => 'view-toolbar'),
                                                                                    $actionElementBarContent)
                                                );
            return $content;
        }
    }
?>

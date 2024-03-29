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
     * Base view class for components that appear in the workflow wizard
     */
    abstract class ComponentForWorkflowWizardView extends ComponentForWizardModelView
    {
        /**
         * @return string
         */
        public function getTitle()
        {
            return static::getWizardStepTitle();
        }

        /**
         * Override if the view should show a previous link.
         */
        protected function renderPreviousPageLinkContent()
        {
            return ZurmoHtml::link(ZurmoHtml::tag('span', array('class' => 'z-label'),
                   Zurmo::t('WorkflowsModule', 'Previous')), '#', array('id' => static::getPreviousPageLinkId()));
        }

        /**
         * Override if the view should show a next link.
         */
        protected function renderNextPageLinkContent()
        {
            $params                = array();
            $params['label']       = Zurmo::t('WorkflowsModule', 'Next');
            $params['htmlOptions'] = array('id' => static::getNextPageLinkId(),
                                           'onclick' => 'js:$(this).addClass("attachLoadingTarget");');
            $element               = new SaveButtonActionElement(null, null, null, $params);
            return $element->render();
        }

        /**
         * @param array $items
         * @return string
         */
        protected function getNonSortableListContent(Array $items)
        {
            $content = null;
            foreach($items as $item)
            {
                $content .= ZurmoHtml::tag('li', array(), $item['content']);
            }
            return ZurmoHtml::tag('ul', array(), $content);
        }

        /**
         * @param array $items
         * @param string $componentType
         * @return string
         */
        protected function getSortableListContent(Array $items, $componentType)
        {
            //unless we refactor getTreeType to getComponentType... but that requires a bigger refactor
            $cClipWidget = new CClipWidget();
            $cClipWidget->beginClip($componentType . 'WorkflowComponentSortable');
            $cClipWidget->widget('application.core.widgets.JuiSortable', array(
                'items' => $items,
                'itemTemplate' => '<li>content</li>',
                'htmlOptions' =>
                array(
                    'id'    => $componentType . 'attributeRowsUl',
                    'class' => 'sortable',
                ),
                'options' => array(
                    'cancel' => 'li.expanded-row',
                    'placeholder' => 'ui-state-highlight',
                    'containment' => 'parent',
                    'deactivate' => 'js:function() { rebuildWorkflowActionRowNumbers("' . $componentType . 'attributeRowsUl"); }'
                ),
                'showEmptyList' => false
            ));
            $cClipWidget->endClip();
            return $cClipWidget->getController()->clips[$componentType . 'WorkflowComponentSortable'];
        }
    }
?>
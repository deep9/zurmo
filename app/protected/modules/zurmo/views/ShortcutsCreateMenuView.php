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

    class ShortcutsCreateMenuView extends View
    {
        protected $controllerId;

        protected $moduleId;

        protected $menuItems;

        protected $displayName;

        public function __construct($controllerId, $moduleId, $menuItems)
        {
            assert('is_string($controllerId)');
            assert('is_string($moduleId)');
            assert('$menuItems == null || is_array($menuItems)');
            $this->controllerId = $controllerId;
            $this->moduleId     = $moduleId;
            $this->menuItems    = $menuItems;
        }

        /**
         * Just renderContent. Do not wrap with any divs.
         */
        public function render()
        {
            return $this->renderContent();
        }

        public function renderMenu($menuId, $contentPrefix = null)
        {
            if (empty($this->menuItems))
            {
                return;
            }
            $this->qualifyMenuItems();
            $cClipWidget = new CClipWidget();
            $cClipWidget->beginClip("Shortcuts");
            $cClipWidget->widget('application.core.widgets.MinimalDynamicLabelMbMenu', array(
                'htmlOptions' => array('id' => $menuId),
                'items'       => array($this->menuItems),
            ));
            $cClipWidget->endClip();
            return $contentPrefix . $cClipWidget->getController()->clips['Shortcuts'];
        }

        protected function renderContent()
        {
            $contentPrefix      = $this->getContentPrefix();
            $menuId             = $this->getMenuId();
            return $this->renderMenu($menuId, $contentPrefix);
        }

        protected function getContentPrefix()
        {
            return null;
        }

        protected function getMenuId()
        {
            return 'ShortcutsMenu';
        }

        protected function qualifyMenuItems()
        {
            // used in subclasses for adding custom data inside menuItems
        }
    }
?>
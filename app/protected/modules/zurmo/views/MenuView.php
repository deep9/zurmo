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

    class MenuView extends View
    {
        protected $items;

        protected $useMinimalDynamicLabelMbMenu;

        public function __construct(array $items, $useMinimalDynamicLabelMbMenu = false)
        {
            $this->items = $items;
            $this->useMinimalDynamicLabelMbMenu = $useMinimalDynamicLabelMbMenu;
        }

        /**
         * Will attempt to get the menu items from cache, otherwise from the appropriate storage, and cache the information
         * for the next call to this method.
         * @see View::renderContent()
         */
        protected function renderContent()
        {
            if (count($this->items) == 0)
            {
                return null;
            }
            if ($this->useMinimalDynamicLabelMbMenu)
            {
                $widgetName = 'MinimalDynamicLabelMbMenu';
            }
            else
            {
                $widgetName = 'MbMenu';
            }
            $widgetPath = 'application.core.widgets.'.$widgetName;
            $cClipWidget = new CClipWidget();
            $cClipWidget->beginClip("Tabs");
            $cClipWidget->widget($widgetPath, array(
                'items' => static::resolveForHiddenItems($this->items)
            ));
            $cClipWidget->endClip();
            return $cClipWidget->getController()->clips['Tabs'];
        }

        protected static function resolveForHiddenItems($items)
        {
            assert('is_array($items)');
            $count = 1;
            foreach($items as $key => $item)
            {
                if($count > 6)
                {
                    $items[$key]['itemOptions']['class'] = 'hidden-nav-item';
                }
                $count ++;
            }
            return $items;
        }
    }
?>
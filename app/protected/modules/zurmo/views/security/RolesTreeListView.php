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
     * View that renders a list of roles in the form of a
     * tree or noded list view.
     */
    class RolesTreeListView extends SecurityTreeListView
    {
        protected function renderContent()
        {
            $content  = $this->renderViewToolBar(false); //why do we need it if its empty?
            $content .= '<div>';
            $content .= '<h1>' . Zurmo::t('ZurmoModule', 'Roles') . '</h1>';
            $content .= $this->renderTreeMenu('role', 'roles', Zurmo::t('ZurmoModule', 'Role'));
            $content .= '</div>';
            return $content;
        }

        protected function renderTreeListView($data)
        {
            assert('is_array($data)');
            $content  = '<table class="configuration-list">';
            $content .= '<colgroup>';
            $content .= '<col style="width:50%" />';
            $content .= '<col style="width:25%" />';
            $content .= '<col style="width:25%" />';
            $content .= '</colgroup>';
            $content .= '<tbody>';
            $content .= '<tr><th>' . Zurmo::t('ZurmoModule', 'Name') . '</th><th>' . Zurmo::t('ZurmoModule', 'Users') . '</th><th></th></tr>';
            static::renderTreeListViewNode($content, $data, 0);
            $content .= '</tbody>';
            $content .= '</table>';
            return $content;
        }

        protected static function renderTreeListViewNode(& $content, $data, $indent)
        {
            assert('is_string($content)');
            assert('is_array($data)');
            foreach ($data as $node)
            {
                $content .= '<tr>';
                $content .= '<td class="level-' . $indent . '">';
                $content .= $node['link'];
                $content .= '</td>';
                $content .= '<td>';
                $content .= static::renderUserCount($node['userCount'], $node['route']);
                $content .= '</td>';
                $content .= '<td>';
                if (isset($node['route']) && $node['route'] != null && static::shouldRenderConfigureLink())
                {
                    $content .= ZurmoHtml::link(ZurmoHtml::wrapLabel(Zurmo::t('ZurmoModule', 'Configure') ),
                        $node['route']);
                }
                $content .= '</td>';
                $content .= '</tr>';
                if (isset($node['children']))
                {
                    static::renderTreeListViewNode($content, $node['children'], $indent + 1);
                }
            }
        }

        protected static function resolveRoleIdFromRoute($route)
        {
            return substr($route, strpos($route, 'id=') + 3); // Not Coding Standard
        }

        protected static function renderUserCount($userCount, $route)
        {
            if ($userCount && static::resolveShouldShowLinkableUserCount())
            {
                $element = new UsersModalListLinkActionElement(Yii::app()->controller->id,
                    Yii::app()->controller->module->id,
                    static::resolveRoleIdFromRoute($route),
                    array('label' => $userCount, 'htmlOptions' => array('class' => 'z-link')));
                return $element->render();
            }
            else
            {
                return $userCount;
            }
        }

        protected static function resolveShouldShowLinkableUserCount()
        {
            return true;
        }
    }
?>

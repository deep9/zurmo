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
     * Zurmo specific view for list view.
     * Used to manipulate elements for a form layout
     * based on rights/permissions of the current user
     */
    abstract class SecuredListView extends ListView
    {
        /**
         * Override to handle security/access resolution on links.
         */
        protected function getCGridViewLastColumn()
        {
            return array(
                'class'           => 'ButtonColumn',
                'template'        => '{update}',
                'buttons'         => array(
                    'update'      => array(
                    'url'             => 'Yii::app()->createUrl("' .
                                         $this->getGridViewActionRoute('edit') . '", array("id" => $data->id))',
                    'imageUrl'        => false,
                    'visible'         => 'ActionSecurityUtil::canCurrentUserPerformAction("Edit", $data)',
                    'options'         => array('class' => 'pencil', 'title' => 'Update'),
                    'label'           => '!'
                    ),
                ),
            );
        }

        /**
         * Override to handle security/access resolution on links.
         */
        public function getLinkString($attributeString, $attribute)
        {
            $string  = 'ActionSecurityUtil::resolveLinkToModelForCurrentUser("' . $attributeString . '", ';
            $string .= '$data, "' . $this->getActionModuleClassName() . '", ';
            $string .= '"' . $this->getGridViewActionRoute('details') . '", (int)$offset)';
            return $string;
        }

        public function getRelatedLinkString($attributeString, $attributeName, $moduleId)
        {
            $string  = 'ActionSecurityUtil::resolveLinkToModelForCurrentUser("' . $attributeString . '", ';
            $string .= '$data->' . $attributeName. ', "' . $this->getActionModuleClassName() . '", '; // Not Coding Standard
            $string .= '"' . $this->getGridViewActionRoute('details', $moduleId) . '")';
            return $string;
        }
    }
?>
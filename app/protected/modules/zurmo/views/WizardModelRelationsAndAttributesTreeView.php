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
     * View for displaying a tree view widget for certain tree type and based on a type.
     * Extended by reporting and workflow views for example.
     */
    abstract class WizardModelRelationsAndAttributesTreeView extends View
    {
        /**
         * @var string
         */
        protected $type;

        /**
         * @var string
         */
        protected $treeType;

        /**
         * @var string
         */
        protected $formName;

        /**
         * Override in children with correct controllerId
         * @throws NotImplementedException
         */
        public static function getControllerId()
        {
            throw new NotImplementedException();
        }

        public function __construct($type, $treeType, $formName)
        {
            assert('is_string($type)');
            assert('is_string($treeType)');
            assert('is_string($formName)');
            $this->type     = $type;
            $this->treeType = $treeType;
            $this->formName = $formName;
        }

        /**
         * @return bool
         */
        public function isUniqueToAPage()
        {
            return false;
        }

        /**
         * @return string
         */
        protected function renderContent()
        {
            $content      = null;
            $cClipWidget  = new CClipWidget();
            $cClipWidget->beginClip("ZurmoTreeView");
            $cClipWidget->widget('application.core.widgets.ZurmoTreeView', array(
            'id'          => $this->getTreeId(),
            'url'         => $this->getDataUrl(),
            'options' => array('formName' => $this->formName),
            'htmlOptions' => array(
                'class'   => 'treeview-red' //todo: use different theme class.
            )));
            $cClipWidget->endClip();
            $content .= $cClipWidget->getController()->clips['ZurmoTreeView'];
            $script = 'makeLargeLoadingSpinner(true, "#' . $this->getTreeId() . '");';
            Yii::app()->getClientScript()->registerScript($this->getTreeId(), $script);
            return $content;
        }

        /**
         * @return string
         */
        protected function getDataUrl()
        {
            return  Yii::app()->createUrl(static::getControllerId() . '/default/relationsAndAttributesTree',
                        array_merge($_GET, array('type' => $this->type, 'treeType' => $this->treeType)));
        }

        /**
         * @return string
         */
        protected function getTreeId()
        {
            return $this->treeType . 'TreeView';
        }
    }
?>
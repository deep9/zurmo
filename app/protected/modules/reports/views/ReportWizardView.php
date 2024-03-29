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
     * Base class for working with the report wizard
     */
    abstract class ReportWizardView extends WizardView
    {
        public static function getControllerId()
        {
            return 'reports';
        }

        /**
         * @return string
         */
        public function getTitle()
        {
            return Zurmo::t('ReportsModule', 'Report Wizard');
        }

        /**
         * @return string
         */
        protected static function getStartingValidationScenario()
        {
            return ReportWizardForm::MODULE_VALIDATION_SCENARIO;
        }

        protected function registerScripts()
        {
            parent::registerScripts();
            Yii::app()->getClientScript()->registerCoreScript('treeview');
            Yii::app()->clientScript->registerScriptFile(
                Yii::app()->getAssetManager()->publish(
                    Yii::getPathOfAlias('application.modules.reports.views.assets')) . '/ReportUtils.js');
            $this->registerClickFlowScript();
            $this->registerModuleClassNameChangeScript();
        }

        protected function registerModuleClassNameChangeScript()
        {
            $moduleClassNameId = get_class($this->model) .  '[moduleClassName]';
            Yii::app()->clientScript->registerScript('moduleForReportChangeScript', "
                $('input:radio[name=\"" . $moduleClassNameId . "\"]').live('change', function()
                    {
                        $('#FiltersForReportWizardView').find('.dynamic-rows').find('ul:first').find('li').remove();
                        $('#FiltersTreeArea').html('');
                        $('." . FiltersForReportWizardView::getZeroComponentsClassName() . "').show();
                        rebuildReportFiltersAttributeRowNumbersAndStructureInput('FiltersForReportWizardView');
                        $('#DisplayAttributesForReportWizardView').find('.dynamic-rows').find('ul:first').find('li').remove();
                        $('#DisplayAttributesTreeArea').html('');
                        $('." . DisplayAttributesForReportWizardView::getZeroComponentsClassName() . "').show();
                        " . $this->registerModuleClassNameChangeScriptExtraPart() . "
                    }
                );
            ");
        }

        protected function registerModuleClassNameChangeScriptExtraPart()
        {
        }
    }
?>
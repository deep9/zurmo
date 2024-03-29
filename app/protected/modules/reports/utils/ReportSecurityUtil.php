<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2011 Zurmo Inc.
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
     * Helper class for working with security operations on report results and for working with the report wizard
     * in the user interface
     */
    class ReportSecurityUtil
    {
        /**
         * @param $moduleClassName
         * @return bool
         */
        public static function canCurrentUserCanAccessModule($moduleClassName)
        {
            assert('is_string($moduleClassName)');
            if($moduleClassName::getStateMetadataAdapterClassName() != null)
            {
                $reportRules     = ReportRules::makeByModuleClassName($moduleClassName);
                return $reportRules->canUserAccessModuleInAVariableState(Yii::app()->user->userModel);
            }
            else
            {
                return RightsUtil::canUserAccessModule($moduleClassName, Yii::app()->user->userModel);
            }
        }

        /**
         * @param array $componentForms
         * @return bool
         */
        public static function canCurrentUserAccessAllComponents(Array $componentForms)
        {
            foreach($componentForms as $componentForm)
            {
                if(!self::canCurrentUserAccessComponent($componentForm))
                {
                    return false;
                }
            }
            return true;
        }

        /**
         * Resolves for a given component whether the user has necessary rights to access the component information.
         * An example is if a component is account's opportunities and the current user cannot access the opportunities
         * module.
         * @param ComponentForReportForm $componentForm
         * @return bool
         */
        protected static function canCurrentUserAccessComponent(ComponentForReportForm $componentForm)
        {
            $modelClassName       = $componentForm->getModelClassName();
            $moduleClassName      = $componentForm->getModuleClassName();
            if(!$componentForm->hasRelatedData())
            {
                return self::canCurrentUserCanAccessModule($moduleClassName);
            }
            else
            {
                foreach($componentForm->attributeAndRelationData as $relationOrAttribute)
                {
                    if(!self::canCurrentUserCanAccessModule($moduleClassName))
                    {
                        return false;
                    }
                    $modelToReportAdapter = ModelRelationsAndAttributesToReportAdapter::
                                            make($moduleClassName, $modelClassName, $componentForm->getReportType());
                    if($modelToReportAdapter->isReportedOnAsARelation($relationOrAttribute))
                    {
                        $modelClassName       = $modelToReportAdapter->getRelationModelClassName($relationOrAttribute);
                        $moduleClassName      = $modelToReportAdapter->getRelationModuleClassName($relationOrAttribute);
                    }
                }
                return true;
            }
        }
    }
?>
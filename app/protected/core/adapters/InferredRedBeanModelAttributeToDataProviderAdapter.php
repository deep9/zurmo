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
     * Adapts an inferred relation on a RedBeanModel to the data provider
     */
    class InferredRedBeanModelAttributeToDataProviderAdapter extends RedBeanModelAttributeToDataProviderAdapter
    {
        protected $inferredRelationModelClassName;

        protected $inferredRelationModuleClassName;

        /**
         * @param string $modelClassName
         * @param string $attribute
         * @param string $relatedAttribute
         * @param string $inferredRelationModelClassName
         * @param string $inferredRelationModuleClassName
         */
        public function __construct($modelClassName, $attribute, $inferredRelationModelClassName, $inferredRelationModuleClassName)
        {
            assert('is_string($modelClassName)');
            assert('is_string($attribute)');
            assert('is_string($inferredRelationModelClassName)');
            assert('is_string($inferredRelationModuleClassName)');
            $this->modelClassName                   = $modelClassName;
            $this->attribute                        = $attribute;
            $this->inferredRelationModelClassName  = $inferredRelationModelClassName;
            $this->inferredRelationModuleClassName = $inferredRelationModuleClassName;

        }

        public function isInferredRelation()
        {
            return true;
        }

        public function getInferredRelationModelClassName()
        {
            return $this->inferredRelationModelClassName;
        }

        public function getInferredRelationModuleClassName()
        {
            return $this->inferredRelationModuleClassName;
        }
    }
?>
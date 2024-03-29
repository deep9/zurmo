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
     * Base class for all wizard form models.  Manages the interaction between the wizard model object and the
     * user interface.  Reporting and Workflow both extend this with @see ReportWizardForm and @see WorkflowWizardForm
     */
    abstract class WizardForm extends CFormModel
    {
        /**
         * Id of the model if available.
         * @var integer
         */
        public $id;

        protected $isNew = false;

        /**
         * Mimics the expected interface by the views when calling into
         * a form or model.
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * If the report has not been saved yet, then this returns true
         * @return bool
         */
        public function isNew()
        {
            return $this->isNew;
        }

        public function setIsNew()
        {
            $this->isNew = true;
        }

        /**
         * @param $componentType
         * @param $componentName
         * @return bool
         */
        protected function validateComponent($componentType, $componentName)
        {
            assert('is_string($componentType)');
            assert('is_string($componentName)');
            $passedValidation = true;
            foreach($this->{$componentName} as $model)
            {
                if(!$model->validate())
                {
                    foreach($model->getErrors() as $attribute => $errorArray)
                    {
                        assert('is_array($errorArray)');
                        $attributePrefix = static::resolveErrorAttributePrefix($componentType, $model->getRowKey());
                        $this->addError( $attributePrefix . $attribute, $errorArray[0]);
                    }
                    $passedValidation = false;
                }
            }
            return $passedValidation;
        }

        /**
         * @param $treeType string
         * @param $count integer
         * @return string
         */
        protected static function resolveErrorAttributePrefix($treeType, $count)
        {
            assert('is_string($treeType)');
            assert('is_int($count)');
            return $treeType . '_' . $count . '_';
        }
    }
?>
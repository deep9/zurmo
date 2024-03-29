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

    class EmailTemplate extends OwnedSecurableItem
    {
        const TYPE_WORKFLOW = 1;

        const TYPE_CONTACT  = 2;

        public static function getByName($name)
        {
            return self::getByNameOrEquivalent('name', $name);
        }

        public static function getModuleClassName()
        {
            return 'EmailTemplatesModule';
        }

        public static function getTypeDropDownArray()
        {
             return array(
                 self::TYPE_WORKFLOW     => Zurmo::t('WorkflowsModule', 'Workflow'),
                 self::TYPE_CONTACT      => Zurmo::t('ContactsModule',  'Contact'),
             );
        }

        public static function renderNonEditableTypeStringContent($type)
        {
            assert('is_int($type) || $type == null');
            $dropDownArray = self::getTypeDropDownArray();
            if (!empty($dropDownArray[$type]))
            {
                return Yii::app()->format->text($dropDownArray[$type]);
            }
        }

        public function __toString()
        {
            try
            {
                if (trim($this->name) == '')
                {
                    return Zurmo::t('Default', '(Unnamed)');
                }
                return $this->name;
            }
            catch (AccessDeniedSecurityException $e)
            {
                return '';
            }
        }

        public static function canSaveMetadata()
        {
            return true;
        }

        public static function isTypeDeletable()
        {
            return true;
        }

        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    'type',
                    'modelClassName',
                    'name',
                    'subject',
                    'language',
                    'htmlContent',
                    'textContent',
                ),
                'rules' => array(
                    array('type',                       'required'),
                    array('type',                       'type',    'type' => 'integer'),
                    array('type',                       'numerical', 'min' => self::TYPE_WORKFLOW,
                                                                     'max' => self::TYPE_CONTACT),
                    array('modelClassName',             'required'),
                    array('modelClassName',             'type',     'type' => 'string'),
                    array('modelClassName',             'length', 'max' => 64),
                    array('modelClassName',             'validateModelExists', 'except' => 'autoBuildDatabase'),
                    array('name',                       'required'),
                    array('name',                       'type',    'type' => 'string'),
                    array('name',                       'length',  'min'  => 3, 'max' => 64),
                    array('subject',                    'required'),
                    array('subject',                    'type',    'type' => 'string'),
                    array('subject',                    'length',  'min'  => 3, 'max' => 64),
                    array('language',                   'type',    'type' => 'string'),
                    array('language',                   'length',  'min' => 2, 'max' => 2),
                    array('language',                   'setToUserDefaultLanguage'),
                    array('htmlContent',                'type',    'type' => 'string'),
                    array('textContent',                'type',    'type' => 'string'),
                    array('htmlContent',                'validateHtmlContentAndTextContent'),
                    array('textContent',                'validateHtmlContentAndTextContent'),
                    array('htmlContent',                'validateMergeTags'),
                    array('textContent',                'validateMergeTags'),
                ),
                'elements' => array(
                    'htmlContent'                  => 'TextArea',
                    'textContent'                  => 'TextArea',
                ),
            );
            return $metadata;
        }

        public function validateModelExists($attribute, $params)
        {
            $passedValidation = true;
            if (!empty($this->$attribute))
            {
                if (@class_exists($this->$attribute))
                {
                    if (!is_subclass_of($this->$attribute, 'RedBeanModel'))
                    {
                        $this->addError($attribute, Zurmo::t('EmailTemplatesModule', 'Provided class name is not a valid Model class.'));
                        $passedValidation = false;
                    }
                }
                else
                {
                    $this->addError($attribute, Zurmo::t('EmailTemplatesModule', 'Provided class name does not exist.'));
                    $passedValidation = false;
                }
            }
            return $passedValidation;
        }

        public function validateHtmlContentAndTextContent($attribute, $params)
        {
            if (empty($this->textContent) && empty($this->htmlContent))
            {
                $this->addError($attribute, Zurmo::t('EmailTemplatesModule', 'Please provide at least one of the contents field.'));
                return false;
            }
            return true;
        }

        public function setToUserDefaultLanguage($attribute, $params)
        {
            if (empty($this->$attribute))
            {
                $this->$attribute = Yii::app()->user->userModel->language;
            }
            else
            {
            }
        }

        public function validateMergeTags($attribute, $params)
        {
            $passedValidation = true;
            if (!empty($this->$attribute) && @class_exists($this->modelClassName))
            {
                $model          = new $this->modelClassName(false);
                $mergeTagsUtil  = MergeTagsUtilFactory::make($this->type, $this->language, $this->$attribute);
                $invalidTags    = array();
                if (!$mergeTagsUtil->extractMergeTagsPlaceHolders() ||
                                    $mergeTagsUtil->resolveMergeTagsArrayToAttributes($model, $invalidTags, null))
                {
                }
                else
                {
                    if (!empty($invalidTags))
                    {
                        foreach ($invalidTags as $tag)
                        {
                            $errorMessage = EmailTemplateHtmlAndTextContentElement::renderModelAttributeLabel($attribute) .
                                            ': Invalid MergeTag({mergeTag}) used.';
                            $this->addError($attribute, Zurmo::t('EmailTemplatesModule', $errorMessage,
                                                        array('{mergeTag}' => $tag)));
                            $passedValidation = false;
                        }
                    }
                    else
                    {
                        $this->addError($attribute, Zurmo::t('EmailTemplatesModule', 'Provided content contains few invalid merge tags.'));
                        $passedValidation = false;
                    }
                }
            }
            return $passedValidation;
        }

        /**
         * @param $type
         * @return Array of EmailTemplate models
         */
        public static function getActiveByModuleClassNameAndIsNewModel($type)
        {
            assert('is_int($type)');
            $searchAttributeData = array();
            $searchAttributeData['clauses'] = array(
                1 => array(
                    'attributeName'        => 'type',
                    'operatorType'         => 'equals',
                    'value'                => $type,
                ),
            );
            $searchAttributeData['structure'] = '1';
            $joinTablesAdapter                = new RedBeanModelJoinTablesQueryAdapter('EmailTemplate');
            $where = RedBeanModelDataProvider::makeWhere('EmailTemplate', $searchAttributeData, $joinTablesAdapter);
            return self::getSubset($joinTablesAdapter, null, null, $where, 'name');
        }

        public static function getDataAndLabelsByType($type)
        {
            assert('is_int($type)');
            $dataAndLabels = array();
            $emailTemplates = static::getActiveByModuleClassNameAndIsNewModel($type);
            foreach($emailTemplates as $emailTemplate)
            {
                $dataAndLabels[$emailTemplate->id] = strval($emailTemplate);
            }
            return $dataAndLabels;
        }

        protected static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return array_merge(parent::translatedAttributeLabels($language),
                array(
                    'modelClassName'  => Zurmo::t('Core',                'Module',   array(), null, $language),
                    'language'        => Zurmo::t('ZurmoModule',         'Language',   array(), null, $language),
                    'htmlContent'     => Zurmo::t('EmailMessagesModule', 'Html Content',  array(), null, $language),
                    'name'            => Zurmo::t('ZurmoModule',         'Name',  array(), null, $language),
                    'subject'         => Zurmo::t('EmailMessagesModule', 'Subject',  array(), null, $language),
                    'type'            => Zurmo::t('Core',                'Type',  array(), null, $language),
                    'textContent'     => Zurmo::t('EmailMessagesModule', 'Text Content',  array(), null, $language),
                )
            );
        }
    }
?>

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

    class Opportunity extends OwnedSecurableItem
    {
        public static function getByName($name)
        {
            return self::getByNameOrEquivalent('name', $name);
        }

        /**
         * @return value of what is considered the 'closed won' stage. It could be in the future named something else
         * or changed by the user.  This api will be expanded to handle that.  By default it will return 'Closed Won'
         */
        public static function getStageClosedWonValue()
        {
            return 'Closed Won';
        }

        public function __toString()
        {
            try
            {
                if (trim($this->name) == '')
                {
                    return Zurmo::t('OpportunitiesModule', '(Unnamed)');
                }
                return $this->name;
            }
            catch (AccessDeniedSecurityException $e)
            {
                return '';
            }
        }

        public static function getModuleClassName()
        {
            return 'OpportunitiesModule';
        }

        public static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return array_merge(parent::translatedAttributeLabels($language), array(
                'account'     => Zurmo::t('AccountsModule',      'AccountsModuleSingularLabel', $params, null, $language),
                'amount'      => Zurmo::t('OpportunitiesModule', 'Amount',  array(), null, $language),
                'closeDate'   => Zurmo::t('OpportunitiesModule', 'Close Date',  array(), null, $language),
                'contacts'    => Zurmo::t('ContactsModule',      'ContactsModulePluralLabel',   $params, null, $language),
                'description' => Zurmo::t('ZurmoModule',         'Description',  array(), null, $language),
                'meetings'    => Zurmo::t('MeetingsModule',      'Meetings',  array(), null, $language),
                'name'        => Zurmo::t('ZurmoModule',         'Name',  array(), null, $language),
                'notes'       => Zurmo::t('NotesModule',         'Notes',  array(), null, $language),
                'probability' => Zurmo::t('OpportunitiesModule', 'Probability',  array(), null, $language),
                'source'      => Zurmo::t('ContactsModule',      'Source',   array(), null, $language),
                'stage'       => Zurmo::t('OpportunitiesModule', 'Stage',  array(), null, $language),
                'tasks'       => Zurmo::t('TasksModule',         'Tasks',  array(), null, $language)));
        }

        public static function canSaveMetadata()
        {
            return true;
        }

        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    'closeDate',
                    'description',
                    'name',
                    'probability',
                ),
                'relations' => array(
                    'account'       => array(RedBeanModel::HAS_ONE,   'Account'),
                    'amount'        => array(RedBeanModel::HAS_ONE,   'CurrencyValue',    RedBeanModel::OWNED,
                                             RedBeanModel::LINK_TYPE_SPECIFIC, 'amount'),
                    'contacts'      => array(RedBeanModel::MANY_MANY, 'Contact'),
                    'stage'         => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED,
                                             RedBeanModel::LINK_TYPE_SPECIFIC, 'stage'),
                    'source'        => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED,
                                             RedBeanModel::LINK_TYPE_SPECIFIC, 'source'),
                ),
                'derivedRelationsViaCastedUpModel' => array(
                    'meetings' => array(RedBeanModel::MANY_MANY, 'Meeting', 'activityItems'),
                    'notes'    => array(RedBeanModel::MANY_MANY, 'Note',    'activityItems'),
                    'tasks'    => array(RedBeanModel::MANY_MANY, 'Task',    'activityItems'),
                ),
                'rules' => array(
                    array('amount',        'required'),
                    array('closeDate',     'required'),
                    array('closeDate',     'type', 'type' => 'date'),
                    array('description',   'type',    'type' => 'string'),
                    array('name',          'required'),
                    array('name',          'type',    'type' => 'string'),
                    array('name',          'length',  'min'  => 3, 'max' => 64),
                    array('probability',   'type',      'type' => 'integer'),
                    array('probability',   'numerical', 'min' => 0, 'max' => 100),
                    array('probability',   'required'),
                    array('probability',   'default', 'value' => 0),
                    array('stage',         'required'),
                ),
                'elements' => array(
                    'amount'      => 'CurrencyValue',
                    'account'     => 'Account',
                    'closeDate'   => 'Date',
                    'description' => 'TextArea',
                ),
                'customFields' => array(
                    'stage'  => 'SalesStages',
                    'source' => 'LeadSources',
                ),
                'defaultSortAttribute' => 'name',
                'rollupRelations' => array(
                    'contacts',
                ),
                'noAudit' => array(
                    'description'
                ),
            );
            return $metadata;
        }

        public static function isTypeDeletable()
        {
            return true;
        }

        public static function getRollUpRulesType()
        {
            return 'Opportunity';
        }

        public static function hasReadPermissionsOptimization()
        {
            return true;
        }

        public static function getGamificationRulesType()
        {
            return 'OpportunityGamification';
        }
    }
?>

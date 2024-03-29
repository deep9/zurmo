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

    class WorkflowTriggersUtilTest extends WorkflowTriggersUtilBaseTest
    {
        public function testResolveStructureToPHPString()
        {
            $this->assertEquals('1', WorkflowTriggersUtil::resolveStructureToPHPString('1'));
            $this->assertEquals('1 && 2', WorkflowTriggersUtil::resolveStructureToPHPString('1 AND 2'));
            $this->assertEquals('1 || 2', WorkflowTriggersUtil::resolveStructureToPHPString('1 OR 2'));
            $this->assertEquals('(1 || 2) && 3', WorkflowTriggersUtil::resolveStructureToPHPString('(1 OR 2) AND 3'));
            $this->assertEquals('1 && 2 && 3', WorkflowTriggersUtil::resolveStructureToPHPString('1 AND 2 AND 3'));
        }

        public function testResolveBooleansDataToPHPString()
        {
            $data = array(1 => true);
            $this->assertEquals('true', WorkflowTriggersUtil::resolveBooleansDataToPHPString('1', $data));

            $data = array(1 => true, 2 => false);
            $this->assertEquals('true && false', WorkflowTriggersUtil::resolveBooleansDataToPHPString('1 && false', $data));
        }

        /**
         * @expectedException NotSupportedException()
         */
        public function testResolveBooleansDataToPHPStringWithInvalidDataKey()
        {
            $data = array(1 => true);
            $this->assertEquals('true', WorkflowTriggersUtil::resolveBooleansDataToPHPString('1', $data));
        }

        /**
         * @expectedException NotSupportedException
         */
        public function testResolveBooleansDataToPHPStringWithInvalidDataValue()
        {
            $data = array(1 => null);
            $this->assertEquals('true', WorkflowTriggersUtil::resolveBooleansDataToPHPString('1', $data));
        }

        public function testEvaluatePHPString()
        {
            $this->assertTrue (WorkflowTriggersUtil::evaluatePHPString('true'));
            $this->assertFalse(WorkflowTriggersUtil::evaluatePHPString('false'));
            $this->assertTrue (WorkflowTriggersUtil::evaluatePHPString('true && true'));
            $this->assertFalse(WorkflowTriggersUtil::evaluatePHPString('true && false'));
            $this->assertTrue (WorkflowTriggersUtil::evaluatePHPString('true || false'));
            $this->assertTrue (WorkflowTriggersUtil::evaluatePHPString('true || false || true'));
            $this->assertFalse(WorkflowTriggersUtil::evaluatePHPString('false || false || false'));
            $this->assertTrue (WorkflowTriggersUtil::evaluatePHPString('true && (true || false)'));
            $this->assertTrue (WorkflowTriggersUtil::evaluatePHPString('false || (true && true)'));
        }

        /**
         * Example of too much nesting, that would result in an exception
         * @expectedException NotSupportedException
         */
        public function testAreTriggersTrueBeforeSaveWithInvalidAttributeData()
        {
            $attributeIndexOrDerivedType = 'hasOne___hasMany2___primaryAddress___street1';
            $workflow = self::makeOnSaveWorkflowAndTriggerWithoutValueType($attributeIndexOrDerivedType, 'equals', 'aValue');
            $model           = new WorkflowModelTestItem();
            $this->assertTrue(WorkflowTriggersUtil::areTriggersTrueBeforeSave($workflow, $model));
        }

        /**
         * Testing that when you have a relation with another owned relation before the attribute, that it works correctly.
         */
        public function testAreTriggersTrueBeforeSaveWithRelationAndAnotherRelationBeforeAttribute()
        {
            $attributeIndexOrDerivedType = 'hasMany2___primaryAddress___street1';
            $workflow      = self::makeOnSaveWorkflowAndTriggerWithoutValueType($attributeIndexOrDerivedType, 'equals', 'cValue',
                             'WorkflowsTestModule', 'WorkflowModelTestItem2');
            $model           = new WorkflowModelTestItem2();
            $relatedModel    = new WorkflowModelTestItem();
            $address                        = new Address();
            $relatedModel->primaryAddress          = $address;
            $relatedModel->primaryAddress->street1 = 'cValue';
            $model->hasMany2->add($relatedModel);
            $this->assertTrue(WorkflowTriggersUtil::areTriggersTrueBeforeSave($workflow, $model));

            $model->hasMany2[0]->primaryAddress->street1 = 'bValue';
            $this->assertFalse(WorkflowTriggersUtil::areTriggersTrueBeforeSave($workflow, $model));
        }
    }
?>
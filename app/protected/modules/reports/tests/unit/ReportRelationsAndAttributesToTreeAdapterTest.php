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

    class ReportRelationsAndAttributesToTreeAdapterTest extends ZurmoBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
        }

        public function setup()
        {
            parent::setUp();
            Yii::app()->user->userModel = User::getByUsername('super');
        }

        public function testGetDataWhereNodeIdIsSource()
        {
            //getData($nodeId)
        }

        public function testGetDataForEachTreeType()
        {
            //getData($nodeId)
            //test completely getAttributesData using different treeTypes
            //make sure calculated derived attribute doesnt show up as a filter since you can't filter on it
        }

        public function testGetDataForVariousReportTypes()
        {
            //getData($nodeId)
            //Main objective is to test completely makeModelRelationsAndAttributesToReportAdapter($moduleClassName, $modelClassName)
        }

        public function testGetDataWhereNodeIdIsNotSourceButAChildRelation()
        {
            //getData($nodeId)
        }

        public function testGetDataWhereNodeIdIsTwoRelationsDeep()
        {
            //getData($nodeId)
        }

        public function testRemoveTreeTypeFromNodeId()
        {
            //removeTreeTypeFromNodeId($nodeId, $treeType)
        }

        public function testResolveInputPrefixData()
        {
        //resolveInputPrefixData($nodeIdWithoutTreeType, $formModelClassName, $treeType, $rowNumber)

            //test both when there is one part and more than one part
        }

        public function testResolveAttributeByNodeId()
        {

            //resolveAttributeByNodeId($nodeIdWithoutTreeType)

            //test both when there is one part and more than one part
        }

        public function testWhereNestedGroupBysAndGettingDataForOrderBy()
        {
            $report                              = new Report();
            $report->setType(Report::TYPE_SUMMATION);
            $groupBy = new GroupByForReportForm('ReportsTestModule', 'ReportModelTestItem', $report->getType());
            $groupBy->attributeIndexOrDerivedType = 'hasOne___name';
            $groupBy->axis                        = 'x';
            $report->addGroupBy($groupBy);
            $report->setModuleClassName('ReportsTestModule');
            $adapter = new ReportRelationsAndAttributesToTreeAdapter($report, ComponentForReportForm::TYPE_ORDER_BYS);
            $data    = $adapter->getData(ComponentForReportForm::TYPE_ORDER_BYS . '_hasOne');
            $this->assertEquals('OrderBys_hasOne___name', $data[0]['id']);
            $this->assertEquals('OrderBys_hasOne___createdByUser', $data[1]['id']);
            $this->assertEquals('OrderBys_hasOne___hasMany3', $data[2]['id']);
            $this->assertEquals('OrderBys_hasOne___modifiedByUser', $data[3]['id']);
            $this->assertEquals('OrderBys_hasOne___owner', $data[4]['id']);
        }
    }
?>
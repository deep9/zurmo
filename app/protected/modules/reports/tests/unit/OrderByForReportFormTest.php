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

    class OrderByForReportFormTest extends ZurmoBaseTest
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

        public function testSetAndGetGroupBy()
        {
            $orderBy                              = new OrderByForReportForm('ReportsTestModule', 'ReportModelTestItem',
                                                                           Report::TYPE_SUMMATION);
            $orderBy->attributeIndexOrDerivedType = 'string';
            $this->assertEquals('asc', $orderBy->order);
            $orderBy->order                       = 'desc';
            $this->assertEquals('string', $orderBy->attributeAndRelationData);
            $this->assertEquals('string', $orderBy->attributeIndexOrDerivedType);
            $this->assertEquals('string', $orderBy->getResolvedAttribute());
            $this->assertEquals('String', $orderBy->getDisplayLabel());
        }

        /**
         * @depends testSetAndGetGroupBy
         */
        public function testValidate()
        {
            $orderBy                              = new OrderByForReportForm('ReportsTestModule', 'ReportModelTestItem',
                                                                           Report::TYPE_SUMMATION);
            $orderBy->attributeIndexOrDerivedType = 'string';
            $orderBy->order                       = null;
            $validated = $orderBy->validate();
            $this->assertFalse($validated);
            $errors = $orderBy->getErrors();
            $compareErrors                       = array('order'  => array('Order cannot be blank.'));
            $this->assertEquals($compareErrors, $errors);
            $orderBy->order                        = 'z';
            $validated = $orderBy->validate();
            $this->assertFalse($validated);
            $errors = $orderBy->getErrors();
            $compareErrors                       = array('order'  => array('Order must be asc or desc.'));
            $this->assertEquals($compareErrors, $errors);
            $orderBy->order                        = 'desc';
            $validated = $orderBy->validate();
            $this->assertTrue($validated);
        }
    }
?>
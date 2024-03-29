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
    class MarketingListTest extends ZurmoBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            SecurityTestHelper::createUsers();
        }

        public function setUp()
        {
            parent::setUp();
            Yii::app()->user->userModel = User::getByUsername('super');
        }

        public function testCreateAndGetMarketingListById()
        {
            $marketingList = new MarketingList();
            $marketingList->name        = 'Test Marketing List';
            $marketingList->description = 'Test Description';
            $this->assertTrue($marketingList->save());
            $id = $marketingList->id;
            unset($marketingList);
            $marketingList = MarketingList::getById($id);
            $this->assertEquals('Test Marketing List', $marketingList->name);
            $this->assertEquals('Test Description',   $marketingList->description);
        }

        /**
         * @depends testCreateAndGetMarketingListById
         */
        public function testGetMarketingListByName()
        {
            $marketingLists = MarketingList::getByName('Test Marketing List');
            $this->assertEquals(1, count($marketingLists));
            $this->assertEquals('Test Marketing List', $marketingLists[0]->name);
        }

        /**
         * @depends testCreateAndGetMarketingListById
         */
        public function testDeleteMarketingList()
        {
            $marketingList = new MarketingList();
            $marketingList->name        = 'Test Marketing List2';
            $marketingList->description = 'Test Description2';
            $this->assertTrue($marketingList->save());
            $marketingLists = MarketingList::getAll();
            $this->assertEquals(2, count($marketingLists));
            $marketingLists[0]->delete();
            $marketingLists = MarketingList::getAll();
            $this->assertEquals(1, count($marketingLists));
        }
    }
?>
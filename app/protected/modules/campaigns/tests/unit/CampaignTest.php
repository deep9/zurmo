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
    class CampaignTest extends ZurmoBaseTest
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

        public function testCreateAndGetCampaignListById()
        {
            $campaign = new Campaign();
            $campaign->name              = 'Test Campaign Name';
            $campaign->type              = 1;
            $campaign->formatType        = 'Test FormatType';
            $campaign->fromName          = 'Test Fromname';
            $campaign->fromAddress       = 'Test From Address';
            $campaign->subject           = 'Test Subject';
            $campaign->htmlContent       = 'Test Html Content';
            $campaign->textContent       = 'Test Text Content';
            $this->assertTrue($campaign->save());
            $id = $campaign->id;
            unset($campaign);
            $campaign = Campaign::getById($id);
            $this->assertEquals('Test Campaign Name', $campaign->name);
            $this->assertEquals(1                   , $campaign->type);
            $this->assertEquals('Test FormatType'   , $campaign->formatType);
            $this->assertEquals('Test Fromname'     , $campaign->fromName);
            $this->assertEquals('Test From Address' , $campaign->fromAddress);
            $this->assertEquals('Test Subject'      , $campaign->subject);
            $this->assertEquals('Test Html Content' , $campaign->htmlContent);
            $this->assertEquals('Test Text Content' , $campaign->textContent);
        }

        /**
         * @depends testCreateAndGetCampaignListById
         */
        public function testGetCampaignByName()
        {
            $campaigns = Campaign::getByName('Test Campaign Name');
            $this->assertEquals(1, count($campaigns));
            $this->assertEquals('Test Campaign Name', $campaigns[0]->name);
        }

        /**
         * @depends testCreateAndGetCampaignListById
         */
        public function testGetLabel()
        {
            $campaigns = Campaign::getByName('Test Campaign Name');
            $this->assertEquals(1, count($campaigns));
            $this->assertEquals('Campaign',  $campaigns[0]::getModelLabelByTypeAndLanguage('Singular'));
            $this->assertEquals('Campaigns', $campaigns[0]::getModelLabelByTypeAndLanguage('Plural'));
        }

        public function testDeleteCampaign()
        {
            $campaigns = new Campaign();
            $campaigns->name        = 'Test Campaign Name';
            $campaigns->subject     = 'Test Subject';
            $this->assertTrue($campaigns->save());
            $campaigns = Campaign::getAll();
            $this->assertEquals(2, count($campaigns));
            $campaigns[0]->delete();
            $campaigns = Campaign::getAll();
            $this->assertEquals(1, count($campaigns));
        }
    }
?>
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
     * Workflows module walkthrough tests for super users.
     */
    class WorkflowsSuperUserWalkthroughTest extends ZurmoWalkthroughBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            //Setup test data owned by the super user.
            $account = AccountTestHelper::createAccountByNameForOwner('superAccount', $super);
            AccountTestHelper::createAccountByNameForOwner('superAccount2', $super);
            ContactTestHelper::createContactWithAccountByNameForOwner('superContact', $super, $account);
        }

        public function testSuperUserAllDefaultControllerActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            
            $this->runControllerWithNoExceptionsAndGetContent      ('workflows/default/list');
            $this->runControllerWithExitExceptionAndGetContent     ('workflows/default/create');
            $this->runControllerWithNoExceptionsAndGetContent      ('workflows/default/selectType');
            //actionList
            //actionCreate
            //actionSelectList
            //actionEdit
            //actionSave
            //test actionDelete
            //test queue views too.
        }

        /**
         * @depends testSuperUserAllDefaultControllerActions
         */
        public function testCreateAction()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            $this->assertEquals(0, count(SavedWorkflow::getAll()));
            $content = $this->runControllerWithExitExceptionAndGetContent     ('workflows/default/create');
            $this->assertFalse(strpos($content, 'On Save Workflow') === false);
            $this->assertFalse(strpos($content, 'By Time Workflow') === false);

            $this->setGetArray(array('type' => 'OnSave'));
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent     ('workflows/default/create');
            $this->assertFalse(strpos($content, 'Accounts') === false);
            
            $this->setGetArray(array('type' => 'OnSave'));

            $data   = array();
            $data['OnSaveWorkflowWizardForm'] = array('description'       => 'someDescription',
                                                      'isActive'          => '0',
                                                      'name'              => 'someName',
                                                      'triggerOn'         => Workflow::TRIGGER_ON_NEW,
                                                      'triggersStructure' => '1 AND 2',
                                                      'moduleClassName'   => 'WorkflowsTestModule');
            $this->setPostArray($data);
            $this->runControllerWithExitExceptionAndGetContent     ('workflows/default/save');
            $this->assertEquals(1, count(SavedWorkflow::getAll()));
        }
    }
?>
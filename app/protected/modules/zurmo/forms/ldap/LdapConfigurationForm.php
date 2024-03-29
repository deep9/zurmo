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
     * Form to all editing and viewing of Ldap Configuration values in the user interface.
     */
    class LdapConfigurationForm extends ConfigurationForm
    {
        public $serverType;
        public $host;
        public $port = 389;
        public $bindRegisteredDomain;
        public $bindPassword;
        public $baseDomain;
        public $testConnection;
        public $enabled;

        public function rules()
        {
            return array(
                array('serverType',                        'type',      'type' => 'string'),
                array('serverType',                        'length',    'min'  => 1, 'max' => 25),
                array('host',                              'required'),
                array('host',                              'type',      'type' => 'string'),
                array('host',                              'length',    'min'  => 1, 'max' => 64),
                array('port',                              'required'),
                array('port',                              'type',      'type' => 'integer'),
                array('port',                              'numerical', 'min'  => 1),
                array('bindRegisteredDomain',              'required'),
                array('bindRegisteredDomain',              'type',      'type' => 'string'),
                array('bindRegisteredDomain',              'length',    'min'  => 1, 'max' => 64),
                array('bindPassword',                      'required'),
                array('bindPassword',                      'type',      'type' => 'string'),
                array('bindPassword',                      'length',    'min'  => 1, 'max' => 64),
                array('baseDomain',                        'required'),
                array('baseDomain',                        'type',      'type' => 'string'),
                array('baseDomain',                        'length',    'min'  => 1, 'max' => 64),
                array('enabled',                           'boolean'),
            );
        }

        public function attributeLabels()
        {
            return array(
                'serverType'                           => Zurmo::t('ZurmoModule', 'Server Type'),
                'host'                                 => Zurmo::t('ZurmoModule', 'Host'),
                'port'                                 => Zurmo::t('ZurmoModule', 'Port'),
                'bindRegisteredDomain'                 => Zurmo::t('ZurmoModule', 'Username'),
                'bindPassword'                         => Zurmo::t('ZurmoModule', 'Password'),
                'baseDomain'                           => Zurmo::t('ZurmoModule', 'Base Domain'.self::renderHelpContent()),
                'enabled'                              => Zurmo::t('ZurmoModule', 'Turn On Ldap')
            );
        }
        
        protected static function renderHelpContent()
        {
            $title       = Zurmo::t('ZurmoModule', 'Like: dc=server,dc=world for both LDAP and Active Directory');
            $content     = '<span id="ldap-rollup-tooltip" class="tooltip" title="' . $title . '">?</span>';
            $qtip = new ZurmoTip();
            $qtip->addQTip("#ldap-rollup-tooltip");
            return $content;
        }
    }
?>
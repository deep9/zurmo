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
     * Component form for group by definitions
     */
    class GroupByForReportForm extends ComponentForReportForm
    {
        /**
         * Default axis is the x-axis
         * @var string
         */
        public $axis = 'x';

        /**
         * @return string component type
         */
        public static function getType()
        {
            return static::TYPE_GROUP_BYS;
        }

        /**
         * @return array
         */
        public function rules()
        {
            return array_merge(parent::rules(), array(
                array('axis', 'required'),
                array('axis', 'type', 'type' => 'string'),
                array('axis', 'validateAxis'),
            ));
        }

        /**
         * @return bool
         */
        public function validateAxis()
        {
            if($this->axis == null)
            {
                return false;
            }
            if($this->axis != 'x' && $this->axis != 'y')
            {
                $this->addError('axis', Zurmo::t('ReportsModule', 'Axis must be x or y.'));
                return false;
            }
            return true;
        }

        /**
         * @return array
         * @throws NotSupportedException if the attributeIndexOrDerivedType is null
         */
        public function getAxisValuesAndLabels()
        {
            if($this->attributeIndexOrDerivedType == null)
            {
                throw new NotSupportedException();
            }
            $data = array();
            $data['x']       = Zurmo::t('ReportsModule', 'X-Axis');
            $data['y']       = Zurmo::t('ReportsModule', 'Y-Axis');
            return $data;
        }
    }
?>
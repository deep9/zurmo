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
     * Create the query string part for the SQL where part (filter components)
     */
    class FiltersReportQueryBuilder extends ReportQueryBuilder
    {
        /**
         * @var null | string
         */
        protected $filtersStructure;

        /**
         * @param RedBeanModelJoinTablesQueryAdapter $joinTablesAdapter
         * @param null | string $filtersStructure
         */
        public function __construct(RedBeanModelJoinTablesQueryAdapter $joinTablesAdapter,
                                    $filtersStructure)
        {
            assert('is_string($filtersStructure)');
            parent::__construct($joinTablesAdapter);
            $this->filtersStructure = $filtersStructure;
        }

        /**
         * @param array $components
         * @return null|string
         */
        public function makeQueryContent(Array $components)
        {
            $whereContent = array();
            foreach($components as $key => $filter)
            {
                $modelToReportAdapter   = static::makeModelToReportAdapterByComponentForm($filter);
                $itemBuilder            = new FilterReportItemQueryBuilder($filter, $this->joinTablesAdapter,
                                              $modelToReportAdapter, $this->currencyConversionType);
                $whereContent[$key + 1] = $itemBuilder->resolveComponentAttributeStringContent();
            }
            $content = strtr(strtolower($this->filtersStructure), $whereContent);
            if(empty($content))
            {
                return null;
            }
            return $content;
        }
    }
?>
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
    Yii::import('zii.widgets.grid.CGridColumn');

    /**
     * Column class for managing the drill down link column.  This is used by summation drill down reports when rendering
     * the each row.  There is a drill down column, that when clicked will expand the drill down results grid for that
     * row
     * Added &page=1 to ensure drillDown always starts at page 1.  Assumes there is at least one parameter always passed.
     * Not perfect, but works ok for now.
     */
    class DrillDownColumn extends CGridColumn
    {
        public function init()
        {
            // Begin Not Coding Standard
            $script = <<<END
jQuery('.drillDownExpandAndLoadLink').unbind('click'); jQuery('.drillDownExpandAndLoadLink').live('click', function(){
    $(this).hide();
    $(this).parent().find('.drillDownCollapseLink').first().show();
    $(this).parentsUntil('tr').parent().next().show();
    var loadDivId = $(this).parentsUntil('tr').parent().addClass('expanded-row').next().find('.drillDownContent').attr('id');
    $.ajax({
        url      : $(this).data('url') + '&page=1',
        type     : 'GET',
        beforeSend : function(){
            makeLargeLoadingSpinner(true, "#"+loadDivId);
        },
        success  : function(data){
            jQuery('#' + loadDivId).html(data)
        },
        error : function(){
            //todo: error call
        }
    });
});
jQuery('.drillDownExpandLink').unbind('click'); jQuery('.drillDownExpandLink').live('click', function(){
    $(this).hide();
    $(this).parent().find('.drillDownCollapseLink').first().show();
    $(this).parentsUntil('tr').parent().addClass('expanded-row').next().show();
});
jQuery('.drillDownCollapseLink').unbind('click');jQuery('.drillDownCollapseLink').live('click', function(){
    $(this).hide();
    $(this).parent().find('.drillDownExpandLink').first().show();
    $(this).parentsUntil('tr').parent().removeClass('expanded-row').next().hide();
});
END;
            // End Not Coding Standard
            Yii::app()->getClientScript()->registerScript(__CLASS__ . 'SummationDrillDownToggleScript', $script);
        }

        /**
         * (non-PHPdoc)
         * @see CCheckBoxColumn::renderDataCellContent()
         */
        protected function renderDataCellContent($row, $data)
        {
            $dataParams               = array_merge(array('rowId' => $data->getId()),
                                                    $data->getDataParamsForDrillDownAjaxCall());
            $expandAndLoadLinkContent = ZurmoHtml::tag('span', array('class' => 'drillDownExpandAndLoadLink drilldown-link',
                                                                     'data-url' => $this->getDrillDownLoadUrl($dataParams)),
                                                                     'G');
            $expandLinkContent        = ZurmoHtml::tag('span', array('class' => 'drillDownExpandLink drilldown-link',
                                                                     'style' => "display:none;"), 'G');
            $collapseLinkContent      = ZurmoHtml::tag('span', array('class' => 'drillDownCollapseLink drilldown-link',
                                                                     'style' => "display:none;"), '&divide;');
            echo $expandAndLoadLinkContent . $expandLinkContent . $collapseLinkContent;
        }

        /**
         * @param array $dataParams
         * @return string
         */
        protected function getDrillDownLoadUrl(Array $dataParams)
        {
            return Yii::app()->createUrl('/reports/default/drillDownDetails/',
                   array_merge(GetUtil::getData(), $dataParams));
        }
    }
?>

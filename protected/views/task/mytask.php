<?php
/* @var $this TaskController */
/* @var $model Task */

$this->widget('booster.widgets.TbBreadcrumbs', array(
    'links'=>array('My task(s)')
));

$this->menu=array(
	array('label'=>'Log Activity', 'url'=>array('tasklog/index')),
);
?>
<div class="span-24">
<h2 class="page-header">My task(s)</h2>
<?php $this->widget('booster.widgets.TbGridView',
    array(
        'id' => 'mytask',
        'dataProvider' => $mytask,
        'type'=>'striped bordered condensed hover',
        'columns' => array(
            //'task_id',
            array(
                    'header' => 'No',
                    'value' => '$this->grid->dataProvider->pagination->currentPage*$this->grid->dataProvider->pagination->pageSize + $row+1',
                    'htmlOptions'=>array('width'=>40),
                ),
            array(
                    'header' => 'Project',
                    'value'=> '$data->task->project->name',
                    'htmlOptions'=>array('width'=>200),
                ),
            /* array(
                    'header' => 'Project Status',
                    'type'=>'raw',
                    'value' => '$data->task->project->status=="Upcoming" ? Yii::app()->controller->widget(\'booster.widgets.TbLabel\', array(\'context\' => \'primary\',\'label\' => \'Upcoming\'), true) : '
                                . '($data->task->project->status=="Active" ? Yii::app()->controller->widget(\'booster.widgets.TbLabel\', array(\'context\' => \'success\',\'label\' => \'Active\'), true) : '
                                . 'Yii::app()->controller->widget(\'booster.widgets.TbLabel\', array(\'context\' => \'danger\',\'label\' => \'Expired\'), true))'
                ),*/
            array(
                    'header' => 'Task Title',
                    'value'=> '$data->task->title',
                ),
            array(
                    'header'=>'Start date',
                    'value'=> 'date(\'d-m-Y\', strtotime($data->task->start_date))',
                    'htmlOptions'=>array('width'=>100),
                ),
            array(
                    'header'=>'Due date',
                    'value'=> 'date(\'d-m-Y\', strtotime($data->task->due_date))',
                    'htmlOptions'=>array('width'=>100),
                ),
            /* 
            array(
                    'header' => 'Deadline',
                    'type'=>'raw',
                    'value' => '$data->task->due_date<=date(\'Y-m-d\') ? Yii::app()->controller->widget(\'booster.widgets.TbLabel\', array(\'context\' => \'danger\',\'label\' => $data->task->due_date), true) : '
                                . 'Yii::app()->controller->widget(\'booster.widgets.TbLabel\', array(\'context\' => \'primary\',\'label\' => $data->task->due_date), true)'
                ),*/ 
            array(
                'class'=>'booster.widgets.TbButtonColumn',
                'header'=>'Options',
                'template'=>'{view}',
                'htmlOptions'=>array(
                    'width'=>80,
                    'style'=>'text-align:center;'
                    ),
                'buttons'=>array(
                    'view'=>array(
                        'url'=>'Yii::app()->createUrl("task/view", array("id"=>$data->task_id))',
                        ),
                    'create-log'=>array(
                        'label'=>'Create log',
                        'url'=>'Yii::app()->createUrl("tasklog/createLog", array("id"=>$data->id))',
                        'icon'=>'list-alt',
                        ),
                    ),
                ),
        )
    )
);
?>
</div>
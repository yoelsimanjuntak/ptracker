<?php
/* @var $this ProjectController */
/* @var $dataProvider CActiveDataProvider */
//$task = Task::model()->count(array('condition'=>'project_id='.$model->id));
//$projectworker = TaskAssignment::model()->count(array('condition'=>'project_id='.$model->id));
$this->widget('booster.widgets.TbBreadcrumbs', array(
    'links'=>array('My project(s)')
));

$this->menu=array(
	array('label'=>'Add new project', 'url'=>array('project/create')),
	array('label'=>'All projects', 'url'=>array('project/index')),
);
?>

<h2 class="page-header">My project(s)</h2>  
<?php $this->widget('booster.widgets.TbGridView',
    array(
        'id' => 'project-myproject',
        'type'=>'striped condensed hover',
        'dataProvider' => $dataProvider,
        'columns' => array(
            array(
                    'header' => 'No',
                    'value' => '$this->grid->dataProvider->pagination->currentPage*$this->grid->dataProvider->pagination->pageSize + $row+1',
                ),
            'name',
            array(
                    'header'=>'Start date',
                    'value'=> 'date(\'d-m-Y\', strtotime($data->start_date))',
                    'htmlOptions'=>array('width'=>140),
                ),
            array(
                    'header'=>'Due date',
                    'value'=> 'date(\'d-m-Y\', strtotime($data->due_date))',
                    'htmlOptions'=>array('width'=>140),
                ),
            array(
                    'header' => 'Status',
                    'type'=>'raw',
                    'value' => '$data->status=="Upcoming" ? Yii::app()->controller->widget(\'booster.widgets.TbLabel\', array(\'context\' => \'primary\',\'label\' => \'Upcoming\'), true) : '
                                . '($data->status=="Active" ? Yii::app()->controller->widget(\'booster.widgets.TbLabel\', array(\'context\' => \'success\',\'label\' => \'Active\'), true) : '
                                . 'Yii::app()->controller->widget(\'booster.widgets.TbLabel\', array(\'context\' => \'danger\',\'label\' => \'Expired\'), true))'
                ),
            array(
                    'header' => 'Progress',
                    'type' => 'raw',
                    'value' => 'Task::model()->count(array(\'condition\'=>\'project_id=\'.$data->id.\' AND has_parent=0 AND deleted=0\')) == 0 ? "0 %" : CHtml::encode(sprintf("%.2f", Task::model()->count(array(\'condition\'=>\'project_id=\'.$data->id.\' AND has_parent=0 AND deleted=0 AND status="Complete"\'))/Task::model()->count(array(\'condition\'=>\'project_id=\'.$data->id.\' AND has_parent=0 AND deleted=0\'))*100))." %"'
            ),
            array(
                        'header'=>'Options',
                        'headerHtmlOptions'=>array(
                                    'style' => 'text-align: left;',
                                ),
			'class'=>'booster.widgets.TbButtonColumn',
                        'htmlOptions'=>array(
                            'width'=>100,
                        )
		),
        )
    )
);
?>
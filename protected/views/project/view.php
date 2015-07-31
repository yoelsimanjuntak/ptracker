<?php
/* @var $this ProjectController */
/* @var $model Project */

// Get member from creator id
$project_owner = Member::model()->find(array('condition'=>'account_id='.$model->creator->id));

// Get progress of the project
$task = Task::model()->count(array('condition'=>'project_id='.$model->id.' AND deleted=0 and has_parent=0'));
$completedtask = Task::model()->count(array('condition'=>'project_id='.$model->id.' AND status="Complete" AND deleted=0 and has_parent=0'));

$this->widget('booster.widgets.TbBreadcrumbs', array(
    'links'=>array('Projects'=>array('project/index'), $model->name)
));

$this->menu=array(
	array('label'=>'See my projects', 'url'=>array('project/myProjects')),
        array('label'=>'See all projects', 'url'=>array('project/index')),
        array('label'=>'Update this project', 'url'=>array('project/update', 'id'=>$model->id)),
	array('label'=>'Add task', 'url'=>array('task/create', 'id'=>$model->id)),
);
?>
<h2 class="page-header">Project Detail : <?php echo $model->name ?></h2>
<?php $this->widget('booster.widgets.TbDetailView', array(
        'id'=>'project-detail',
	'data'=>$model,
	'attributes'=>array(
                array(
                    'label'=>'Project Owner',
                    'value'=>$project_owner->name,
                ),
                array(
                    'label'=>'Department',
                    'value'=>$project_owner->department,
                ),
                'description',
		array(
                    'label'=>'Start date',
                    'value'=> date('d-m-Y', strtotime($model->start_date)),
                ),
		array(
                    'label'=>'Due date',
                    'value'=> date('d-m-Y', strtotime($model->due_date)),
                ),
	),
)); ?><hr>

<!-- Display current status -->
<h4>
    <b>Current Status :</b>
    <?php $model->status=="Upcoming" ? $this->widget('booster.widgets.TbLabel', array('context' => 'primary','label' => 'Upcoming')) : 
                                    ($model->status=="Active" ? $this->widget('booster.widgets.TbLabel', array('context' => 'success','label' => 'Active')) : 
                                    $this->widget('booster.widgets.TbLabel', array('context' => 'danger','label' => 'Expired')))
    ?>
</h4><hr>

<!-- Display current progress -->
<h4>
    <b>Current Progress : <?php if($task==0) echo 0; else echo sprintf("%.2f",($completedtask/$task)*100); ?>%</b> <i><?php echo '('.$completedtask.' of '.$task.' task completed)' ?></i>
    <?php 
        $this->widget(
        'booster.widgets.TbProgress',
        array(
            'context' => $completedtask==0 ? 'danger' : ($completedtask==$task ? 'success' : 'primary'), // 'success', 'info', 'warning', or 'danger'
            'striped' => true,
            'animated' => true,
            'percent' => $completedtask==0 ? 1 : ($completedtask==$task ? 100 : ($completedtask/$task*100)),
        ));
    ?>
</h4>

<div class="span-24"><hr>
<i class="glyphicon glyphicon-tasks"></i><b> Task List in this project:
<a class="right" href="javascript:window.open('timetable/<?php echo $model->id; ?>','JSGANTT','width=1244,height=420')">View timetable</a></b>
<?php $this->widget('booster.widgets.TbGridView',
    array(
        'id' => 'project-tasklist',
        'dataProvider' => $tasks,
        'type'=>'striped bordered condensed hover',
        'columns' => array(
            array(
                    'header' => 'No',
                    'value' => '$this->grid->dataProvider->pagination->currentPage*$this->grid->dataProvider->pagination->pageSize + $row+1',
                    'htmlOptions'=>array('width'=>40),
                ),
            'title',
            array(
                    'header' => 'Assigned to',
                    'value'=> 'TaskAssignment::model()->find(array(\'condition\'=>\'task_id=\'.$data->id)) ? TaskAssignment::model()->find(array(\'condition\'=>\'task_id=\'.$data->id))->member->username : CHtml::encode(\'-\')',
                    'htmlOptions'=>array('width'=>140),
                ),
            array(
                    'header'=>'Start date',
                    'value'=> 'date(\'d-m-Y\', strtotime($data->start_date))',
                    'htmlOptions'=>array('width'=>100),
                ),
            array(
                    'header'=>'Due date',
                    'value'=> 'date(\'d-m-Y\', strtotime($data->due_date))',
                    'htmlOptions'=>array('width'=>100),
                ),
            array(
                'header' => 'Status',
                'value' => '$data->status',
                 'htmlOptions'=>array('width'=>140),
            ),
            array(
			'class'=>'booster.widgets.TbButtonColumn',
                        'header'=>'Options',
                        'template'=>'{view} {update} {delete} {check} {uncheck} {viewlog}',
                        'headerHtmlOptions'=>array(
                            'style' => 'text-align: left;',
                        ),
                        'htmlOptions'=>array(
                            'width'=>100,
                            'style' => 'align:left',
                        ),
                        'buttons'=>array(
                            'view'=>array(
                                'url'=>'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                            ),
                            'update'=>array(
                                'url'=>'Yii::app()->createUrl("task/update", array("id"=>$data->id))',
                            ),
                            'delete'=>array(
                                'url'=>'Yii::app()->createUrl("task/delete", array("id"=>$data->id))',
                            ),
                            'check'=>array(
                                'icon'=>'ok',
                                'label'=>'Check task',
                                'visible'=>'$data->status == "Complete" ? false : true',
                                'url'=>'Yii::app()->createUrl("task/check", array("id"=>$data->id))',
                            ),
                            'uncheck'=>array(
                                'icon'=>'remove',
                                'label'=>'Uncheck task',
                                'visible'=>'$data->status == "Complete" ? true : false',
                                'url'=>'Yii::app()->createUrl("task/uncheck", array("id"=>$data->id))',
                            ),
                            'viewlog'=>array(
                                'icon'=>'list-alt',
                                'label'=>'View log activity',
                                'url'=>'Yii::app()->createUrl("taskLog/viewlog", array("id"=>$data->id))',
                            ),
                        )
		),
        )
    )
);
?>
</div>

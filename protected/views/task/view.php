<?php
/* @var $this TaskController */
/* @var $model Task */

// Making task array
$task = $model;
$links = array();
$links = array_merge(array($task->title), $links);
while($task->has_parent) {
    $task = Task::model()->findByPk($task->parent_id);
    $links = array_merge(array($task->title=>array('task/view', 'id'=>$task->id)), $links);
}
$links = array_merge(array($model->project->name=>array('project/view', 'id'=>$model->project_id)), $links);
$links = array_merge(array('Projects'=>array('project/index')), $links);

// Displaying breadcrumbs
$this->widget('booster.widgets.TbBreadcrumbs', array(
    'links'=>$links
));

$this->menu=array(
	array('label'=>'See my projects', 'url'=>array('project/myProjects')),
        array('label'=>'See all projects', 'url'=>array('project/index')),
	array('label'=>'Add task', 'url'=>array('task/create', 'id'=>$model->project_id)),
        array('label'=>'Update task', 'url'=>array('task/update', 'id'=>$model->id)),
);
?>
<h2 class="page-header">Task Detail : <?php echo $model->title ?></h2>
<?php $this->widget('booster.widgets.TbDetailView', array(
        'id'=>'task-detail',
	'data'=>$model,
	'attributes'=>array(
		'project.name',
                array(
                    'label'=>'Parent Task',
                    'value'=>$model->has_parent == 0 ? '-' : $model->parent->title,
                ),
                array(
                    'label'=>'Creator',
                    'value'=>$model->creator->username,
                ),
                array(
                        'label'=>'Assigned to',
                        'value'=> TaskAssignment::model()->find(array('condition'=>'task_id='.$model->id)) ? TaskAssignment::model()->find(array('condition'=>'task_id='.$model->id))->member->username : '-',
                    ),
		array(
                    'label'=>'Start date',
                    'value'=> date('d-m-Y', strtotime($model->start_date)),
                ),
		array(
                    'label'=>'Due date',
                    'value'=> date('d-m-Y', strtotime($model->due_date)),
                ),
                array(
                        'label'=>'Status',
                        'value'=> $model->status,
                    ),
	),
)); ?>

<!-- Display current status -->
<!--<h4>
    <b>Current Status :</b>
    <?php /*$model->status=="New" ? $this->widget('booster.widgets.TbLabel', array('context' => 'default','label' => 'NEW')) : 
                                    ($model->status=="On progress" ? $this->widget('booster.widgets.TbLabel', array('context' => 'primary','label' => 'ON PROGRESS')) : 
                                    $this->widget('booster.widgets.TbLabel', array('context' => 'success','label' => 'COMPLETE')))*/
    ?>
</h4>-->

<div class="span-24"><hr>
<!-- List of subtask(s) -->
<i class="glyphicon glyphicon-th-list"></i><b> List of Subtask(s) : <div class="right"><?php echo CHtml::link("(+) Add subtask", Yii::app()->createUrl("task/createChild", array("id"=>$model->id))); ?></div></b>

<?php
    // Retrieve the data of subtask
    $childtask = new CActiveDataProvider('Task', array('criteria'=>array('condition'=>'parent_id='.$model->id.' AND deleted=0')));
?>
    
<!-- Invoke Alert-->
<?php $this->widget('booster.widgets.TbAlert', array(
    'fade' => true,
    'closeText' => '&times;', // false equals no close link
    'events' => array(),
    'htmlOptions' => array(),
    'userComponentId' => 'user',
    'alerts' => array( // configurations per alert type
        // success, info, warning, error or danger
        'success' => array('closeText' => '&times;'),
        'info', // you don't need to specify full config
        'warning',
        'error'
    ),
));
?>

<?php $this->widget('booster.widgets.TbGridView',
    array(
        'id' => 'childtask-list',
        'dataProvider' => $childtask,
        'ajaxUpdate'=>false,
        'type'=>'striped bordered condensed hover',
        'columns' => array(
            //'id',
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
                            'style' => 'text-align:left;',
                        ),
                        'htmlOptions'=>array(
                            'style' => 'align:left',
                            'width' => 100,
                        ),
                        'buttons'=>array(
                            'update'=>array(
                                'url'=>'Yii::app()->createUrl("task/update", array("id"=>$data->id))',
                            ),
                            'delete'=>array(
                                'url'=>'Yii::app()->createUrl("task/delete", array("id"=>$data->id))',
                            ),
                            'check'=>array(
                                'icon'=>'ok',
                                'label'=>'Checklist task',
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
//            array(
//                    'header' => 'Completion',
//                    'type' => 'raw',
//                    //'visible' => $data->status == "Complete" ? false : true,
//                    'value' => 'CHtml::link("Complete", Yii::app()->createUrl("subtask/complete", array("id"=>$data->id)))'
//                ),
        )
    )
);
?>
<!--</div>-->
</div>
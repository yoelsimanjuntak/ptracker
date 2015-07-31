<?php
// Making task array
$task = $model;
$links = array();
$links = array_merge(array('Log activity'), $links);
$links = array_merge(array($task->title=>array('task/view', 'id'=>$task->id)), $links);
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
?>
<h2 class="page-header">Log activity</h2>
<?php $form=$this->beginWidget('booster.widgets.TbActiveForm', array(
	'id'=>'tasklog-filter',
	'type'=>'inline',
        'htmlOptions'=>array('class'=>'well'),
)); ?>

<?php 
echo $form->datePickerGroup($tasklog, 'date',array(
    'widgetOptions' => array(
        'options' => array(
            'language' => 'en',
            'format' => 'yyyy-mm-dd'
            ),
        ),
    'wrapperHtmlOptions' => array(
        'class' => 'col-sm-5',
        ),
    )); 
?>

<?php $this->widget('booster.widgets.TbButton', array(
    'buttonType'=>'submit', 
    'context'=>'primary', 
    'label'=>'Search',
    )); 
?>
<?php $this->endWidget(); ?>

<?php $this->widget('booster.widgets.TbGridView',
    array(
        'id' => 'log-activity',
        'type'=>'striped bordered condensed hover',
        'dataProvider' => $dataProvider,
        'columns' => array(
            array(
                    'header' => 'No',
                    'value' => '$this->grid->dataProvider->pagination->currentPage*$this->grid->dataProvider->pagination->pageSize + $row+1',
                    'htmlOptions'=>array('width'=>40),
                ),
            array(
                    'header' => 'Type',
                    'value'=> '$data->type',
                    'htmlOptions'=>array('width'=>100),
                ),
            array(
                    'header' => 'Task',
                    'value'=> '$data->type == "Non-project" ? $data->task_title : $data->taskAssignment->task->title',
                ),
            array(
                    'header'=>'Date',
                    'value'=> 'date(\'d-m-Y\', strtotime($data->date))',
                    'htmlOptions'=>array('width'=>100),
                ),
        )
    )
);
?>
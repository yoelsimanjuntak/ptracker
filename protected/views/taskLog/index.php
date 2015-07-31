<?php
/* @var $this TaskLogController */
/* @var $dataProvider CActiveDataProvider */
$this->widget('booster.widgets.TbBreadcrumbs', array(
    'links'=>array('Log activity')
));

$this->menu=array(
        array('label'=>'View Task(s)', 'url'=>array('task/mytask')),
	array('label'=>'Create log', 'url'=>array('tasklog/create')),
);
?>

<h2 class="page-header">Log activity</h2>
<?php $form=$this->beginWidget('booster.widgets.TbActiveForm', array(
	'id'=>'tasklog-filter',
	'type'=>'inline',
        'htmlOptions'=>array('class'=>'well'),
)); ?>

<?php 
echo $form->dropDownListGroup($model, 'type', array(
    'widgetOptions' => array(
        'data' => array('Project'=>'Project', 'Non-project'=>'Non-project'),
        'htmlOptions' => array('id'=>'type', 'empty'=>'- Type -', 'style'=>'width:220px;',),
        ),
    ));
?>

<?php 
echo $form->datePickerGroup($model, 'date',array(
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
            array(
                'class'=>'booster.widgets.TbButtonColumn',
                'header'=>'Options',
                'headerHtmlOptions'=>array(
                    'style' => 'text-align: center;',
                    ),
                'template'=>'{view} {update} {delete}',
                'htmlOptions'=>array(
                    'width'=>80,
                    'style'=>'text-align:center;'
                    ),
                'buttons'=>array(
                    'view'=>array(
                        'url'=>'$data->id',
                        'click'=>'js:function() {
                                    $("#tasklog-detail-header").html("Log Detail");
                                    $("#tasklog-detail-body").load("'.Yii::app()->createUrl('tasklog/view').'/"+$(this).attr("href")+"?asModal=true");
                                    $("#tasklog-detail").modal();
                                    return false;
                                }'
                        )
                    ),
		),
        )
    )
);
?>

<?php $this->beginWidget('booster.widgets.TbModal',array('id' => 'tasklog-detail')); ?>
            <div class="modal-header">
                <a class="close" data-dismiss="modal">&times;</a>
                <h4 id="tasklog-detail-header">Modal header</h4>
            </div>
            
            <div class="modal-body" id="tasklog-detail-body"></div>
            
            <div class="modal-footer">
                <?php $this->widget('booster.widgets.TbButton',
                        array(
                            'label' => 'Close',
                            'url' => '#',
                            'htmlOptions' => array('data-dismiss' => 'modal'),
                        )
                ); ?>
            </div>
                
<?php $this->endWidget(); ?>

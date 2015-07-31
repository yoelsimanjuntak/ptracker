<?php 
$project_owner = Member::model()->find(array('condition'=>'account_id='.$model->creator->id));
$this->widget('booster.widgets.TbDetailView', array(
        'id'=>'event-detail',
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
                'status',
		'start_date',
		'due_date',
	),
)); 
//echo $model->status;
?>
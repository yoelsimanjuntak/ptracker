<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<?php $this->beginWidget(
    'booster.widgets.TbJumbotron',
    array(
        'heading' => 'Project Tracker Application',
    )
); ?>
 
    <p>
        This is a web-based application to handle projects under Wilmar Group Plantation.
    </p>
    <p>
        This application is used by project managers and staff. 
        For those who have not been enrolled into this application can contact the administrator.
    </p>
    
 
    <p><?php $this->widget(
            'booster.widgets.TbButton',
            array(
                'context' => 'primary',
                'size' => 'large',
                'label' => 'Learn more',
            )
        ); ?></p>
 
<?php $this->endWidget(); ?>

    <?php $this->beginWidget(
    'booster.widgets.TbModal',
    array('id' => 'myModal')
); ?>
 
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>Modal header</h4>
    </div>
 
    <div class="modal-body">
        <p>One fine body...</p>
    </div>
 
    <div class="modal-footer">
        <?php $this->widget(
            'booster.widgets.TbButton',
            array(
                'context' => 'primary',
                'label' => 'Save changes',
                'url' => '#',
                'htmlOptions' => array('data-dismiss' => 'modal'),
            )
        ); ?>
        <?php $this->widget(
            'booster.widgets.TbButton',
            array(
                'label' => 'Close',
                'url' => '#',
                'htmlOptions' => array('data-dismiss' => 'modal'),
            )
        ); ?>
    </div>
 
<?php $this->endWidget(); ?>
<?php $this->widget(
    'booster.widgets.TbButton',
    array(
        'label' => 'Click me',
        'context' => 'primary',
        'htmlOptions' => array(
            'data-toggle' => 'modal',
            'data-target' => '#myModal',
        ),
    )
);
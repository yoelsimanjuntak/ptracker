<?php

class TaskLogController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'users'=>array('@'),
			),
			array('deny'),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
            if($this->loadModel($id)->deleted==1) {
                throw new CHttpException(403, 'The page you are requested are invalid');
            }
            
            if (@$_GET['asModal'] == true) {
                $model = Tasklog::model()->findByPk($id);
                //$this->renderPartial('_view',array('model'=>$model),false,true);
                echo $model->id;
            }
	}
        
        public function actionViewlog($id) {
            $model = Task::model()->findByPk($id);
            $tasklog = new TaskLog;
            
            if(isset($_POST['TaskLog'])) {
                if(($_POST['TaskLog']['date'] != null)) {
                    $tasklog->date = $_POST['TaskLog']['date'];
                    $dataProvider = new CActiveDataProvider('TaskLog', array(
                        'criteria'=>array(
                        'with' => 'taskAssignment.task',
                        'condition'=>'taskAssignment.task_id='.$id.' AND t.date="'.$_POST['TaskLog']['date'].'" AND t.deleted=0',
                        )
                    ));
                    $this->render('viewlog', array('dataProvider'=>$dataProvider, 'tasklog'=>$tasklog, 'model'=>$model));
                }
                else {
                    $dataProvider = new CActiveDataProvider('TaskLog', array(
                        'criteria'=>array(
                            'with' => 'taskAssignment.task',
                            'condition'=>'taskAssignment.task_id='.$id.' AND t.deleted=0',
                            )
                        ));
                    $this->render('viewlog', array('dataProvider'=>$dataProvider, 'tasklog'=>$tasklog, 'model'=>$model));
                }
            }
            else {
                $dataProvider = new CActiveDataProvider('TaskLog', array(
                    'criteria'=>array(
                        'with' => 'taskAssignment.task',
                        'condition'=>'taskAssignment.task_id='.$id.' AND t.deleted=0',
                        )
                    ));
                $this->render('viewlog', array('dataProvider'=>$dataProvider, 'tasklog'=>$tasklog, 'model'=>$model));
            }
        }

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
            // Get task_assignment for this user
            // $assignments = TaskAssignment::model()->with('task')->findAll(array('condition'=>'member_id='.Yii::app()->user->id.' AND t.deleted=0 AND task.status != "Complete"'));
            
            // Create new TaskLog
            $model = new TaskLog;
            $model->date = date('Y-m-d');
            $model->member_id = Yii::app()->user->id;
            
            if(isset($_POST['TaskLog'])) {
                $model->attributes = $_POST['TaskLog'];
                
                if($model->type == "Non-project") {
                    $model->task_assignment_id = null;
                    if($model->save()) {
                        $this->redirect(array('index'));
                    }
                }
                
                else if($model->type == "Project") {
                    // Check wheter log already exist
                    $check_log = TaskLog::model()->find(array('condition'=>
                        'member_id='.Yii::app()->user->id.' AND task_assignment_id='.$model->task_assignment_id.' AND date="'.date('Y-m-d').'" AND deleted=0'));
                    if($check_log) {
                        throw new CHttpException(403, 'You have already create log activity for this task today');
                    }
                    else {
                        if($model->save()) {
                            $this->redirect(array('index'));
                        }
                    }
                }
            }
            $this->render('create', array('model'=>$model));
	}
        
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['TaskLog']))
		{
                    $model->attributes=$_POST['TaskLog'];
                    
                    if($model->type == "Non-project") {
                        $model->task_assignment_id = null;
                        if($model->save()) {
                            $this->redirect(array('index'));
                        }
                    }
                    
                    else if($model->type == "Project") {
                        // Validate task assignment
                        if($model->task_assignment_id == null) {
                            $model->addErrors(array('task_assignment_id'=>'Task cannot be blank.'));
                            $this->render('update',array('model'=>$model,));
                        }
                        
                        // Check wheter log already exists
                        $check_log = TaskLog::model()->find(array('condition'=>
                                'member_id='.Yii::app()->user->id.' AND task_assignment_id='.$model->task_assignment_id.' AND date="'.date('Y-m-d').'" AND deleted=0'));
                        
                        if($check_log) {
                            throw new CHttpException(403, 'You have already create log activity for this task today');
                        }
                        else {
                            if($model->save()) {
                                $this->redirect(array('index'));
                            }
                        }
                    }
                    
		}

		$this->render('update',array('model'=>$model,));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
            if($this->loadModel($id)->deleted==1) {
                throw new CHttpException(403, 'The page you are requested are invalid');
            }
            
            $model = $this->loadModel($id);
            $model->deleted = 1;
            $model->save();
            

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	// List of log activity
	public function actionIndex()
	{
            $model = new TaskLog;
            if(isset($_POST['TaskLog'])) {
                if(($_POST['TaskLog']['date'] != null) && ($_POST['TaskLog']['type'] != null)) {
                    $model->type = $_POST['TaskLog']['type'];
                    $model->date = $_POST['TaskLog']['date'];
                    $criteria = new CDbCriteria;
                    $criteria->condition = 't.member_id='.Yii::app()->user->id.' '
                            . ' AND type="'.$_POST['TaskLog']['type'].'" AND date="'.$_POST['TaskLog']['date'].'" AND t.deleted=0';

                    $dataProvider = new CActiveDataProvider('TaskLog', array('criteria'=>$criteria));
                    $this->render('index',array('dataProvider'=>$dataProvider, 'model'=>$model,));
                }
                else if(($_POST['TaskLog']['type'] != null)) {
                    $model->type = $_POST['TaskLog']['type'];
                    $criteria = new CDbCriteria;
                    $criteria->condition = 't.member_id='.Yii::app()->user->id.' '
                            . ' AND type="'.$_POST['TaskLog']['type'].'" AND t.deleted=0';

                    $dataProvider = new CActiveDataProvider('TaskLog', array('criteria'=>$criteria));
                    $this->render('index',array('dataProvider'=>$dataProvider, 'model'=>$model,));
                }
                else if(($_POST['TaskLog']['date'] != null)) {
                    $model->date = $_POST['TaskLog']['date'];
                    $criteria = new CDbCriteria;
                    $criteria->condition = 't.member_id='.Yii::app()->user->id.' '
                            . ' AND date="'.$_POST['TaskLog']['date'].'" AND t.deleted=0';

                    $dataProvider = new CActiveDataProvider('TaskLog', array('criteria'=>$criteria));
                    $this->render('index',array('dataProvider'=>$dataProvider, 'model'=>$model,));
                }
                else {
                    $criteria = new CDbCriteria;
                    $criteria->condition = 't.member_id='.Yii::app()->user->id.' '
                            . ' AND t.deleted=0';

                    $dataProvider = new CActiveDataProvider('TaskLog', array('criteria'=>$criteria));
                    $this->render('index',array('dataProvider'=>$dataProvider, 'model'=>$model,));
                }
            }
            else {
                $criteria = new CDbCriteria;
                $criteria->condition = 't.member_id='.Yii::app()->user->id.' '
                        . ' AND t.deleted=0';

                $dataProvider = new CActiveDataProvider('TaskLog', array('criteria'=>$criteria));
                $this->render('index',array('dataProvider'=>$dataProvider, 'model'=>$model,));
            }
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new TaskLog('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TaskLog']))
			$model->attributes=$_GET['TaskLog'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return TaskLog the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=TaskLog::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param TaskLog $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='task-log-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}

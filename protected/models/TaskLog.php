<?php

/**
 * This is the model class for table "t_task_log".
 *
 * The followings are the available columns in table 't_task_log':
 * @property integer $id
 * @property string $type
 * @property integer $member_id
 * @property integer $task_assignment_id
 * @property string $task_title
 * @property string $description
 * @property string $date
 * @property integer $deleted
 * @property string $timestamp
 *
 * The followings are the available model relations:
 * @property TTaskAssignment $taskAssignment
 */
class TaskLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TaskLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_task_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, date', 'required'),
			array('task_assignment_id, deleted', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>25),
			array('task_title', 'length', 'max'=>64),
                        array('date', 'date', 'format'=>'yyyy-M-d'),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, task_assignment_id, task_title, description, date, deleted, timestamp', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'taskAssignment' => array(self::BELONGS_TO, 'TaskAssignment', 'task_assignment_id'),
                        'member' => array(self::BELONGS_TO, 'Account', 'member_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
                        'member_id' => 'Staff ID',
			'task_assignment_id' => 'Task',
			'task_title' => 'Task Title',
			'description' => 'Description',
			'date' => 'Date',
			'deleted' => 'Deleted',
			'timestamp' => 'Timestamp',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('type',$this->type,true);
                $criteria->compare('member_id',$this->member_id,true);
		$criteria->compare('task_assignment_id',$this->task_assignment_id);
		$criteria->compare('task_title',$this->task_title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('timestamp',$this->timestamp,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
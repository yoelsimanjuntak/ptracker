<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jsgantt.css"/>
    <script language="javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/css/jsgantt.js"></script>
</head>

<body>
<div style="position:relative" class="gantt" id="GanttChartDIV"></div>
<script>

  var g = new JSGantt.GanttChart('g',document.getElementById('GanttChartDIV'), 'day');
  g.setShowRes(1); // Show/Hide Responsible (0/1)
  g.setShowDur(1); // Show/Hide Duration (0/1)
  g.setShowComp(0); // Show/Hide % Complete(0/1)
  g.setCaptionType('Resource');  // Set to Show Caption

  if( g ) {
      <?php
      print_task($tasks);
      
      function print_task($tasks) {
          $id = 1;
          foreach($tasks as $task) {
          $childcount = Task::model()->count(array('condition'=>'parent_id='.$task->id.' AND deleted=0'));
          $assignment = TaskAssignment::model()->find(array('condition'=>'task_id='.$task->id.' AND deleted=0'));
          if($childcount > 0) {
              $childtasks = Task::model()->findAll(array('condition'=>'parent_id='.$task->id.' AND deleted=0'));
              echo 'g.AddTaskItem(new JSGantt.TaskItem("'.$task->id.'", "'.$task->title.'", "'.date('m/d/Y', strtotime($task->start_date)).'", "'.date('m/d/Y', strtotime($task->due_date)).'", "659af7", "#", 0, "'.(isset($assignment) ? $assignment->member->username : '').'", 0, 1, '.($task->has_parent ? $task->parent_id : 0).', 0));';
              print_task($childtasks);
          }
          else {
              echo 'g.AddTaskItem(new JSGantt.TaskItem("'.$task->id.'", "'.$task->title.'", "'.date('m/d/Y', strtotime($task->start_date)).'", "'.date('m/d/Y', strtotime($task->due_date)).'", "659af7", "#", 0, "'.(isset($assignment) ? $assignment->member->username : '').'", 0, 0, '.($task->has_parent ? $task->parent_id : 0).', 0));';
              $log_first = TaskLog::model()->find(array('order'=>'date ASC', 'condition'=>'task_assignment_id='.$assignment->id.' AND t.deleted=0'));
              $log_last = TaskLog::model()->find(array('order'=>'date DESC', 'condition'=>'task_assignment_id='.$assignment->id.' AND t.deleted=0'));
              if($log_first && $log_last) {
                  //echo 'g.AddTaskItem(new JSGantt.TaskItem("001.'.$task->id.'", "'.$task->title.' (REAL)", "'.date('m/d/Y', strtotime($log_first->date)).'", "'.date('m/d/Y', strtotime($log_last->date)).'", "fe6e6e", "#", 0, "'.(isset($assignment) ? $assignment->member->username : '').'", 0, 0, '.($task->has_parent ? $task->parent_id : 0).', 0));';
              }
          }
      }
      }
      ?>
    g.Draw();	
    g.DrawDependencies();


  }
  else
  {
    alert("not defined");
  }

</script>
</html>
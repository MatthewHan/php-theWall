<?php
session_start();
require("connection.php");
if(!isset($_SESSION['logged_user']))
{
  Header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>The Wall</title>
  <link rel="stylesheet" href="style.css">  
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</head>
<body>
  <div id="header">
    <h1>CodingDojo Wall</h1>
    <?php if(isset($_SESSION['success']['login'])){?> 
    <span class="help-block alert alert-success"><?= $_SESSION['success']['login'] ?></span> 
    <?php
    unset($_SESSION['success']);
    } ?>
    <div class = "pull-right">
      <span>Welcome <?= $_SESSION['logged_user']['first_name'] ?> </span>
      <a href="process.php?id=logout"> Log Off</a>
    </div>
  </div>
  <div class="post">
    <form action="process.php" method="post" role="form" class="facebook-share-box">
      <div class="timeline-body">
        <div class="share-form">
          <div class="share">
            <div><textarea name="message" cols="40" rows="10" id="status_message" class="form-control message" style="height: 62px; overflow: hidden;" placeholder="What's on your mind ?"></textarea> </div>
          </div>
        </div>
      </div>
      <div class="timeline-footer clearfix">
        <div class="pull-right">
          <button name="message_post" value="message" id="btn-share" class="btn btn-primary">Post A Message</button>
        </div>
      </div>
    </form>
    <?php if(isset($_SESSION['message_errors'])){?> 
    <span class="help-block alert alert-danger"><?= $_SESSION['message_errors'] ?></span> 
    <?php
    unset($_SESSION['message_errors']);
    } ?>
  </div>
  <div class="wall">
  <?php 
  //Populate Messages
  $retrieveMessages = "SELECT users.first_name, users.last_name, users.id as user_id, messages.id, messages.message, messages.created_at, messages.updated_at
                       FROM messages LEFT JOIN users on users.id = messages.user_id";
  $messages = mysqli_query($connection, $retrieveMessages);
  foreach($messages as $message)
  { ?>
    <ul class = "timeline">
      <li></li>
      <li id ="M<?= $message['id'] ?>" class="post-list">
        <div class="timeline-panel">
          <div class="timeline-header">
            <div class="row">
              <div class="col-xs-8">
                <?= $message['first_name'] ?> <?= $message['last_name'] ?> - <?= date("F j, Y g:i a" ,strtotime($message['created_at'])) ?>
              </div>
              <?php if($_SESSION['logged_user']['id']==$message['user_id'])
              {
              ?>
              <div class="col-xs-1 pull-right">
                <form action="process.php" method="post">
                  <input type="hidden" name="message_id" value = "<?= $message['id'] ?>">
                  <button class="btn btn-danger" name="delete_message" value="delete">Delete</button>
                </form>
              </div>
              <?php
              }
              ?>
            </div>
          </div>
          <div class="timeline-heading">                 
          </div>
          <div class="timeline-body">
            <p><?= $message['message'] ?></p>
          </div>
          <div class="timeline-footer">
            <button class = "btn btn-link" data-toggle="collapse" data-target="#collapse<?= $message['id'] ?>">Comments</button>
          </div>
        </div>
      </li>
    </ul>
    <div id="collapse<?= $message['id'] ?>" class="collapse"> 
     <?php
    //Populate Comments
    $retrieveComments = "SELECT users.first_name, users.last_name, users.id as user_id, messages.id as messages_id, comments.id as comment_id, comments.comment, comments.created_at
                         FROM comments JOIN users on users.id = comments.user_id
                              JOIN messages on messages.id = comments.message_id
                         WHERE messages.id = {$message['id']}";
    $comments = mysqli_query($connection,$retrieveComments);
    foreach($comments as $comment)
    { ?>
      <ul class = "timeline">
        <li></li>
        <li id ="C<?= $comment['comment_id'] ?>" class="post-list">
          <div class="timeline-comment">
            <div class="timeline-header">
              <div class="row">
                <div class="col-xs-6">
                  <?= $comment['first_name'] ?> <?= $comment['last_name'] ?> - <?= date("F j, Y g:i a" ,strtotime($comment['created_at'])) ?>
                </div>
                <?php if($_SESSION['logged_user']['id']==$comment['user_id'])
                {
                ?>
                <div class="col-xs-1 pull-right">
                  <form action="process.php" method="post">
                    <input type="hidden" name="comment_id" value = "<?= $comment['comment_id'] ?>">
                    <button class="btn btn-danger" name="delete_comment" value="delete">Delete</button>
                  </form>
                </div>
                <?php
                }
                ?>
              </div>
            </div>
            <div class="timeline-heading">                 
            </div>
            <div class="timeline-body">
              <p><?= $comment['comment'] ?></p>
            </div>
          </div>
        </li>
      </ul>
    <?php
    }
    ?>
    </div>
    <div class="post">
      <form action="process.php" method="post" role="form" class="facebook-share-box">
        <div class="timeline-body">
          <div class="share-form-comment">
            <div class="share">
              <div><textarea name="comment" cols="35" rows="8" id="comment" class="form-control message" style="height: 62px; overflow: hidden;" placeholder="What's not on your mind?"></textarea> </div>
              <input type="hidden" name = "message_id" value = <?= $message['id'] ?>>
            </div>
          </div>
        </div>
        <div class="timeline-footer-comment clearfix">
          <div class="pull-right">
            <button name="comment_post" value="comment" id="btn-share" class="btn btn-success">Post A Comment</button>
          </div>
        </div>
      </form>
      <?php if(isset($_SESSION['comment_errors'])){?> 
      <span class="help-block alert alert-danger"><?= $_SESSION['comment_errors'] ?></span> 
      <?php
      unset($_SESSION['comment_errors']);
      } ?>
    </div>
  <?php
  }
  ?>
  </div>
</body>
</html>
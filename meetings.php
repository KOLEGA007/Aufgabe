<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  define("APP", true);
  require_once("config.php");
  require_once("meeting.php");
  require_once("utilities.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Meetings ultimate administrator</title>
    <style>
      /* Fast css on table, dirty, but quick */
      /* based on https://www.w3schools.com/cSS/tryit.asp?filename=trycss_table_fancy */
      table {
        border-collapse: collapse;
      }
      table th, table td {
        border: 1px solid #ddd;
        padding: 8px;
      }
      table tr:nth-child(even){
        background-color: #f2f2f2;
      }

      table tr:hover {
        background-color: #ddd;
      }

      table th {
          padding-top: 12px;
          padding-bottom: 12px;
          text-align: left;
          background-color: #4CAF50;
          color: white;
      }
    </style>
  </head>
  <body>
    <h1>Meetings</h1>
    <?php
    $errors = [];
    if(isset( $_POST["id"])) {
      if((int) $_POST["id"] >= 0) {
          $data["id"] = (int)  $_POST["id"];
      }
      else {
        $errors[] = "Id not provided correctly";
      }
      if(isset( $_POST["date"]) && validateDate( $_POST["date"]) && isset($_POST["time"]) && validateDate($_POST["time"], "H:i"))
      {
        $data["date"] =  $_POST["date"] . " " . $_POST["time"];
      }
      else {
        $errors[] = "Date is not properly formated";
      }
      if(isset( $_POST["description"]))
      {
        $data["description"] = filter_var( $_POST["description"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      }
      else {
        $errors[] = "Whooo, something happened to description on the way";
      }
      if(count($errors))
      {
        echo "<ul>";
          foreach($errors as $error)
            echo "<li>$error</li>";
        echo "</ul>";
      }
      else
      {
        $tmp = new Meeting($data);
        $tmp->save();
        echo "<div class=\"alert alert-info\">
          Saved!
        </div>";
      }
    }
    if(isset($_GET["action"]))
    {
      if($_GET["action"] == "delete")
      {
        $id = (int) $_GET["id"];
        Meeting::delete($id);
        echo '<div class="alert alert-info"> Item was removed </div>';
      }
      if($_GET["action"] == "edit")
      {
        $meeting = new Meeting((int)$_GET["id"]);
      }
    }
    ?>
    <h2>Ultimate meeting editor</h2>
    <form method="POST">
      <?php if (isset($meeting)) { ?>
        <a href="meetings.php">Better add fresh new one</a><br />
      <?php } ?>
      <input type="hidden" name="id" value="<?php echo isset($meeting) ? $meeting->id : 0; ?>">
      <div>
        <label for="date">Date:</label><br />
        <i>Standard date input is a mess with no format values. Ill keep it text input. There are JS libraries to solve it</i><br />
        <input type="text" id="date" name="date" value="<?php if(isset($meeting)) echo date("Y-m-d", strtotime($meeting->date)); ?>">
      </div>
      <div>
        <label for="date">Time:</label>
        <i>Same for time</i><br />
        <input type="text" id="time" name="time" value="<?php if(isset($meeting)) echo date("H:i", strtotime($meeting->date)); ?>">
      </div>
      <div>
      <label for="description">Description:</label><br />
      <textarea maxlength="255" name="description" id="description"><?php if(isset($meeting)) echo $meeting->description;?></textarea>
      </div>
      <button type="submit">Submit</button>
    </form>
    <h2>Scheduled meetings</h2>
    <table>
        <thead>
          <tr>
            <th>
              #
            </th>
            <th>
              Description
            </th>
            <th>
              Date of meeting
            </th>
            <th>
              Edited
            </th>
            <th>
              Created
            </th>
          </tr>
        </thead>
        <?php  foreach (Meeting::getAll() as $meeting) { ?>
        <tr>
          <td>
            <a href="?action=edit&id=<?php echo $meeting->id; ?>">Edit</a> | <a href="?action=delete&id=<?php echo $meeting->id; ?>">Delete</a>
          </td>
          <td>
            <?php echo $meeting->description; ?>
          </td>
          <td>
            <?php echo date(TIMESTAMP_FORMAT, strtotime($meeting->date)); ?>
          </td>
          <td>
            <?php echo $meeting->isEdited() ? date(DATE_FORMAT, strtotime($meeting->updated)) : "Not edited yet"; ?>
          </td>
          <td>
            <?php echo date(TIMESTAMP_FORMAT, strtotime($meeting->created)); ?>
          </td>
        </tr>
        <?php } unset($meeting); ?>
    </table>
  </body>
</html>

<?php
defined("APP") or die("NO DIRECT ACCESS");
/**
* Model for Meeting
**/
class Meeting {
  public $id;
  public $description;
  public $date;
  public $created;
  public $updated;

  /**
  * Creates new instance of class
  * If it gets only ID it searches the db for rest (exception on no result)
  * If array with ID (exception if no id provided)
  * @throws Exception if no real id provided
  * @param int|array $data Id or $data of the instance
  **/
  public function __construct($data) {

    if(is_array($data))
    {
      $this->id = $data["id"];
      $this->description = $data["description"];
      $this->date = $data["date"];
      $this->created = isset($data["created"]) ? $data["created"] : "0000-00-00 00:00:00";
      $this->updated = isset($data["updated"]) ? $data["updated"] : "0000-00-00 00:00:00";
    }
    else {
      if(!is_numeric($data))
        throw new Exception("Error on \$id is not numeric", 1);
      global $db;
      $query = $db->prepare("SELECT * FROM meetings WHERE id=:id");
      $query->execute([":id" => (int) $data]);
      if(!$query->rowCount()) {
        throw new Exception("No item for this ID found!");
      }
      $result = $query->fetch();
      $this->id = $result["id"];
      $this->description = $result["description"];
      $this->date = $result["date"];
      $this->created = $result["created"];
      $this->updated = $result["updated"];
    }
  }
  /**
  * Checks if the object is edited or not
  * @return bool object is edited / not;
  **/
  public function isEdited() {
    return $this->updated !== "0000-00-00 00:00:00";
  }
  /**
  * Saves the object to database. Creates new record if necessary
  **/
  public function save() {
    //SANITAZING INPUT TO BE SURE!
    //Possible information loss here
    echo "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    if($this->id != (int) $this->id)
      throw new Exception("Possible ID integrity problem detected");
    $this->id = (int) $this->id;
    $this->description = filter_var($this->description, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $this->date = date("Y-m-d H:i:s", strtotime($this->date));
    global $db;

    if($this->id == 0) {
      $query = $db->prepare("INSERT INTO meetings VALUES (null, ?, ?, ?, null)");
      $result = $query->execute([$this->description, $this->date, date("Y-m-d H:i:s", time())]);
      $this->id = $db->lastInsertId();
    }
    else
    {
      $this->updated = date("Y-m-d H:i:s", time());
      $query = $db->prepare("UPDATE meetings SET description=:description, date=:date, updated=:updated WHERE id=:id");
      $query->execute([":id" => $this->id, ":date" => $this->date, ":description" => $this->description, ":updated" => $this->updated]);
    }
  }
  /**
  * Read every object from database;
  * @return Meeting[]
  */
  static public function getAll() {
    global $db;
    $query = $db->prepare("SELECT * FROM meetings");
    $query->execute();
    $output = array();
    while($row = $query->fetch())
    {
      $output[] = new Meeting($row);
    }
    return $output;
  }
  /**
  * Delete a record by id
  * @param int $id Id of the record;
  * @return bool Which is always true xD what a mess here!
  */
  static public function delete($id)
  {
    global $db;
    if($id != (int) $id)
      throw new Exception("Possible ID integrity problem detected");
    $id = (int) $id;
    $query = $db->prepare("DELETE FROM meetings WHERE id=:id");
    //Does the ID REALLY EXISTED? NOBODY CHECKED!
    $query->execute([":id" => $id]);
    return true;
  }
}
?>

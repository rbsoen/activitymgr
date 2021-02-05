<?php
# DB object
class DB {
	public $db;
	private $db_name;
	
	public function __construct($db_host,$db_name,$db_user,$db_pass){
		$this->db = new PDO(
			"mysql:host=$db_host;
			charset=utf8;",
			$db_user,
			$db_pass,
			[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
		);
		
		$this->db_name = $db_name;
		$this->init_db();
	}
	
	private function init_db(){
	// Initialize database and table if they don't exist
		$init_q = "
			Create Database If Not Exists `$this->db_name`;
			
			Create Table If Not Exists `$this->db_name`.`activities` (
				id	Int	Primary Key	Auto_Increment,
				time		DateTime 	Default current_timestamp,
				subject		VarChar(64),
				description	VarChar(512)
			);
		";
		$this->db->query($init_q);
	}
	
	public function add_activity($subject, $description){
	// Add one activity with subject and description
		$add_q = "
			Insert Into `$this->db_name`.`activities`
			(`subject`, `description`) Values (:subj, :desc);
		";
		$statement = $this->db->prepare($add_q);
		$statement->execute(['subj'=>$subject, 'desc'=>$description]);
		return $this->db->lastInsertId();
	}
	
	public function list_activities($start = null, $end = null){
	// List either all activities or from a certain point in time
		$list_q = "Select * From `$this->db_name`.`activities` ";
		$args_q = [];
		if ( !is_null($start) && !is_null($end) ){
			$list_q .= 'Where time >= ? And time < ?';
			array_push($args_q, $start, $end);
		} else if ( !is_null($end) ){
			$list_q .= 'Where time < ?';
			array_push($args_q, $end);
		} else if ( !is_null($start) ){
			$list_q .= 'Where time >= ?';
			array_push($args_q, $start);
		}
		
		// Execute generated queries
		$statement = $this->db->prepare($list_q);
		$statement->execute($args_q);
		$fetched = $statement->fetchAll();
		return $fetched;
	}
}
?>

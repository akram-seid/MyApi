<?php
  class DbOperations{

		private $con; 

		function __construct(){

			require_once dirname(__FILE__).'/Dbconnect.php';

			$db = new Dbconnect();

			$this->con = $db->connect();

		}

		/*CRUD -> C -> CREATE */

		public function create_user($username, $email, $pass){
			if($this->isUserExist($username, $email)){
				return 0;
			}
			else
			{
				$password = md5($pass);
				$stmt = $this->con->prepare("INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES (NULL, ?, ?, ?);");
				$stmt->bind_param("sss",$username,$email,$password);

				if($stmt->execute()){
					return 1; 
				}else{
					return 2; 
				}
			}
			}
			
			public function userLogin($table,$username, $pass){
				
				$stmt = $this->con->prepare("SELECT * FROM $table WHERE username =? AND password =? ");
				$stmt->bind_param("ss",$username,$pass);
				$stmt->execute();
				$stmt->store_result(); 
				return $stmt->num_rows > 0; 
			}
			public function getUserByUsername($username){
			$stmt = $this->con->prepare("SELECT * FROM users WHERE username = ?");
			$stmt->bind_param("s",$username);
			$stmt->execute();
			return $stmt->get_result()->fetch_assoc();
		}

			private function isUserExist($username, $email){
				$stmt = $this->con->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
			$stmt->bind_param("ss", $username, $email);
			$stmt->execute(); 
			$stmt->store_result(); 
			return $stmt->num_rows > 0; 
			}
			function getAttendacne($id){
				$query="SELECT * FROM attendance WHERE ".$id."=?";
				$result=mysqli_query($this->con,$query);
				$response = array();
				while($row=mysqli_fetch_array($result)){
				array_push($response,array("Present"=>$row["present"],"Absent"=>$row["absent"]));
				}
				return $response;
			}
			function getCourseOffered($batchId){
				$query="SELECT * FROM courses WHERE ".$id."=?";
				$result=mysqli_query($this->con,$query);
				$response = array();
				while($row=mysqli_fetch_array($result)){
				array_push($response,array("Present"=>$row["present"],"Absent"=>$row["absent"]));
				}
				return $response;
			}
			function getSections($insId){
				$query="SELECT sections.sec_name,sections.sec_id FROM sections INNER JOIN couinstructor ON couinstructor.sec_id=sections.sec_id WHERE couinstructor.ins_id= $insId";
				$rows=mysqli_query($this->con,$query);
				
					$result = array();
				while($row = mysqli_fetch_array($rows)){
 				array_push($result,array('sec'=>$row['sec_name']
  							  ));
							}
					return (array('result'=>$result));
			}
			function getCourses($insId){
				$query="SELECT courses.course_name FROM courses INNER JOIN couinstructor ON couinstructor.course_id=courses.course_id WHERE couinstructor.ins_id= $insId";
				$rows=mysqli_query($this->con,$query);
				
					$result = array();
				while($row = mysqli_fetch_array($rows)){
 				array_push($result,array('cou'=>$row['course_name']
  							  ));
							}
					return (array('result'=>$result));
			}
			function getIns($username){
				$stmt = "SELECT ins_id FROM instructors WHERE username = '$username'";
			$result=mysqli_query($this->con,$stmt);
		
			$row=mysqli_fetch_row($result);
			
			return $row[0]; 

			}
			function getAdmin($username){
				$stmt = "SELECT id FROM admin WHERE username = '$username'";
			$result=mysqli_query($this->con,$stmt);
		
			$row=mysqli_fetch_row($result);
			
			return $row[0]; 

			}
			function getStudent($username){
				$stmt = "SELECT stud_id FROM students WHERE username = '$username'";
			$result=mysqli_query($this->con,$stmt);
		
			$row=mysqli_fetch_row($result);
			
			return $row[0]; 

			}
			
		
		function getAllStudents($insId){
				$query="SELECT students.name,students.roll_id FROM students INNER JOIN sections ON students.sec_id=sections.sec_id WHERE sections.sec_name= '$insId'";
				$rows=mysqli_query($this->con,$query);
				
					$result = array();
				while($row = mysqli_fetch_array($rows)){
 				array_push($result,array('namey'=>$row['name'],'id'=>$row['roll_id']
  							  ));
							}
					return (array('result'=>$result));
			}

  }
	
?>	
    
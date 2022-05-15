<?php
  class DbOperations{

		private $con; 

		function __construct(){

			require_once dirname(__FILE__).'/Dbconnect.php';

			$db = new Dbconnect();

			$this->con = $db->connect();

		}
        public function userLogin($table,$username, $pass){
				
            $stmt = $this->con->prepare("SELECT * FROM $table WHERE username =? AND password =? ");
            $stmt->bind_param("ss",$username,$pass);
            $stmt->execute();
            $stmt->store_result(); 
            return $stmt->num_rows > 0; 
        }
        function getIns($username){
            $stmt = "SELECT ins_id FROM instructors WHERE username = '$username'";
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
        function get_course($instructor){
            $query = "select distinct courses.course_name from courses inner join course_comb On 
            courses.course_id=course_comb.courses_course_id
            where course_comb.instructors_ins_id='$instructor'";
            if ($stmt = $this->con->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($course_name);
                $result = array();
                while ($stmt->fetch()) {
                 //printf("%s\n", $course_name);
                 array_push($result,$course_name);
                    }
                $stmt->close();
                }
                return $result;
        }

        function get_course_id($course){
            $query = "select courses.course_id from courses where courses.course_name = '$course'";


            if ($stmt =$this->con->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($course_id);
                while ($stmt->fetch()) {
                    //printf("%s\n", $course_id);
                }
                $stmt->close();
            }
                    return $course_id;
        } 


        function get_section($course){
            $course_id=$this->get_course_id($course);

            $query = "select distinct sections.sec_name from sections inner join course_comb On
             sections.sec_id = course_comb.sections_sec_id where course_comb.courses_course_id ='$course_id';";
            if ($stmt = $this->con->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($sec_name);
                $result = array();
                while ($stmt->fetch()) {
                 //printf("%s\n", $course_name);
                 array_push($result,$sec_name);
                    }
                $stmt->close();
                }
                return $result;
        }

        function get_studentid($section){
            $query = "select students.id_no from students inner join sections on
            sections_sec_id=sections.sec_id where sections.sec_name='$section';";
            if ($stmt = $this->con->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($id_no);
                $result = array();
                while ($stmt->fetch()) {
                 //printf("%s\n", $course_name);
                 array_push($result,$id_no);
                    }
                $stmt->close();
                }
                return $result;
        }
        function get_sec_id($section){
            
			$stmt = "select sections.sec_id from sections where sections.sec_name = '$section' ;";
			$result=mysqli_query($this->con,$stmt);
		
			$row=mysqli_fetch_row($result);
			
			return $row[0];

        }
        function get_studentname($section){
            $query = "select students.stud_name from students inner join sections on 
            sections_sec_id=sections.sec_id where sections.sec_name='$section';";
            if ($stmt = $this->con->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($stud_name);
                $result = array();
                while ($stmt->fetch()) {
                 //printf("%s\n", $course_name);
                 array_push($result,$stud_name);
                    }
                $stmt->close();
                }
                return $result;
        }

        function get_studentcourses($student_id){
            $query = "select distinct courses.course_name from courses join course_comb on 
            courses.course_id=course_comb.courses_course_id join students 
            on students.sections_sec_id = course_comb.sections_sec_id where students.stud_id='$student_id';";
            if ($stmt = $this->con->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($course_name);
                $result = array();
                while ($stmt->fetch()) {
                 //printf("%s\n", $course_name);
                 array_push($result,$course_name);
                    }
                $stmt->close();
                }
                return $result;
        }
        function get_total_present($stud_id, $stud_course){
            $query = "select count(status) from attendance join students on students.id_no = attendance.students_id_no join session on
             session.ses_id = attendance.session_ses_id join courses on 
             courses.course_id = session.courses_course_id where attendance.status='1'
              and students.stud_id='$stud_id' and courses.course_name='$stud_course';";
              $result=mysqli_query($this->con,$query);
          
              $row=mysqli_fetch_row($result);
              
              return $row[0];
        }

        function get_total_session($stud_id, $stud_course){
            $stmt = "select count(ses_date) from session join students on students.sections_sec_id= session.sections_sec_id 
            join courses on  session.courses_course_id = courses.course_id where students.stud_id='$stud_id' and courses.course_name='$stud_course';";
			$result=mysqli_query($this->con,$stmt);
			$row=mysqli_fetch_row($result);
                $present = $this->get_total_present($stud_id, $stud_course);
                $per = $present ."/". $row[0];
                return $per;
        }
        function get_total_session_roll($id_no, $stud_course){
            $query = "select count(ses_date) from session join students on students.sections_sec_id= session.sections_sec_id 
            join courses on  session.courses_course_id = courses.course_id where students.id_no='$id_no' 
            and courses.course_name='$stud_course';";
                if ($stmt = $this->con->prepare($query)) {
                 $stmt->execute();
                 $stmt->bind_result($date);
                $stmt->close();
                }
                $stud_id=$this->get_student_id($id_no);
                $present = $this->get_total_present($stud_id, $stud_course);
                $per = $present ."/". $date;
                return $per;
        }
        function get_student_id($id_no){
            $query = "SELECT stud_id FROM students WHERE id_no ='$id_no'";
            if ($stmt = $this->con->prepare($query)) {
              $stmt->execute();
             $stmt->bind_result($stud_id);
                 $stmt->close();
                }
                return $stud_id;
        }

        function change_password($table, $user, $old_pass, $new_pass){
            $ins_id=0;
            $stud_id=0;
            $admin_id=0;
            if($table=='instructors'){
                $stmt = $this->con->prepare("SELECT ins_id FROM $table WHERE ins_id =? AND password =? ");
            $stmt->bind_param("ss",$user,$old_pass);
            $stmt->execute();
            $stmt->store_result();
                
                if($stmt->num_rows>0){
                    $query = "UPDATE mydb.instructors SET password ='$new_pass' WHERE ins_id = '$user'";
                if ($stmt = $this->con->prepare($query)) {
                        $stmt->execute();
                     $stmt->close();
                        }
                        $message='1';
                }
                else{
                    $message='0';
                }
            }
            else if($table=='students'){
                $stmt = $this->con->prepare("SELECT stud_id FROM $table WHERE stud_id =? AND password =? ");
            $stmt->bind_param("ss",$user,$old_pass);
                 $stmt->execute();
                 $stmt->store_result();
                
                
                if($stmt->num_rows>0){
                    $query = "UPDATE mydb.students SET password ='$new_pass' WHERE stud_id = '$user'";
                if ($stmt = $this->con->prepare($query)) {
                        $stmt->execute();
                     $stmt->close();
                        }
                        $message='1';
                }
                else{
                    $message='0';
                }
            }
            else if($table=='admin'){
                $stmt = $this->con->prepare("SELECT admin_id FROM $table WHERE admin_id =? AND password =? ");
            $stmt->bind_param("ss",$user,$old_pass);
                 $stmt->execute();
                 $stmt->store_result();                
                if($stmt->num_rows>0){
                    $query = "UPDATE mydb.admins SET password ='$new_pass' WHERE admin_id = '$user'";
                if ($stmt = $this->con->prepare($query)) {
                        $stmt->execute();
                     $stmt->close();
                        }
                        $message='1';
                }
                else{
                    $message='0';
                }
            }
                return $message;
        }

        function insert_all($session, $present, $permission){
            $message='';
                $ses_id=$this->insert_session($session);
            foreach($present as $attendance){
                $stmt = $this->con->prepare("INSERT INTO attendance (status, students_id_no,session_ses_id) VALUES  ( 1, ?, ?);");
            $stmt->bind_param("ss",$attendance,$ses_id);
            $stmt->execute();
            if($stmt->execute()){
                $message= "Attendance is added to database successfully!";
            }else{
                $message= "Something went wrong adding Attendace with permission!"; 
            } 
            }
            foreach($permission as $permit){
                $stmt = $this->con->prepare("INSERT INTO attendance (status, students_id_no,session_ses_id) VALUES  ( 0, ?, ?);");
            $stmt->bind_param("ss",$attendance,$ses_id);
            $stmt->execute();

            if($stmt->execute()){
                $message= "Attendance is added to database successfully!";
            }else{
                $message= "Something went wrong adding permission!"; 
            }   
        }
            return $message;
        }
        function insert($session, $present){
            $ses_id=$this->insert_session($session);
            foreach($present as $attendance){
                $stmt = $this->con->prepare("INSERT INTO attendance (status, students_id_no,session_ses_id) VALUES  ( 1, ?, ?);");
            $stmt->bind_param("ss",$attendance,$ses_id);
            $stmt->execute();
            
				if($stmt->execute()){
					return "Attendance is added to database successfully!";
				}else{
					return "Something went wrong adding attendance!"; 
				}
            }
        }

        function insert_session($session){
            $ses_date=$session[0];
            $ses_course= $this->get_course_id($session[1]);
            $ses_ins = $session[2];
            $ses_section= $this->get_sec_id($session[3]);
            $sql = "insert into session (ses_date,courses_course_id,instructors_ins_id,sections_sec_id) VALUES ($ses_date,$ses_course,$ses_ins,$ses_section);";
            if ($this->con->query($sql) === TRUE) {
                $stmt = "select last_insert_id() from session";
                $result=mysqli_query($this->con,$stmt);
		
			$row=mysqli_fetch_row($result);
			
			    return $row[0];
              } else {
                echo "Error: " . $sql . "<br>" . $this->con->error;
              }
        }
    }
<?php 
// Practical 8: Display Student Data using Pure PHP Arrays (No SQL Database) 
 
$students = [ 
    [ 
        "student_id" => 1, 
        "name" => "Fenil Finava", 
        "email" => "25cs011@charusat.edu.in", 
        "course" => "Computer Science Engineering" 
    ], 
    [ 
        "student_id" => 2, 
        "name" => "Priya", 
        "email" => "priya@gmail.com", 
        "course" => "Computer" 
    ], 
    [ 
        "student_id" => 3, 
        "name" => "Amit", 
        "email" => "amit@gmail.com", 
        "course" => "IT" 
    ], 
    [ 
        "student_id" => 4, 
        "name" => "Neha", 
        "email" => "neha@gmail.com", 
        "course" => "Computer" 
    ] 
]; 
?> 
 
<h2>Student List</h2> 
<?php 
foreach ($students as $student) { 
    echo $student["student_id"] . " - "; 
    echo $student["name"] . " - "; 
    echo $student["email"] . " - "; 
    echo $student["course"] . "<br>"; 
} 
?>

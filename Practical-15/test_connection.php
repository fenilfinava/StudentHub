<?php 
// Practical 8: Data Display Test using Pure PHP Arrays (No SQL Database) 
 
echo "<h3>Testing Data Retrieval using Pure PHP:</h3>"; 
 
// 1. Student Records (PHP Array) 
$students = [ 
    ["student_id" => 1, "name" => "Fenil Finava", "email" => "25cs011@charusat.edu.in", "course" => "Computer Science Engineering"], 
    ["student_id" => 2, "name" => "Priya", "email" => "priya@gmail.com", "course" => "Computer"], 
    ["student_id" => 3, "name" => "Amit", "email" => "amit@gmail.com", "course" => "IT"], 
    ["student_id" => 4, "name" => "Neha", "email" => "neha@gmail.com", "course" => "Computer"] 
]; 
 
echo "<h4>Student Records (Count: " . count($students) . "):</h4>"; 
echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse; width:80%; font-family:Arial, sans-serif;'>"; 
echo "<tr style='background-color:#f2f2f2;'><th>Student ID</th><th>Name</th><th>Email</th><th>Course</th></tr>"; 
foreach ($students as $student) { 
    echo "<tr>"; 
    echo "<td>" . htmlspecialchars($student["student_id"]) . "</td>"; 
    echo "<td>" . htmlspecialchars($student["name"]) . "</td>"; 
    echo "<td>" . htmlspecialchars($student["email"]) . "</td>"; 
    echo "<td>" . htmlspecialchars($student["course"]) . "</td>"; 
    echo "</tr>"; 
} 
echo "</table>"; 
 
// 2. Event Registrations (PHP Array) 
$registrations = [ 
    ["registration_id" => 1, "student_name" => "Fenil Finava", "event_name" => "Hackathon", "venue" => "Lab 301"], 
    ["registration_id" => 2, "student_name" => "Fenil Finava", "event_name" => "Web Workshop", "venue" => "Lab 302"], 
    ["registration_id" => 3, "student_name" => "Priya", "event_name" => "Hackathon", "venue" => "Lab 301"], 
    ["registration_id" => 4, "student_name" => "Amit", "event_name" => "AI Seminar", "venue" => "Auditorium"] 
]; 
 
echo "<br><h4>Event Registrations (Count: " . count($registrations) . "):</h4>"; 
echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse; width:80%; font-family:Arial, sans-serif;'>"; 
echo "<tr style='background-color:#f2f2f2;'><th>Reg ID</th><th>Student Name</th><th>Event Name</th><th>Venue</th></tr>"; 
foreach ($registrations as $reg) { 
    echo "<tr>"; 
    echo "<td>" . htmlspecialchars($reg["registration_id"]) . "</td>"; 
    echo "<td>" . htmlspecialchars($reg["student_name"]) . "</td>"; 
    echo "<td>" . htmlspecialchars($reg["event_name"]) . "</td>"; 
    echo "<td>" . htmlspecialchars($reg["venue"]) . "</td>"; 
    echo "</tr>"; 
} 
echo "</table>"; 
?>

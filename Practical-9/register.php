<!DOCTYPE html> 
<html> 
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration - Practical 9</title> 
    <link rel="stylesheet" href="style.css"> 
</head> 

<body> 

<div class="container"> 

    <h2>User Registration</h2> 

    <form action="process_registration.php" method="POST"> 

        <label>Username:</label> 
        <input  
            type="text"  
            name="username"  
            required 
            minlength="3" 
            maxlength="50" 
            placeholder="e.g. student01"
        > 

        <label>Email:</label> 
        <input  
            type="email"  
            name="email"  
            required 
            placeholder="e.g. student01@gmail.com"
        > 

        <label>Password:</label> 
        <input  
            type="password"  
            name="password"  
            required 
            minlength="6" 
            placeholder="At least 6 characters"
        > 

        <label>Confirm Password:</label> 
        <input  
            type="password"  
            name="confirm_password"  
            required 
            minlength="6" 
            placeholder="Re-enter password"
        > 

        <button type="submit">Register</button> 

        <div class="links">
            <a href="users.php">📋 View Registered Users in MySQL</a>
        </div>

    </form> 

</div> 

</body> 
</html>

Event Management CRUD with Poster Upload (simple version, 5 files)

SETUP (XAMPP): copy this folder to htdocs/event-simple, run schema.sql in phpMyAdmin,
open http://localhost/event-simple/

LIVE-CODING ORDER (type it with the students)
 1. schema.sql + db.php        - table and connection (5 min)
 2. upload.php + upload_demo.php - file upload and validation ALONE, no database (15 min)
 3. index.php: CREATE + list   - call upload_poster(), save only the file name (15 min)
 4. index.php: DELETE + SEARCH - read the file name first, then delete row and file (10 min)
 5. edit.php: UPDATE           - keep old poster if no new file, delete old file after update (10 min)

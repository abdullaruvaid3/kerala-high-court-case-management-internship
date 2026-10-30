# Kerala High Court Case Tracker (Demo)

A localhost demo built with PHP, PostgreSQL, Apache and Ubuntu (WSL).

## Features
- Registration and login with password hashing
- OTP verification (demo code shown on screen)
- Dashboard with case counts, a doughnut chart and upcoming hearings
- Case list with search
- Add case form with status validation

## Setup
1. Install Apache, PHP (with pgsql) and PostgreSQL on Linux.
2. Create the database `court_db` and a user.
3. Create the tables `users`, `otps` and `cases`, and the views `case_summary` and `upcoming_hearings`.
4. Replace `YOUR_PASSWORD` in the PHP files with your database password.
5. Copy the files to `/var/www/html/court`.
6. Open `localhost/court/login.php`.

## Note
Demo only. Uses test data, not real court records.

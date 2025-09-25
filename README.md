# 📘 System Setup Guide

## 1. Install XAMPP
1. Download **XAMPP** from the official website:  
   👉 [https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html)  
2. Install **XAMPP** by following the setup wizard.  

---

## 2. Start Required Services
1. Open **XAMPP Control Panel**.  
2. Click **Start** for:
   - **Apache**
   - **MySQL**

---

## 3. Import Database
1. Open your browser and go to:  
   👉 [http://localhost/phpmyadmin](http://localhost/phpmyadmin)  
2. Create a new database (e.g., `system_db`).  
3. Click **Import**, then select the provided `.sql` file.  
4. Click **Go** to upload the database.  

---

## 4. Install Composer
1. Download **Composer** from:  
   👉 [https://getcomposer.org/download/](https://getcomposer.org/download/)  
2. Run the installer and make sure it integrates with **PHP from XAMPP**  
   (usually located at `C:\xampp\php\php.exe`).  
3. Verify installation by running this command in **Command Prompt**:

   ```bash
   composer -V

5. Install PHPMailer (via Composer)

### Open Command Prompt.

### Navigate to your project folder inside htdocs:

cd C:\xampp\htdocs\[your-system-folder-name]


### Run the following command to install PHPMailer:

composer require phpmailer/phpmailer

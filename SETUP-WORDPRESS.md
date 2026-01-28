# Setting Up WordPress for Cinema Plugin

You have several options to set up WordPress locally on your Windows machine:

## Option 1: Local by Flywheel (Recommended - Easiest)

1. **Download Local**
   - Visit: https://localwp.com/
   - Download and install Local (free)

2. **Create New Site**
   - Open Local and click "Create a new site"
   - Name: "Cinema Website"
   - Choose "Preferred" environment
   - Set WordPress username/password
   - Click "Add Site"

3. **Install Plugin**
   - Click "Open site folder" or go to Admin
   - Copy the `wp-cinema` folder to: `app/public/wp-content/plugins/`
   - Go to WordPress Admin > Plugins
   - Activate "WP Cinema Manager"

## Option 2: Docker Compose (For Developers)

1. **Install Docker Desktop**
   - Download from: https://www.docker.com/products/docker-desktop
   - Install and start Docker Desktop

2. **Use the provided docker-compose.yml**
   - Open PowerShell in `wp-cinema` directory
   - Run: `docker-compose up -d`
   - Wait for containers to start

3. **Access WordPress**
   - Visit: http://localhost:8080
   - Complete WordPress installation
   - Username: admin
   - Password: (choose your own)

4. **Install Plugin**
   - The plugin is automatically mounted
   - Go to Plugins > Activate "WP Cinema Manager"

## Option 3: XAMPP (Traditional Method)

1. **Install XAMPP**
   - Download from: https://www.apachefriends.org/
   - Install XAMPP (includes Apache, MySQL, PHP)

2. **Start Services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL

3. **Download WordPress**
   - Visit: https://wordpress.org/download/
   - Download WordPress ZIP
   - Extract to: `C:\xampp\htdocs\cinema-site`

4. **Create Database**
   - Visit: http://localhost/phpmyadmin
   - Create new database: `cinema_db`

5. **Install WordPress**
   - Visit: http://localhost/cinema-site
   - Follow installation wizard
   - Database name: cinema_db
   - Username: root
   - Password: (leave empty)
   - Host: localhost

6. **Install Plugin**
   - Copy `wp-cinema` folder to: `C:\xampp\htdocs\cinema-site\wp-content\plugins\`
   - Activate in WordPress Admin

## Option 4: Online Hosting (Production)

1. **Get Hosting**
   - Choose a hosting provider (Bluehost, SiteGround, etc.)
   - Most include WordPress auto-installer

2. **Install WordPress**
   - Use cPanel or hosting panel
   - Click "WordPress" installer
   - Follow prompts

3. **Upload Plugin**
   - Via FTP: Upload to `/wp-content/plugins/`
   - Via Admin: Plugins > Add New > Upload Plugin (zip the wp-cinema folder first)

## Quick Start with Docker (Recommended for Testing)

If you have Docker installed, just run:

```powershell
cd C:\Users\oelyazam\Desktop\wp-cinema
docker-compose up -d
```

Then visit http://localhost:8080 and complete the WordPress setup!

## After Installation

1. **Login to WordPress Admin**
   - URL: http://your-site/wp-admin
   - Use your admin credentials

2. **Activate Plugin**
   - Go to Plugins page
   - Find "WP Cinema Manager"
   - Click "Activate"

3. **Start Using**
   - You'll see "Cinema" menu in admin sidebar
   - Click "Cinema > Dashboard" to start
   - Add movies, venues, and showtimes!

## Troubleshooting

- **Port 8080 already in use?** Change it in docker-compose.yml
- **Database connection error?** Check credentials in wp-config.php
- **Plugin not showing?** Make sure folder name is `wp-cinema` in plugins directory
- **Permissions error?** On XAMPP, ensure write permissions on wp-content folder

# Quick Start Guide

## Fastest Way to Test Your Cinema Plugin

### Using Docker (5 minutes setup)

1. **Install Docker Desktop**
   ```
   Download from: https://www.docker.com/products/docker-desktop
   Install and start Docker Desktop
   ```

2. **Open PowerShell in this directory**
   ```powershell
   cd C:\Users\oelyazam\Desktop\wp-cinema
   ```

3. **Start WordPress**
   ```powershell
   docker-compose up -d
   ```

4. **Wait 1-2 minutes, then open browser**
   - WordPress: http://localhost:8080
   - phpMyAdmin: http://localhost:8081 (optional)

5. **Complete WordPress Installation**
   - Choose language: English
   - Site Title: "My Cinema"
   - Username: admin
   - Password: (choose strong password)
   - Email: your@email.com
   - Click "Install WordPress"

6. **Activate Plugin**
   - Login to WordPress admin
   - Go to: Plugins
   - Find "WP Cinema Manager"
   - Click "Activate"

7. **Start Managing Your Cinema!**
   - Click "Cinema" in sidebar
   - Explore Dashboard
   - Add your first movie!

### Using Local by Flywheel (Easiest for beginners)

1. **Download & Install**
   - Visit: https://localwp.com/
   - Download Local (free)
   - Install and open

2. **Create Site**
   - Click "+ Create a new site"
   - Name: "Cinema Website"
   - Continue through defaults
   - Set admin username/password

3. **Install Plugin**
   - Click "Open site folder"
   - Navigate to: app/public/wp-content/plugins/
   - Copy the entire `wp-cinema` folder here
   - In Local, click "WP Admin"
   - Go to Plugins > Activate "WP Cinema Manager"

### Useful Commands (Docker)

```powershell
# Start WordPress
docker-compose up -d

# Stop WordPress
docker-compose down

# View logs
docker-compose logs -f

# Restart WordPress
docker-compose restart

# Remove everything (start fresh)
docker-compose down -v
```

### Accessing Your Site

- **WordPress Site**: http://localhost:8080
- **WordPress Admin**: http://localhost:8080/wp-admin
- **Database Admin**: http://localhost:8081
  - Username: root
  - Password: rootpassword

### Next Steps

1. Go to **Cinema > Dashboard**
2. Add a few movies
3. Create venue/hall
4. Schedule showtimes
5. Test the REST API: http://localhost:8080/wp-json/cinema/v1/showtimes/upcoming

### Troubleshooting

**Port already in use?**
```powershell
# Change port in docker-compose.yml
# Line 26: "8080:80" → "8888:80"
```

**Plugin not showing?**
- Check folder is named `wp-cinema` (not wp-cinema-main or wp-cinema-master)
- Check it's in wp-content/plugins/ directory

**Can't access site?**
```powershell
# Check Docker is running
docker ps

# Should see: cinema_wordpress, cinema_db, cinema_phpmyadmin
```

Need help? Check SETUP-WORDPRESS.md for detailed instructions!

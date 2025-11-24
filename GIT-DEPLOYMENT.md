# Git Deployment Guide - Quick Order Table for Variations

Deploy the plugin directly from your Git repository to your WordPress hosting.

---

## 🚀 Quick Deployment

### Step 1: SSH into Your Hosting

```bash
ssh user@yourserver.com
```

### Step 2: Navigate to Plugins Directory

```bash
cd /path/to/wp-content/plugins/
```

Common paths:
- cPanel: `~/public_html/wp-content/plugins/`
- Direct: `/var/www/html/wp-content/plugins/`
- Custom: `/home/username/yourdomain.com/wp-content/plugins/`

### Step 3: Clone the Repository

```bash
git clone https://github.com/yourusername/quick-order-table-for-variations.git
```

Or if using a different branch:
```bash
git clone -b main https://github.com/yourusername/quick-order-table-for-variations.git
```

### Step 4: Set Correct Permissions

```bash
cd quick-order-table-for-variations
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
```

### Step 5: Activate Plugin

Go to WordPress Admin:
```
WordPress Admin → Plugins → Installed Plugins → Activate
```

**Done!** ✅

---

## 🔄 Updating the Plugin

### Update via Git Pull

```bash
# Navigate to plugin directory
cd /path/to/wp-content/plugins/quick-order-table-for-variations

# Pull latest changes
git pull origin main

# Clear WordPress cache (if using caching plugin)
# wp cache flush  # If WP-CLI is available
```

### Check for Changes

```bash
# View what changed
git log -5 --oneline

# View specific changes
git diff HEAD~1 HEAD
```

---

## 📂 Repository Structure

After cloning, you should have:

```
wp-content/plugins/quick-order-table-for-variations/
├── quick-order-table-for-variations.php  [Main file]
├── includes/
│   ├── class-qot-shortcode.php
│   ├── class-qot-ajax.php
│   ├── class-qot-assets.php
│   └── class-qot-ux-builder.php
├── assets/
│   ├── css/quick-order-table.css
│   └── js/quick-order-table.js
├── README.md
├── readme.txt
├── INSTALLATION.md
├── AUDIT-REPORT.md
├── CHANGELOG.md
├── .gitignore
└── GIT-DEPLOYMENT.md (this file)
```

---

## 🔐 File Permissions

### Recommended Permissions

```bash
# Directories: 755 (rwxr-xr-x)
chmod 755 includes/
chmod 755 assets/
chmod 755 assets/css/
chmod 755 assets/js/

# Files: 644 (rw-r--r--)
chmod 644 *.php
chmod 644 includes/*.php
chmod 644 assets/css/*.css
chmod 644 assets/js/*.js
chmod 644 *.md
chmod 644 *.txt
```

### One-Line Command

```bash
find . -type d -exec chmod 755 {} \; && find . -type f -exec chmod 644 {} \;
```

---

## 🌿 Branch Strategy

### Main Branch (Production)

```bash
# Deploy production-ready code
git checkout main
git pull origin main
```

### Development Branch

```bash
# For testing new features
git checkout -b development
git pull origin development
```

### Feature Branches

```bash
# Create feature branch
git checkout -b feature/new-feature-name

# Push to remote
git push origin feature/new-feature-name
```

---

## 🔧 Common Deployment Scenarios

### Scenario 1: First Time Deployment

```bash
# 1. Clone repository
cd /path/to/wp-content/plugins/
git clone https://github.com/yourusername/quick-order-table-for-variations.git

# 2. Set permissions
cd quick-order-table-for-variations
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# 3. Activate in WordPress
# Go to: WP Admin → Plugins → Activate
```

### Scenario 2: Update Existing Installation

```bash
# 1. Navigate to plugin
cd /path/to/wp-content/plugins/quick-order-table-for-variations

# 2. Backup current version (optional but recommended)
cd ..
tar -czf quick-order-table-backup-$(date +%Y%m%d).tar.gz quick-order-table-for-variations/

# 3. Pull updates
cd quick-order-table-for-variations
git pull origin main

# 4. Clear cache
# Clear WordPress cache, browser cache, and any CDN cache
```

### Scenario 3: Rollback to Previous Version

```bash
# 1. Check available versions
git log --oneline

# 2. Rollback to specific commit
git reset --hard COMMIT_HASH

# 3. Or rollback one version
git reset --hard HEAD~1

# 4. Force update (be careful!)
git push origin main --force
```

### Scenario 4: Multiple Sites Deployment

```bash
# Site 1
ssh user@site1.com
cd /path/to/wp-content/plugins/
git clone https://github.com/yourusername/quick-order-table-for-variations.git

# Site 2
ssh user@site2.com
cd /path/to/wp-content/plugins/
git clone https://github.com/yourusername/quick-order-table-for-variations.git

# Site 3
ssh user@site3.com
cd /path/to/wp-content/plugins/
git clone https://github.com/yourusername/quick-order-table-for-variations.git
```

---

## 🚨 Troubleshooting

### Issue: Permission Denied

```bash
# Fix ownership
sudo chown -R www-data:www-data quick-order-table-for-variations/

# Or for cPanel
sudo chown -R username:username quick-order-table-for-variations/
```

### Issue: Git Pull Fails (Local Changes)

```bash
# Save local changes
git stash

# Pull updates
git pull origin main

# Reapply local changes
git stash pop

# Or discard local changes
git reset --hard HEAD
git pull origin main
```

### Issue: Plugin Not Showing After Clone

**Check:**
1. Directory name must be: `quick-order-table-for-variations`
2. Main file must exist: `quick-order-table-for-variations.php`
3. Permissions must be correct (755 for dirs, 644 for files)

```bash
# Verify structure
ls -la quick-order-table-for-variations/
```

### Issue: Can't Activate Plugin

**Check WordPress error log:**
```bash
tail -f /path/to/wp-content/debug.log
```

**Common fixes:**
1. Ensure WooCommerce is installed and active
2. Check PHP version (7.4+ required)
3. Check file permissions
4. Look for syntax errors in PHP files

---

## 🔄 Automated Deployment

### Using WP-CLI

If WP-CLI is available on your server:

```bash
#!/bin/bash
# deploy-plugin.sh

# Navigate to WordPress root
cd /path/to/wordpress/

# Deactivate plugin
wp plugin deactivate quick-order-table-for-variations

# Update from Git
cd wp-content/plugins/quick-order-table-for-variations
git pull origin main

# Go back to WP root
cd /path/to/wordpress/

# Activate plugin
wp plugin activate quick-order-table-for-variations

# Clear cache
wp cache flush

echo "✅ Plugin updated successfully!"
```

Make it executable:
```bash
chmod +x deploy-plugin.sh
```

Run it:
```bash
./deploy-plugin.sh
```

### Using Deployment Hook (Advanced)

Set up a webhook to auto-deploy on Git push:

```bash
# Create webhook script
cat > /var/www/hooks/deploy-qot.php << 'WEBHOOK'
<?php
// Verify webhook secret
$secret = 'your-secret-key';
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';

// Verify request
if (!hash_equals($signature, 'sha1=' . hash_hmac('sha1', file_get_contents('php://input'), $secret))) {
    http_response_code(403);
    die('Invalid signature');
}

// Execute deployment
exec('cd /path/to/plugins/quick-order-table-for-variations && git pull origin main 2>&1', $output);

// Log deployment
file_put_contents('/var/log/deploy-qot.log', date('Y-m-d H:i:s') . ' - Deployed: ' . implode("\n", $output) . "\n", FILE_APPEND);

echo json_encode(['status' => 'success', 'output' => $output]);
WEBHOOK
```

Add webhook URL to GitHub/GitLab:
```
https://yoursite.com/hooks/deploy-qot.php
```

---

## 📋 Pre-Deployment Checklist

Before deploying:

- [ ] All changes committed to Git
- [ ] Tested on local/staging environment
- [ ] WordPress version compatible (5.8+)
- [ ] WooCommerce version compatible (8.0+)
- [ ] PHP version compatible (7.4+)
- [ ] Backup current site
- [ ] Note current plugin version
- [ ] Check server disk space
- [ ] Verify SSH/FTP access works

---

## 📋 Post-Deployment Checklist

After deploying:

- [ ] Plugin activated successfully
- [ ] No fatal errors in error log
- [ ] Shortcode displays correctly
- [ ] Search functionality works
- [ ] Add to cart works
- [ ] Mini cart updates
- [ ] Mobile responsive
- [ ] Clear all caches (WordPress, browser, CDN)
- [ ] Test on frontend
- [ ] Verify admin settings (if any)

---

## 🔍 Health Check Commands

### Verify Installation

```bash
# Check plugin exists
ls -la /path/to/plugins/quick-order-table-for-variations/

# Check main file
cat /path/to/plugins/quick-order-table-for-variations/quick-order-table-for-variations.php | grep "Plugin Name"

# Check permissions
stat /path/to/plugins/quick-order-table-for-variations/
```

### Check Git Status

```bash
cd /path/to/plugins/quick-order-table-for-variations/

# Current branch
git branch

# Latest commit
git log -1

# Remote URL
git remote -v

# Check for updates
git fetch origin
git status
```

### Using WP-CLI

```bash
# List all plugins
wp plugin list

# Check plugin status
wp plugin status quick-order-table-for-variations

# Get plugin info
wp plugin get quick-order-table-for-variations
```

---

## 🌐 Multi-Environment Setup

### Development → Staging → Production

```bash
# Development
git push origin development

# Staging server
ssh staging@server.com
cd /path/to/plugins/quick-order-table-for-variations
git pull origin development

# Test on staging...

# If all good, merge to main
git checkout main
git merge development
git push origin main

# Production server
ssh prod@server.com
cd /path/to/plugins/quick-order-table-for-variations
git pull origin main
```

---

## 💾 Backup Before Update

### Quick Backup

```bash
# Create backup
cd /path/to/plugins/
tar -czf qot-backup-$(date +%Y%m%d-%H%M).tar.gz quick-order-table-for-variations/

# List backups
ls -lh qot-backup-*.tar.gz

# Restore if needed
tar -xzf qot-backup-YYYYMMDD-HHMM.tar.gz
```

### Database Backup (Optional)

```bash
# If plugin stores any data (currently it doesn't)
wp db export qot-backup-$(date +%Y%m%d).sql
```

---

## 🔐 Security Best Practices

1. **Use SSH Keys**
   ```bash
   ssh-keygen -t rsa -b 4096
   ssh-copy-id user@server.com
   ```

2. **Secure Git Credentials**
   ```bash
   git config credential.helper store
   # Or use SSH instead of HTTPS
   git remote set-url origin git@github.com:user/repo.git
   ```

3. **Restrict File Permissions**
   ```bash
   # Plugin files should NOT be writable by web server
   find . -type f -exec chmod 644 {} \;
   ```

4. **Keep .git Directory Secure**
   ```bash
   # Add to .htaccess (Apache)
   <DirectoryMatch "\.git">
       Require all denied
   </DirectoryMatch>
   
   # Or nginx
   location ~ /\.git {
       deny all;
   }
   ```

---

## 📞 Support

**Issue with Git deployment?**

Contact: **0915.833.321**

**Common Support Resources:**
- Git Documentation: https://git-scm.com/doc
- WordPress Plugin Handbook: https://developer.wordpress.org/plugins/
- WP-CLI Commands: https://wp-cli.org/commands/

---

## 🎯 Quick Reference

### Clone Repository
```bash
git clone https://github.com/user/quick-order-table-for-variations.git
```

### Update Plugin
```bash
cd quick-order-table-for-variations && git pull origin main
```

### Check Status
```bash
git status && git log -1
```

### Set Permissions
```bash
find . -type d -exec chmod 755 {} \; && find . -type f -exec chmod 644 {} \;
```

### Activate Plugin
```bash
wp plugin activate quick-order-table-for-variations
```

---

**Deployed via Git** ✅ **No ZIP files needed!** 🎉

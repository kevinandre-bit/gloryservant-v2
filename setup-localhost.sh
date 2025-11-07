#!/bin/bash

PROJECT_NAME="gloryservant_v2"
DOMAIN="gloryservant-v2.local"
PROJECT_PATH="/Applications/XAMPP/xamppfiles/htdocs/$PROJECT_NAME/public"
HOSTS_FILE="/etc/hosts"
VHOSTS_FILE="/Applications/XAMPP/xamppfiles/etc/extra/httpd-vhosts.conf"

# 1. Add to /etc/hosts
if ! grep -q "$DOMAIN" "$HOSTS_FILE"; then
  echo "Adding $DOMAIN to /etc/hosts..."
  echo "127.0.0.1 $DOMAIN" | sudo tee -a "$HOSTS_FILE"
else
  echo "$DOMAIN already exists in /etc/hosts"
fi

# 2. Add virtual host
VHOST_ENTRY="
<VirtualHost *:80>
    ServerAdmin webmaster@$DOMAIN
    DocumentRoot \"$PROJECT_PATH\"
    ServerName $DOMAIN
    ErrorLog \"logs/$DOMAIN-error.log\"
    CustomLog \"logs/$DOMAIN-access.log\" common
    <Directory \"$PROJECT_PATH\">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
"

if ! grep -q "$DOMAIN" "$VHOSTS_FILE"; then
  echo "Adding virtual host for $DOMAIN..."
  echo "$VHOST_ENTRY" | sudo tee -a "$VHOSTS_FILE"
else
  echo "Virtual host for $DOMAIN already exists"
fi

# 3. Ensure httpd.conf includes vhosts
HTTPD_CONF="/Applications/XAMPP/xamppfiles/etc/httpd.conf"
if grep -q "^#Include etc/extra/httpd-vhosts.conf" "$HTTPD_CONF"; then
  echo "Uncommenting virtual hosts include..."
  sudo sed -i '' 's|#Include etc/extra/httpd-vhosts.conf|Include etc/extra/httpd-vhosts.conf|' "$HTTPD_CONF"
fi

# 4. Restart XAMPP Apache
echo "Restarting XAMPP Apache..."
sudo /Applications/XAMPP/xamppfiles/xampp restartapache

echo "✅ Setup complete. Visit: http://$DOMAIN"

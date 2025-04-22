FROM tomsik68/xampp:8

# Copy application files
COPY . /opt/lampp/htdocs/project

# Make sure the directory is writable
RUN chmod -R 755 /opt/lampp/htdocs/project

# Copy database.sql for import
COPY database.sql /opt/lampp/

# Create a startup script
RUN echo '#!/bin/bash' > /opt/lampp/setup.sh && \
    echo '/opt/lampp/lampp start' >> /opt/lampp/setup.sh && \
    echo 'sleep 10' >> /opt/lampp/setup.sh && \
    echo '/opt/lampp/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS dynamic_web_app;"' >> /opt/lampp/setup.sh && \
    echo '/opt/lampp/bin/mysql -u root dynamic_web_app < /opt/lampp/database.sql' >> /opt/lampp/setup.sh && \
    echo 'tail -f /opt/lampp/logs/error_log' >> /opt/lampp/setup.sh && \
    chmod +x /opt/lampp/setup.sh

# Update config.php with Docker-compatible settings if needed
# RUN sed -i 's/localhost/127.0.0.1/g' /opt/lampp/htdocs/project/includes/config.php

EXPOSE 80 3306

# Use the setup script as the entry point
CMD ["/opt/lampp/setup.sh"]
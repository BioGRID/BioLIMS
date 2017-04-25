# BioLIMS Configuration
This directory contains the configuration file for the BioLIMS application. The following is a brief outline of options available for configuration.

## Database Configuration
These variables are used to setup connectivity to the MySQL database used to operate the BioLIMS web application and tools.
+ **DB_HOST** - The IP or Hostname of your Database Server example: localhost, 192.112.132.111, www.mysite.com
+ **DB_PORT** - The port number of your MySQL Database. On most configurations, this will be 3306.
+ **DB_USER** - The username used to connect to the **DB_MAIN** and **DB_QUICK** databases.
+ **DB_PASS** - The Password associated with the **DB_USER** login.
+ **DB_MAIN** - The main database name that will be used for your BioLIMS installation.
+ **DB_QUICK** - THe main annotation database that will be used for your BioLIMS installation
+ **DB_IMS** - THe main IMS database that will be used for your BioLIMS installation

## Web Configuration
These variables pertain to the website and the public facing aspects of the IMS. 
+ **WEB_URL** - This is the full url to your BioLIMS installation. For example: http://www.BioLIMS.com, http://192.111.111.111, http://localhost
+ **WEB_SUBFOLDER** - If your url uses a subfolder, you need to specify it here. Example: if you url is http://www.BioLIMS.com/myorca you would put "myBioLIMS" here. Generally not required, so the default is "".
+ **WEB_NAME** - This is the name for your installation. Default: Open Repository for CRISPR Analysis
+ **WEB_NAME_ABBR** - An abbreviated version of the **WEB_NAME** variable for instances with limited space. Default: BioLIMS
+ **WEB_DESC** - A general description of your site, likely no need to modify. This is used in meta desc headings in HTML.
+ **WEB_KEYWORDS** - A general set of keywords for your site, likely no need to modify. This is used in meta keywords headings in HTML.
+ **OWNER_NAME** - A general name to refer to when attributing the site. Default: TyersLab.com.
+ **OWNER_URL** - A url to link to when attributing the site. Default: http://www.tyerslab.com.
+ **ADMIN_EMAIL** - An email address to contact when seeking the site administrator. Default: biogridadmin@gmail.com.
+ **WIKI_URL** - A url to the Wiki for the BioLIMS platform, with tips for its use and operation. Default: https://github.com/BioGRID/crispr-upload/wiki.
+ **VERSION** - A version number. Can be almost anything. Default: 0.0.0.1Alpha.

## Directory Configuration
These are the general directories used by the application. Other than the **BASE_PATH** variable, you should not need to modify any of the other defaults, unless in circumstances where you are unable to use the default value.
+ **BASE_PATH** - The full path to the site installation of BioLIMS. Example: /home/myusername/public_html/BioLIMS/site
+ **WEB_DIR** - The name of the web directory under the **BASE_PATH**. Do not modify unless necessary. Default: www.
+ **APP_DIR** - The name of the app directory under the **BASE_PATH**. Do not modify unless necessary. Default: app.
+ **CSS_DIR** - The name of the css directory under the **WEB_DIR**. Do not modify unless necessary. Default: css.
+ **JS_DIR** - The name of the javascript directory under the **WEB_DIR**. Do not modify unless necessary. Default: js.
+ **IMG_DIR** - The name of the image directory under the **WEB_DIR**. Do not modify unless necessary. Default: img.
+ **SCRIPT_DIR** - The name of the script directory under the **WEB_DIR**. Do not modify unless necessary. Default: script.
+ **TEMPLATE_DIR** - The name of the template directory under the **APP_DIR**. Do not modify unless necessary. Default: templates.
+ **INC_DIR** - The name of the include directory under the **APP_DIR**. Do not modify unless necessary. Default: inc.

## Session Management Configuration
These options are for setting up the default session management strategy for the BioLIMS web application. Do not modify these variables unless necessary.
+ **SESSION_NAME** - The name used in the session for any logged in user. Default: BioLIMS_USER
+ **PERSISTENT_TIMEOUT** - Number of seconds a persistent login is active for without being refreshed. Default: 604800 (7 Days)
+ **COOKIE_NAME** - The name of the cookie used for tracking already logged in users. Default: BioLIMS
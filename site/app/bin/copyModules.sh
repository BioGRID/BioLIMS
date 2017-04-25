#!/bin/sh -e

# FONT AWESOME
cp -rf node_modules/font-awesome/css/font-awesome.min.css ../www/css/
cp -rf node_modules/font-awesome/fonts/* ../www/fonts/

# BOOTSTRAP
cp -rf node_modules/bootstrap/dist/fonts/* ../www/fonts/
cp -rf node_modules/bootstrap/dist/js/bootstrap.min.js ../www/js/

# JQUERY
cp -rf node_modules/jquery/dist/jquery.min.js ../www/js/

# DATATABLES
cp -rf node_modules/datatables.net/js/jquery.dataTables.js ../www/js/
cp -rf node_modules/datatables.net-bs/js/dataTables.bootstrap.js ../www/js/
cp -rf node_modules/datatables.net-bs/css/dataTables.bootstrap.css ../www/css/

# QTIP2
cp -rf node_modules/qtip2/dist/jquery.qtip.min.js ../www/js/
cp -rf node_modules/qtip2/dist/jquery.qtip.min.css ../www/css/

# DROPZONE
cp -rf node_modules/dropzone/dist/min/dropzone.min.js ../www/js/
cp -rf node_modules/dropzone/dist/min/dropzone.min.css ../www/css/

# BOOTSTRAP DATEPICKER
cp -rf node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js ../www/js/
cp -rf node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css ../www/css/

# FORM VALIDATION
cp -rf node_modules/formvalidation/dist/js/formValidation.min.js ../www/js/formValidation/
cp -rf node_modules/formvalidation/dist/js/framework/bootstrap.min.js ../www/js/formValidation/
cp -rf node_modules/formvalidation/dist/css/formValidation.min.css ../www/css/formValidation/

# ALERTIFY
cp -rf node_modules/alertifyjs/build/alertify.min.js ../www/js/
cp -rf node_modules/alertifyjs/build/css/alertify.min.css ../www/css/
cp -rf node_modules/alertifyjs/build/css/themes/bootstrap.min.css ../www/css/alertify-bootstrap.min.css
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2017-05-22 09:42:56 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 10:31:49 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 11:30:25 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 11:51:43 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 11:51:44 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 11:52:05 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 11:52:06 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 11:52:06 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 11:57:31 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 11:57:32 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 11:57:32 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 12:07:05 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 12:07:05 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 12:10:30 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 12:21:34 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 12:23:17 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 12:29:49 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 12:30:47 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 12:31:05 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 12:48:29 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: FATAL:  el sistema de base de datos está en modo de recuperación /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 12:52:32 --> Unable to connect to the database
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 326
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: FATAL:  el sistema de base de datos está en modo de recuperación /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 12:52:32 --> Unable to connect to the database
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 480
ERROR - 2017-05-22 12:52:32 --> Query error:  - Invalid query: 
		SELECT
			org.c_org_id,
			org.name,
			remote.ip
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		WHERE
			org.description =  AND remote.view = 1;
ERROR - 2017-05-22 12:52:32 --> Severity: Error --> Call to a member function result() on boolean /var/www/html/ocsmanager/application/models/COrg_model.php 31
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: FATAL:  el sistema de base de datos está en modo de recuperación /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 12:52:32 --> Unable to connect to the database
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 326
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: FATAL:  el sistema de base de datos está en modo de recuperación /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 12:52:32 --> Unable to connect to the database
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 480
ERROR - 2017-05-22 12:52:32 --> Query error:  - Invalid query: 
		SELECT
			org.c_org_id,
			org.name,
			remote.ip
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		WHERE
			org.description =  AND remote.view = 1;
ERROR - 2017-05-22 12:52:32 --> Severity: Error --> Call to a member function result() on boolean /var/www/html/ocsmanager/application/models/COrg_model.php 31
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: FATAL:  el sistema de base de datos está en modo de recuperación /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 12:52:32 --> Unable to connect to the database
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 326
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: FATAL:  el sistema de base de datos está en modo de recuperación /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 12:52:32 --> Unable to connect to the database
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 480
ERROR - 2017-05-22 12:52:32 --> Query error:  - Invalid query: 
		SELECT
			org.c_org_id,
			org.name,
			remote.ip
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		WHERE
			org.description =  AND remote.view = 1;
ERROR - 2017-05-22 12:52:32 --> Severity: Error --> Call to a member function result() on boolean /var/www/html/ocsmanager/application/models/COrg_model.php 31
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: FATAL:  el sistema de base de datos está en modo de recuperación /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 12:52:32 --> Unable to connect to the database
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 326
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: FATAL:  el sistema de base de datos está en modo de recuperación /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 12:52:32 --> Unable to connect to the database
ERROR - 2017-05-22 12:52:32 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 480
ERROR - 2017-05-22 12:52:32 --> Query error:  - Invalid query: 
		SELECT
			org.c_org_id,
			org.name,
			remote.ip
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		WHERE
			org.description =  AND remote.view = 1;
ERROR - 2017-05-22 12:52:32 --> Severity: Error --> Call to a member function result() on boolean /var/www/html/ocsmanager/application/models/COrg_model.php 31
ERROR - 2017-05-22 13:08:49 --> Severity: Warning --> fopen(http://192.168.8.1/sistemaweb/centralizer_.php?mod=TOTALS_SUMARY_SALE&amp;from=20170521&amp;to=20170521&amp;warehouse_id=201&amp;type_cost=avg): failed to open stream: Connection timed out /var/www/html/ocsmanager/application/helpers/functions_helper.php 68
ERROR - 2017-05-22 13:08:49 --> Error al conectarse a http://192.168.8.1/sistemaweb/centralizer_.php?mod=TOTALS_SUMARY_SALE&from=20170521&to=20170521&warehouse_id=201&type_cost=avg
ERROR - 2017-05-22 13:26:05 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 13:27:21 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 14:21:18 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 15:48:45 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:45 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:45 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 326
ERROR - 2017-05-22 15:48:45 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:45 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:45 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 480
ERROR - 2017-05-22 15:48:45 --> Query error:  - Invalid query: 
		SELECT
			org.c_org_id,
			org.name,
			remote.ip
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		WHERE
			org.description =  AND remote.view = 1;
ERROR - 2017-05-22 15:48:45 --> Severity: Error --> Call to a member function result() on boolean /var/www/html/ocsmanager/application/models/COrg_model.php 31
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:46 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 326
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:46 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 480
ERROR - 2017-05-22 15:48:46 --> Query error:  - Invalid query: 
		SELECT
			org.c_org_id,
			org.name,
			remote.ip
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		WHERE
			org.description =  AND remote.view = 1;
ERROR - 2017-05-22 15:48:46 --> Severity: Error --> Call to a member function result() on boolean /var/www/html/ocsmanager/application/models/COrg_model.php 31
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:46 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 326
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:46 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 480
ERROR - 2017-05-22 15:48:46 --> Query error:  - Invalid query: 
		SELECT
			org.c_org_id,
			org.name,
			remote.ip
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		WHERE
			org.description =  AND remote.view = 1;
ERROR - 2017-05-22 15:48:46 --> Severity: Error --> Call to a member function result() on boolean /var/www/html/ocsmanager/application/models/COrg_model.php 31
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:46 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 326
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:46 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:46 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 480
ERROR - 2017-05-22 15:48:46 --> Query error:  - Invalid query: 
		SELECT
			org.c_org_id,
			org.name,
			remote.ip
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		WHERE
			org.description =  AND remote.view = 1;
ERROR - 2017-05-22 15:48:46 --> Severity: Error --> Call to a member function result() on boolean /var/www/html/ocsmanager/application/models/COrg_model.php 31
ERROR - 2017-05-22 15:48:51 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:51 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:51 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 326
ERROR - 2017-05-22 15:48:51 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:51 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:51 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 480
ERROR - 2017-05-22 15:48:51 --> Query error:  - Invalid query: 
		SELECT
			org.c_org_id,
			org.name,
			remote.ip
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		WHERE
			org.description =  AND remote.view = 1;
ERROR - 2017-05-22 15:48:51 --> Severity: Error --> Call to a member function result() on boolean /var/www/html/ocsmanager/application/models/COrg_model.php 31
ERROR - 2017-05-22 15:48:56 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:56 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:56 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 326
ERROR - 2017-05-22 15:48:56 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TCP/IP connections on port 5432?
could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (127.0.0.1) and accepting
	TCP/IP connections on port 5432? /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 154
ERROR - 2017-05-22 15:48:56 --> Unable to connect to the database
ERROR - 2017-05-22 15:48:56 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given /var/www/html/ocsmanager/system/database/drivers/postgre/postgre_driver.php 480
ERROR - 2017-05-22 15:48:56 --> Query error:  - Invalid query: 
		SELECT
			org.c_org_id,
			org.name,
			remote.ip
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		WHERE
			org.description =  AND remote.view = 1;
ERROR - 2017-05-22 15:48:56 --> Severity: Error --> Call to a member function result() on boolean /var/www/html/ocsmanager/application/models/COrg_model.php 31
ERROR - 2017-05-22 15:48:59 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection refused
	Is the server running on host &quot;localhost&quot; (::1) and accepting
	TERROR - 2017-05-22 16:23:37 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 16:51:18 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 16:51:45 --> Severity: Core Warning --> PHP Startup: Unable to load dynamic library '/usr/lib64/php/modules/oci8.so' - /usr/lib64/php/modules/oci8.so: cannot open shared object file: No such file or directory Unknown 0
ERROR - 2017-05-22 16:52:29 --> Severity: Warning --> fopen(http://192.168.4.1/sistemaweb/centralizer_.php?mod=TOTALS_SUMARY_SALE&amp;from=20170521&amp;to=20170521&amp;warehouse_id=205&amp;type_cost=avg): failed to open stream: Connection timed out /var/www/html/ocsmanager/application/helpers/functions_helper.php 68
ERROR - 2017-05-22 16:52:29 --> Error al conectarse a http://192.168.4.1/sistemaweb/centralizer_.php?mod=TOTALS_SUMARY_SALE&from=20170521&to=20170521&warehouse_id=205&type_cost=avg

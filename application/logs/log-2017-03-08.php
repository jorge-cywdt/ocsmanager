<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2017-03-08 09:14:00 --> Severity: Warning --> fopen(http://192.168.4.1/sistemaweb/centralizer_.php?mod=TOTALS_SALE_COMB&from=20170301&to=20170301&warehouse_id=205&qty_sale=kardex&type_cost=): failed to open stream: Connection timed out /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 54
ERROR - 2017-03-08 09:14:00 --> Error al conectarse a http://192.168.4.1/sistemaweb/centralizer_.php?mod=TOTALS_SALE_COMB&from=20170301&to=20170301&warehouse_id=205&qty_sale=kardex&type_cost=
ERROR - 2017-03-08 09:14:00 --> Severity: Warning --> arsort() expects parameter 1 to be array, null given /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 117
ERROR - 2017-03-08 09:14:00 --> Severity: Warning --> Invalid argument supplied for foreach() /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 120
ERROR - 2017-03-08 09:15:51 --> Severity: Warning --> fopen(http://192.168.4.1/sistemaweb/centralizer_.php?mod=TOTALS_SALE_COMB&from=20170301&to=20170301&warehouse_id=205&qty_sale=kardex&type_cost=): failed to open stream: Connection timed out /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 54
ERROR - 2017-03-08 09:15:51 --> Error al conectarse a http://192.168.4.1/sistemaweb/centralizer_.php?mod=TOTALS_SALE_COMB&from=20170301&to=20170301&warehouse_id=205&qty_sale=kardex&type_cost=
ERROR - 2017-03-08 09:15:51 --> Severity: Warning --> arsort() expects parameter 1 to be array, null given /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 117
ERROR - 2017-03-08 09:15:51 --> Severity: Warning --> Invalid argument supplied for foreach() /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 120
ERROR - 2017-03-08 09:52:44 --> Severity: Warning --> fopen(http://192.168.4.1/sistemaweb/centralizer_.php?mod=TOTALS_SALE_COMB&from=20170301&to=20170307&warehouse_id=205&qty_sale=kardex&type_cost=): failed to open stream: Connection timed out /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 54
ERROR - 2017-03-08 09:52:44 --> Error al conectarse a http://192.168.4.1/sistemaweb/centralizer_.php?mod=TOTALS_SALE_COMB&from=20170301&to=20170307&warehouse_id=205&qty_sale=kardex&type_cost=
ERROR - 2017-03-08 09:52:44 --> Severity: Warning --> arsort() expects parameter 1 to be array, null given /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 117
ERROR - 2017-03-08 09:52:44 --> Severity: Warning --> Invalid argument supplied for foreach() /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 120
ERROR - 2017-03-08 10:20:07 --> Severity: Warning --> pg_query(): Query failed: ERROR:  invalid input syntax for type numeric: "undefined"
LINE 26:   AND org.c_org_id = 'undefined' AND remote.view = 1
                              ^ /usr/local/apache/htdocs/ocsmanager/system/database/drivers/postgre/postgre_driver.php 242
ERROR - 2017-03-08 10:20:07 --> Query error: ERROR:  invalid input syntax for type numeric: "undefined"
LINE 26:   AND org.c_org_id = 'undefined' AND remote.view = 1
                              ^ - Invalid query: 
		SELECT
			client.name AS client_name,
			client.taxid,
			org.c_org_id,
			org.name,
			org.initials,
			remote.ip,
			warehouse.description AS almacen_id
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN c_client client ON (
			org.c_client_id = client.c_client_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		JOIN i_warehouse warehouse ON (
			org.c_org_id = warehouse.c_org_id
		)
		WHERE
			org.description = 'C'
		AND org.c_org_id = 'undefined' AND remote.view = 1
		ORDER BY client.c_client_id ASC;
ERROR - 2017-03-08 10:20:07 --> Severity: Error --> Call to a member function result() on a non-object /usr/local/apache/htdocs/ocsmanager/application/models/COrg_model.php 63
ERROR - 2017-03-08 10:20:28 --> Severity: Warning --> pg_query(): Query failed: ERROR:  invalid input syntax for type numeric: "undefined"
LINE 26:   AND org.c_org_id = 'undefined' AND remote.view = 1
                              ^ /usr/local/apache/htdocs/ocsmanager/system/database/drivers/postgre/postgre_driver.php 242
ERROR - 2017-03-08 10:20:28 --> Query error: ERROR:  invalid input syntax for type numeric: "undefined"
LINE 26:   AND org.c_org_id = 'undefined' AND remote.view = 1
                              ^ - Invalid query: 
		SELECT
			client.name AS client_name,
			client.taxid,
			org.c_org_id,
			org.name,
			org.initials,
			remote.ip,
			warehouse.description AS almacen_id
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN c_client client ON (
			org.c_client_id = client.c_client_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		JOIN i_warehouse warehouse ON (
			org.c_org_id = warehouse.c_org_id
		)
		WHERE
			org.description = 'C'
		AND org.c_org_id = 'undefined' AND remote.view = 1
		ORDER BY client.c_client_id ASC;
ERROR - 2017-03-08 10:20:28 --> Severity: Error --> Call to a member function result() on a non-object /usr/local/apache/htdocs/ocsmanager/application/models/COrg_model.php 63
ERROR - 2017-03-08 10:22:10 --> Severity: Warning --> pg_query(): Query failed: ERROR:  invalid input syntax for type numeric: "undefined"
LINE 26:   AND org.c_org_id = 'undefined' AND remote.view = 1
                              ^ /usr/local/apache/htdocs/ocsmanager/system/database/drivers/postgre/postgre_driver.php 242
ERROR - 2017-03-08 10:22:10 --> Query error: ERROR:  invalid input syntax for type numeric: "undefined"
LINE 26:   AND org.c_org_id = 'undefined' AND remote.view = 1
                              ^ - Invalid query: 
		SELECT
			client.name AS client_name,
			client.taxid,
			org.c_org_id,
			org.name,
			org.initials,
			remote.ip,
			warehouse.description AS almacen_id
		FROM
			c_org org
		JOIN mig_cowmap cowmap ON (
			org.c_org_id = cowmap.c_org_id
		)
		JOIN c_client client ON (
			org.c_client_id = client.c_client_id
		)
		JOIN mig_remote remote ON (
			cowmap.id_remote = mig_remote_id
		)
		JOIN i_warehouse warehouse ON (
			org.c_org_id = warehouse.c_org_id
		)
		WHERE
			org.description = 'C'
		AND org.c_org_id = 'undefined' AND remote.view = 1
		ORDER BY client.c_client_id ASC;
ERROR - 2017-03-08 10:22:10 --> Severity: Error --> Call to a member function result() on a non-object /usr/local/apache/htdocs/ocsmanager/application/models/COrg_model.php 63
ERROR - 2017-03-08 11:11:42 --> Severity: Warning --> fopen(http://192.168.4.1/sistemaweb/centralizer_.php?mod=STOCK_COMB&from=20170228&to=20170307&warehouse_id=205&days=7&isvaliddiffmonths=si): failed to open stream: Connection timed out /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 54
ERROR - 2017-03-08 11:11:42 --> Error al conectarse a http://192.168.4.1/sistemaweb/centralizer_.php?mod=STOCK_COMB&from=20170228&to=20170307&warehouse_id=205&days=7&isvaliddiffmonths=si
ERROR - 2017-03-08 14:05:07 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:05:41 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:05:42 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:05:42 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:05:43 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:05:43 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:13:57 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:13:57 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:13:57 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:13:57 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:13:58 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:14:16 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:14:16 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:14:16 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:14:17 --> 404 Page Not Found: Erer/index
ERROR - 2017-03-08 14:14:17 --> 404 Page Not Found: Erer/index

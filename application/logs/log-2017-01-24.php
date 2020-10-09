<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2017-01-24 10:36:04 --> Severity: Notice --> Undefined variable: sheet /usr/local/apache/htdocs/ocsmanager/application/controllers/Reports.php 56
ERROR - 2017-01-24 10:36:04 --> Severity: Error --> Call to a member function getStyle() on a non-object /usr/local/apache/htdocs/ocsmanager/application/controllers/Reports.php 56
ERROR - 2017-01-24 14:52:47 --> Severity: Warning --> pg_query(): Query failed: ERROR:  column org.initial does not exist
LINE 7:    org.initial,
           ^ /usr/local/apache/htdocs/ocsmanager/system/database/drivers/postgre/postgre_driver.php 242
ERROR - 2017-01-24 14:52:47 --> Query error: ERROR:  column org.initial does not exist
LINE 7:    org.initial,
           ^ - Invalid query: 
		SELECT
			client.name AS client_name,
			client.taxid,
			org.c_org_id,
			org.name,
			org.initial,
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
		ORDER BY client.c_client_id ASC;
ERROR - 2017-01-24 14:53:33 --> Severity: Notice --> Undefined property: stdClass::$description /usr/local/apache/htdocs/ocsmanager/application/controllers/Requests.php 221
ERROR - 2017-01-24 17:34:37 --> Severity: Warning --> fopen(http://192.168.11.1/sistemaweb/centralizer_.php?mod=TOTALS_SALE_COMB&from=20170123&to=20170123&warehouse_id=206&qty_sale=kardex&type_cost=last): failed to open stream: Connection timed out /usr/local/apache/htdocs/ocsmanager/application/controllers/Requests.php 364
ERROR - 2017-01-24 17:34:37 --> Error al conectarse a http://192.168.11.1/sistemaweb/centralizer_.php?mod=TOTALS_SALE_COMB&from=20170123&to=20170123&warehouse_id=206&qty_sale=kardex&type_cost=last
ERROR - 2017-01-24 19:01:53 --> Severity: Parsing Error --> syntax error, unexpected T_CONSTANT_ENCAPSED_STRING /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 7
ERROR - 2017-01-24 19:01:54 --> Severity: Parsing Error --> syntax error, unexpected T_CONSTANT_ENCAPSED_STRING /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 7
ERROR - 2017-01-24 19:02:11 --> Severity: Parsing Error --> syntax error, unexpected T_IS_EQUAL, expecting ')' /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 3
ERROR - 2017-01-24 19:02:12 --> Severity: Parsing Error --> syntax error, unexpected T_IS_EQUAL, expecting ')' /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 3
ERROR - 2017-01-24 19:06:23 --> Severity: Warning --> Missing argument 2 for getDateNow(), called in /usr/local/apache/htdocs/ocsmanager/application/controllers/Home.php on line 25 and defined /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 3
ERROR - 2017-01-24 19:06:55 --> Severity: Warning --> Missing argument 2 for getDateNow(), called in /usr/local/apache/htdocs/ocsmanager/application/controllers/Home.php on line 25 and defined /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 3
ERROR - 2017-01-24 19:06:57 --> Severity: Warning --> Missing argument 2 for getDateNow(), called in /usr/local/apache/htdocs/ocsmanager/application/controllers/Home.php on line 25 and defined /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 3
ERROR - 2017-01-24 19:06:58 --> Severity: Warning --> Missing argument 2 for getDateNow(), called in /usr/local/apache/htdocs/ocsmanager/application/controllers/Home.php on line 25 and defined /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 3
ERROR - 2017-01-24 19:07:04 --> Severity: Warning --> Missing argument 2 for getDateNow(), called in /usr/local/apache/htdocs/ocsmanager/application/controllers/Home.php on line 25 and defined /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 3
ERROR - 2017-01-24 19:09:33 --> Severity: Warning --> Missing argument 2 for getDateNow(), called in /usr/local/apache/htdocs/ocsmanager/application/controllers/Home.php on line 25 and defined /usr/local/apache/htdocs/ocsmanager/application/helpers/functions_helper.php 3

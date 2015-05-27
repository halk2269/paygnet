<?php

	$conn = mysql_connect("msn", "intertrade", "sdkf47hskfh837") or die("MySql ERROR");
	mysql_select_db("intertrade", $conn) or die("Error intertrade connection");
	
//	$conn = mysql_connect("msn", "kdcm", "cjhkfahsda") or die("MySql ERROR");
//	mysql_select_db("kdcm", $conn) or die("Error intertrade connection");


	
	echo "<table border=\"1\"><tr><th>ID</th><th>Name</th><th>Default Reference Rights</th><th>Default Section Rights</th></tr>";
	$SQL0 = "SELECT * FROM sys_roles";
	$res0 = mysql_query($SQL0);
	while ($r = mysql_fetch_object($res0)) {
		echo "<tr>";
		echo "<td>{$r->id}</td><td>{$r->name}</td><td>{$r->defrights}</td><td>{$r->defsectionrights}</td>";
		echo "</tr>";
	}
	echo "</table>";
	
	$SQL = "SELECT * FROM sys_sections";
	$res = mysql_query($SQL);
	
	echo "<table border=\"1\">";
	echo "<tr><th>ID</th><th>pID</th><th>Name</th><th>Enabled</th><th>XSLT</th><th>Auth</th><th>Section Rights</th><th>References</th></tr>";
	
	if ($res !== false) {
		while ($section = mysql_fetch_object($res))	{
			echo "<tr>";
			echo "<td><h1>{$section->id}</h1></td><td>{$section->parent_id}</td><td><strong>{$section->name}</strong></td><td>{$section->enabled}</td><td>{$section->xslt}</td><td>{$section->auth}</td>";
			$SQL1 = "SELECT * FROM sys_section_rights WHERE `section_id` = '{$section->id}'";
			$res1 = mysql_query($SQL1);
			echo "<td>";
			if (mysql_num_rows($res1) > 0) {
				echo "<table border=\"1\"><tr><th>role id</th><th>rights</th></tr>";
				while ($role = mysql_fetch_object($res1)) {
					echo "<tr><td>{$role->role_id}</td><td>{$role->rights}</td></tr>";
				}			
				echo "</table>";
			}
			echo "</td>";
			
			$SQL2 = "SELECT * FROM sys_references WHERE `ref` = '{$section->id}'";
			$res2 = mysql_query($SQL2);
			if (mysql_num_rows($res2) > 0) {
				echo "<td><table border=\"1\"><tr><th>RefID</th><th>Rights</th></tr>";
				while ($ref = mysql_fetch_object($res2)) {
					echo "<tr><td><h3>{$ref->id}</h3></td><td>";
				
					$SQL3 = "SELECT * FROM sys_ref_rights WHERE `ref_id` = '{$ref->id}'";
					$res3 = mysql_query($SQL3);
					if (mysql_num_rows($res3) > 0) {
						echo "<table border=\"1\"><tr><th>Role ID</th><th>Rights</th></tr>";
						while ($refRights = mysql_fetch_object($res3)) {
							echo "<tr><td>{$refRights->role_id}</td><td>{$refRights->rights}</td></tr>";
						}
						echo "</table>";
					}
					echo "</td></tr>";
				}
				echo "</table></td>";
			}
			
			
	
			echo "</tr>";
		}

		/*
		foreach ($allsections as $section) {
			echo "<tr>";
			echo "<td>";
			
			echo "</td>";
			echo "</tr>";
		}
		*/

	}
	echo "</table>";
	mysql_close($conn);

?>
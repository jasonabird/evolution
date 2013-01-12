<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('new_plugin')) {	
	$e->setError(3);
	$e->dumpError();	
}

$id=$_GET['id'];

// duplicate Plugin
$sql = "INSERT INTO $dbase.`" . $table_prefix . "site_plugins` (name, description, disabled, moduleguid, plugincode, properties, category) 
		SELECT CONCAT('Duplicate of ',name) AS 'name', description, disabled, moduleguid, plugincode, properties, category 
		FROM $dbase.`" . $table_prefix . "site_plugins` WHERE id=$id;";
$rs = $modx->db->query($sql);

if ($rs) {
	$newid = $modx->db->getInsertId();
}
else {
	echo "A database error occured while trying to duplicate plugin: <br /><br />" . $modx->db->getLastError();
	exit;
}

// duplicate Plugin Event Listeners
$sql = "INSERT INTO $dbase.`" . $table_prefix . "site_plugin_events` (pluginid,evtid,priority)
		SELECT $newid, evtid, priority
		FROM $dbase.`" . $table_prefix . "site_plugin_events` WHERE pluginid=$id;";
$rs = $modx->db->query($sql);

if (!$rs) {
	echo "A database error occured while trying to duplicate plugin events: <br /><br />" . $modx->db->getLastError();
	exit;
}

// finish duplicating - redirect to new plugin
$header="Location: index.php?r=2&a=102&id=$newid";
header($header);
?>

<?php

// Admin page header
add_action('admin_head', 'sociable_admin_head');
function sociable_admin_head() {
	?>

<!-- The ToolMan lib provides drag and drop: http://tool-man.org/examples/sorting.html -->
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/sociable-zyblog-edition/js/tool-man/core.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/sociable-zyblog-edition/js/tool-man/coordinates.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/sociable-zyblog-edition/js/tool-man/css.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/sociable-zyblog-edition/js/tool-man/drag.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/sociable-zyblog-edition/js/tool-man/dragsort.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/sociable-zyblog-edition/js/tool-man/events.js"></script>
<script type="text/javascript"><!--
var dragsort = ToolMan.dragsort();
var junkdrawer = ToolMan.junkdrawer();

function save_reorder(id) {
	site_order = document.getElementById('site_order');

	old_order = site_order.value;
	new_order = junkdrawer.serializeList(document.getElementById('sociable_site_list'));
	site_order.value = new_order;

	if (!site_order.used || new_order == old_order)
		toggle_checkbox(id);
	site_order.used = true;
}

/* make checkbox action prettier */
function toggle_checkbox(id) {
	var checkbox = document.getElementById(id);

	checkbox.checked = !checkbox.checked;
	if (checkbox.checked)
		checkbox.parentNode.className = 'active';
	else
		checkbox.parentNode.className = 'inactive';
}
--></script>

<link rel="stylesheet" type="text/css" media="screen" href="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/sociable-zyblog-edition/css/sociable-admin.css" />
<?php
}

function sociable_message($message) {
	echo "<div id=\"message\" class=\"updated fade\"><p>$message</p></div>\n";
}

// Sanity check the upload worked
function sociable_upload_errors() {
	global $sociable_files;

	$cwd = getcwd(); // store current dir for restoration
	if (!@chdir('../wp-content/plugins'))
		return __("Couldn't find wp-content/plugins folder. Please make sure WordPress is installed correctly.", 'sociable');
	if (!is_dir(plugin_basename(dirname(__FILE__))))
		return __("Can't find sociable folder.", 'sociable');
	chdir(str_replace('/php', '', dirname( plugin_dir_path(__FILE__) )) );

	
	foreach($sociable_files as $file) {
		if (substr($file, -1) == '/') {
			if (!is_dir(substr($file, 0, strlen($file) - 1)))
				return __("Can't find folder:", 'sociable') . " <kbd>$file</kbd>";
		} else if (!is_file($file))
		return __("Can't find file:", 'sociable') . " <kbd>$file</kbd>";
	}


	$header_filename = '../../themes/' . get_option('template') . '/header.php';
	if (!file_exists($header_filename) or strpos(@file_get_contents($header_filename), 'wp_head()') === false)
		return __("Your theme isn't set up for Sociable to load its style. Please edit <kbd>header.php</kbd> and add a line reading <kbd>&lt?php wp_head(); ?&gt;</kbd> before <kbd>&lt;/head&gt;</kbd> to fix this.", 'sociable');

	chdir($cwd); // restore cwd

	return false;
}


// Hook the admin_menu display to add admin page
add_action('admin_menu', 'sociable_admin_menu');
function sociable_admin_menu() {
	add_submenu_page('options-general.php', 'Sociable ZyEdition', 'Sociable ZyEdition', 'manage_options', 'sociable-zyblog-edition', 'sociable_submenu');
}

// The admin page
function sociable_submenu() {
	global $sociable_known_sites, $sociable_files;


	// update options in db if requested
	if (isset($_REQUEST['restore']) && $_REQUEST['restore']) {
		sociable_restore_config(True);
	sociable_message(__("Restored all settings to defaults.", 'sociable'));
	} else if (isset($_REQUEST['save']) && $_REQUEST['save']) {
		// update active sites
		$active_sites = Array();
		if (!$_REQUEST['active_sites'])
			$_REQUEST['active_sites'] = Array();
		foreach($_REQUEST['active_sites'] as $sitename=>$dummy)
			$active_sites[] = $sitename;
		update_option('sociable_active_sites', $active_sites);
		// have to delete and re-add because update doesn't hit the db for identical arrays
		// (sorting does not influence associated array equality in PHP)
		delete_option('sociable_active_sites', $active_sites);
		add_option('sociable_active_sites', $active_sites);

		// update conditional displays
		$conditionals = Array();
		if (!$_REQUEST['conditionals'])
			$_REQUEST['conditionals'] = Array();
		foreach(get_option('sociable_conditionals') as $condition=>$toggled)
			$conditionals[$condition] = array_key_exists($condition, $_REQUEST['conditionals']);
		update_option('sociable_conditionals', $conditionals);

		// update tagline
		if (!$_REQUEST['tagline'])
			$_REQUEST['tagline'] = "";
		update_option('sociable_tagline', $_REQUEST['tagline']);

		sociable_message(__("Saved changes.", 'sociable'));
	}

	if ($str = sociable_upload_errors())
		sociable_message("$str</p><p>" . __("In your plugins/sociable folder, you must have these files:", 'sociable') . ' <pre>' . implode("\n", $sociable_files) );

	// show active sites first and in order
	$active_sites = get_option('sociable_active_sites');
	$active = Array(); $disabled = $sociable_known_sites;
	foreach($active_sites as $sitename) {
		$active[$sitename] = $disabled[$sitename];
		unset($disabled[$sitename]);
	}
	uksort($disabled, "strnatcasecmp");

	// load options from db to display
	$tagline = get_option('sociable_tagline');
	$conditionals = get_option('sociable_conditionals');

	// display options
?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

<div class="wrap" id="sociable_options">
<fieldset id="sociable_sites">

<h3><?php _e("Sociable Options", 'sociable'); ?></h3>

<p><?php _e("Drag and drop sites to reorder them. Only the sites you check will appear publicly.", 'sociable'); ?></p>

<ul id="sociable_site_list">
<?php foreach (array_merge($active, $disabled) as $sitename=>$site) { ?>
	<li
		id="<?php echo $sitename; ?>"
		class="sociable_site <?php echo (in_array($sitename, $active_sites)) ? "active" : "inactive"; ?>"
		onmouseup="javascript:save_reorder('cb_<?php echo $sitename; ?>');"
	>
		<input type="checkbox" id="cb_<?php echo $sitename; ?>" class="checkbox" name="active_sites[<?php echo $sitename; ?>]" onclick="javascript:toggle_checkbox('cb_<?php echo $sitename; ?>');" <?php echo (in_array($sitename, $active_sites)) ? ' checked="checked"' : ''; ?>
		/>
		<img src="../wp-content/plugins/sociable-zyblog-edition/images/<?php echo $site['favicon']?>" width="16" height="16" alt="" />
		<?php print $sitename; ?>
	</li>
<?php } ?>
</ul>
<input type="hidden" id="site_order" name="site_order" value="<?php echo join('|', array_keys($sociable_known_sites)) ?>" />
<script type="text/javascript"><!--
	dragsort.makeListSortable(document.getElementById("sociable_site_list"));
--></script>

</fieldset>
<div style="clear: left; display: none;"><br/></div>

<fieldset id="sociable_tagline">
<p>
<?php _e("Change the text displayed in front of the icons below. For complete customization, edit <kbd>sociable.css</kbd> in the Sociable plugin directory.", 'sociable'); ?>
</p>
<input type="text" name="tagline" value="<?php echo htmlspecialchars($tagline); ?>" />
</fieldset>


<fieldset id="sociable_conditionals">
<p><?php _e("The icons appear at the end of each blog post, and posts may show on many different types of pages. Depending on your theme and audience, it may be tacky to display icons on all types of pages.", 'sociable'); ?></p>

<ul style="list-style-type: none">
	<li><input type="checkbox" name="conditionals[is_home]"<?php echo ($conditionals['is_home']) ? ' checked="checked"' : ''; ?> /> <?php _e("Front page of the blog", 'sociable'); ?></li>
	<li><input type="checkbox" name="conditionals[is_single]"<?php echo ($conditionals['is_single']) ? ' checked="checked"' : ''; ?> /> <?php _e("Individual blog posts", 'sociable'); ?></li>
	<li><input type="checkbox" name="conditionals[is_page]"<?php echo ($conditionals['is_page']) ? ' checked="checked"' : ''; ?> /> <?php _e('Individual WordPress "Pages"', 'sociable'); ?></li>
	<li><input type="checkbox" name="conditionals[is_category]"<?php echo ($conditionals['is_category']) ? ' checked="checked"' : ''; ?> /> <?php _e("Category archives", 'sociable'); ?></li>
	<li><input type="checkbox" name="conditionals[is_date]"<?php echo ($conditionals['is_date']) ? ' checked="checked"' : ''; ?> /> <?php _e("Date-based archives", 'sociable'); ?></li>
	<li><input type="checkbox" name="conditionals[is_search]"<?php echo ($conditionals['is_search']) ? ' checked="checked"' : ''; ?> /> <?php _e("Search results", 'sociable'); ?></li>
</ul>
</fieldset>

<p class="submit"><input name="save" id="save" tabindex="3" value="<?php _e("Save Changes", 'sociable'); ?>" type="submit" /></p>
<p class="submit"><input name="restore" id="restore" tabindex="3" value="<?php _e("Restore Built-in Defaults", 'sociable'); ?>" type="submit" style="border: 2px solid #e00;" /></p>

</div>

</form>

<?php
}
?>
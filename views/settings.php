<?php
$setting_prefix = $this->SETTING_PREFIX;
$settings       = $this->get_settings();

require_once( plugin_dir_path( __FILE__ ) . '../ui/templates/colorpicker.html' );

require_once( plugin_dir_path( __FILE__ ) . '../ui/templates/_display_settings.html' );
require_once( plugin_dir_path( __FILE__ ) . '../ui/templates/_preview_area.html' );
require_once( plugin_dir_path( __FILE__ ) . '../ui/templates/_style_settings.html' );
require_once( plugin_dir_path( __FILE__ ) . '../ui/templates/dropdown.html' );
require_once( plugin_dir_path( __FILE__ ) . '../ui/templates/fontpicker.html' );
require_once( plugin_dir_path( __FILE__ ) . '../ui/templates/settings.php' );
?>
<form method="post" action="options.php">
	<?php @settings_fields( $setting_prefix ); ?>
	<?php @do_settings_fields( $setting_prefix, '' ); ?>
	<div id="settings_wrapper"></div>
</form>

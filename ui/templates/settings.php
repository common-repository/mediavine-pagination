<?php
$setting_prefix = $this->SETTING_PREFIX;
?>
<script type="text/template" id="mediavine-settings-view">
	<h1>Mediavine Pagination - Settings</h1>
	<div class="media-ui ">

		<div class="row">
			<div class="col-md-6 col-sm-12">
				<div class="bb-partial"
				     data-template="_style_settings"
				     data-model="model">
				</div>
				<?php @submit_button( 'Save Settings', 'primary mediavine-save', 'submit', FALSE ); ?>
			</div>
			<div class="col-md-6 col-sm-12">
				<div class="box">
					<h3>Teaser Text Settings</h3>
					<section>
						<div class="row">
							<div class="col-xs-10">
								<label for="input-teaser-text">Teaser Text</label>
								<div class="mediavine-pagination tip">
									You can use <strong>{title}</strong> to use the title of the post you're viewing
								</div>
								<input type="text"
								       id="<?php echo $setting_prefix; ?>teaser_text"
								       value="<?php echo $settings[ 'teaser_text' ]; ?>"
								       name="<?php echo $setting_prefix; ?>teaser_text"
								       class="mv-teaser-text"/>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="box">
									<label for="input-text-font-style">Font Style</label>
									<div id="bb_teaser_font_style"
									     data-name="<?php echo $setting_prefix; ?>teaser_font_style"
									     data-value="<?php echo $settings[ 'teaser_font_style' ]; ?>"
									     data-modName="<?php echo $setting_prefix; ?>teaser_font_mod"
									     data-modValue="<?php echo $settings[ 'teaser_font_mod' ]; ?>">
									</div>
								</div>
							</div>
							<div class="col-md-3 col-sm-12">
								<label for="input-text-font-color">Font Color</label>
								<div id="bb_teaser_font_color"
								     data-id="<?php echo $setting_prefix; ?>teaser_font_color"
								     data-name="<?php echo $setting_prefix; ?>teaser_font_color"
								     data-value="<?php echo $settings[ 'teaser_font_color' ]; ?>">
								</div>
							</div>
						</div>
					</section>

					<h3>Button Settings</h3>
					<section>
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="box">
									<label for="input-text-font-style">Font Style</label>
									<div id="bb_button_font_style"
									     data-name="<?php echo $setting_prefix; ?>button_font_style"
									     data-value="<?php echo $settings[ 'button_font_style' ]; ?>"
									     data-modName="<?php echo $setting_prefix; ?>button_font_mod"
									     data-modValue="<?php echo $settings[ 'button_font_mod' ]; ?>">
									</div>
								</div>
							</div>
							<div class="col-md-3 col-sm-12">
								<label for="input-text-font-color">Font Color</label>
								<!-- Color picker -->
								<div id="bb_button_font_color"
								     data-id="<?php echo $setting_prefix; ?>button_font_color"
								     data-value="<?php echo $settings[ 'button_font_color' ]; ?>"
								     data-name="<?php echo $setting_prefix; ?>button_font_color">
								</div>
							</div>
						</div>
						<!-- Background -->
						<div class="row">
							<div class="col-md-3 col-sm-12">
								<div class="box">
									<label for="input-text-font-color">Background Color</label>
									<!-- Color picker -->
									<div id="bb_button_background_color"
									     data-id="<?php echo $setting_prefix; ?>button_background_color"
									     data-value="<?php echo $settings[ 'button_background_color' ]; ?>"
									     data-name="<?php echo $setting_prefix; ?>button_background_color">
									</div>
								</div>
							</div>
							<div class="col-md-3 col-sm-12">
								<div class="box">
									<label for="input-text-font-color">Border Style</label>
									<div id="bb_button_border_style"
									     data-id="<?php echo $setting_prefix; ?>button_border_style"
									     data-value="<?php echo $settings[ 'button_border_style' ]; ?>"
									     data-name="<?php echo $setting_prefix; ?>button_border_style">
									</div>
								</div>
							</div>
							<div class="col-md-3 col-sm-12">
								<div class="box">
									<label for="input-text-font-color">Border Color</label>
									<!-- Color picker -->
									<div id="bb_button_border_color"
									     data-id="<?php echo $setting_prefix; ?>button_border_color"
									     data-value="<?php echo $settings[ 'button_border_color' ]; ?>"
									     data-name="<?php echo $setting_prefix; ?>button_border_color">
									</div>
								</div>
							</div>
						</div>
					</section>

					<h3>Display Settings</h3>
					<section>
						<div class="bb-partial"
						     data-template="_display_settings"
						     data-model="model">
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
</script>

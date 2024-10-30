<?php
$btn_margin = $styles[ 'button_font_size' ] / 2;
$colorMod   = - 20;
if ( $this->colorIsDark( $styles[ 'button_background_color' ] ) ) {
	$colorMod = 20;
}
$hoverbg = $this->adjustBrightness( $styles[ 'button_background_color' ], $colorMod );

$button_font_mod = $this->parse_font_mod( $styles[ 'button_font_mod' ] );
$teaser_font_mod = $this->parse_font_mod( $styles[ 'teaser_font_mod' ] );
$hidebuttons     = '';
if ( 'next' === $this->option( 'display_setting' ) ) {
	$hidebuttons = 'display: none;';
}
$style_block = <<<EOT
<style>
	.mediavine-pagination {
	                text-align: center;
	}

	.mediavine-pagination a {
		background: {$styles['button_background_color']};
		color: {$styles['button_font_color']};
		border-width: {$styles['button_border_size']}px;
		border-color: {$styles['button_border_color']};
		border-style: {$styles['button_border_style']};
		font-size: {$styles['button_font_size']}px;
		margin-bottom: {$styles['button_font_size']}px;
		padding: 2px {$btn_margin}px;
		border-radius: 5px;
		text-align: center;
		text-decoration: none;
		display: inline-block;
		{$button_font_mod}
	}
	
	.mediavine-pagination > span {
		border-radius: 5px;
		background: {$styles['button_font_color']};
		color: {$styles['button_background_color']};
				text-decoration: none;
		font-size: {$styles['button_font_size']}px;
		margin-bottom: {$btn_margin}px;
		padding: {$btn_margin}px;
		{$button_font_mod}
	}
	
	.mediavine-pagination a:hover {
		background: {$hoverbg};
		text-decoration: none;
	}
	
	.mediavine-pagination > a:not(.nextpage){
		{$hidebuttons}
	}
	
	.mediavine-pagination .nextpage{
		display: block;
		width: 100%;
	}
	
	.mediavine-pagination > a, .mediavine-pagination > span{
		margin-left: ${btn_margin}px;
		margin-right: ${btn_margin}px;
				white-space: nowrap;
	}
	
	.mediavine-pagination .nextpage a{
		display: block;
		width: 100%;
	}

	.mediavine-pagination h4.teaser-text {
		color: {$styles['teaser_font_color']};
		font-size: {$styles['teaser_font_size']}px;
		font-color: {$styles['teaser_font_color']};
		font-family: {$styles['teaser_font_style']};
		{$teaser_font_mod}
	}
</style>
EOT;
?>

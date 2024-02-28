<?php
/**
 * The search box template.
 *
 * @since      4.1.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/templates
 * @author     wptools.io
 */

/**
 * @var string $placeholder   The input field placeholder text.
 * @var string $value         The input field value.
 * @var string $icon_position Determines the icon position, left or right side.
 * @var string $with_cross    Determines if we show the cross icon to clear the input field.
 */

$classes = 'wcapf-search-box';

if ( 'left' === $icon_position || 'right' === $icon_position ) {
	$classes .= ' with-icon icon-' . $icon_position;
}

if ( $with_cross ) {
	$classes .= ' with-cross';
}
?>

<div class="<?php echo esc_attr( $classes ); ?>">
	<input type="text" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php echo esc_attr( $value ); ?>">

	<?php if ( 'left' === $icon_position || 'right' === $icon_position ): ?>
		<span class="wcapf-search-icon">
			<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path
					fill-rule="evenodd"
					clip-rule="evenodd"
					d="M17.0392 15.6244C18.2714 14.084 19.0082 12.1301 19.0082 10.0041C19.0082 5.03127 14.9769 1 10.0041 1C5.03127 1 1 5.03127 1 10.0041C1 14.9769 5.03127 19.0082 10.0041 19.0082C12.1301 19.0082 14.084 18.2714 15.6244 17.0392L21.2921 22.707C21.6828 23.0977 22.3163 23.0977 22.707 22.707C23.0977 22.3163 23.0977 21.6828 22.707 21.2921L17.0392 15.6244ZM10.0041 17.0173C6.1308 17.0173 2.99087 13.8774 2.99087 10.0041C2.99087 6.1308 6.1308 2.99087 10.0041 2.99087C13.8774 2.99087 17.0173 6.1308 17.0173 10.0041C17.0173 13.8774 13.8774 17.0173 10.0041 17.0173Z"
					fill="currentColor"
				/>
			</svg>

			<button class="wcapf-clear-state">
				<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g>
						<path
							d="M18 18L12 12M12 12L6 6M12 12L18 6M12 12L6 18"
							stroke="currentColor"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
						/>
					</g>
				</svg>
			</button>
		</span>
	<?php endif; ?>
</div>

<?php
/**
 * The template to show the range slider filter.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/templates
 * @author     wptools.io
 */

/**
 * @var string $filter_id
 * @var string $filter_key
 * @var string $display_type
 * @var string $display_values_as
 * @var string $alignment
 * @var string $input_type_number
 * @var string $min_value
 * @var string $max_value
 * @var string $range_min_value
 * @var string $range_max_value
 * @var string $step
 * @var string $value_prefix
 * @var string $value_postfix
 * @var string $values_separator
 * @var string $text_before_min_value
 * @var string $text_before_max_value
 * @var string $format_numbers
 * @var string $decimal_places
 * @var string $thousand_separator
 * @var string $decimal_separator
 * @var string $slider_id
 * @var string $slider_style
 * @var string $filter_url
 * @var string $clear_filter_url
 */

// We don't show value postfix and prefix at the same time.
$value_unit    = '';
$unit_position = '';

if ( $value_prefix ) {
	$value_unit    = $value_prefix;
	$unit_position = 'left';
} elseif ( $value_postfix ) {
	$value_unit    = $value_postfix;
	$unit_position = 'right';
}

if ( 'range_number' === $display_type ) {
	$display_values_as = 'input_field';
}

if ( 'input_field' === $display_values_as ) {
	$value_unit = str_replace( '&nbsp;', '', $value_unit );
}

$_attrs = array();

$_attrs[] = 'data-range-min-value="' . $range_min_value . '"';
$_attrs[] = 'data-range-max-value="' . $range_max_value . '"';
$_attrs[] = 'data-min-value="' . $min_value . '"';
$_attrs[] = 'data-max-value="' . $max_value . '"';
$_attrs[] = 'data-step="' . $step . '"';
$_attrs[] = 'data-format-numbers="' . $format_numbers . '"';
$_attrs[] = 'data-decimal-places="' . $decimal_places . '"';
$_attrs[] = 'data-thousand-separator="' . $thousand_separator . '"';
$_attrs[] = 'data-decimal-separator="' . $decimal_separator . '"';
$_attrs[] = 'data-display-values-as="' . $display_values_as . '"';
$_attrs[] = 'data-url="' . esc_url( $filter_url ) . '"';
$_attrs[] = 'data-clear-filter-url="' . esc_url( $clear_filter_url ) . '"';

$attrs = implode( ' ', $_attrs );

// We don't show the text before min and max values when displaying the slider values in input fields.
if ( 'input_field' === $display_values_as ) {
	$text_before_min_value = '';
	$text_before_max_value = '';
}

$input_type      = 'text';
$show_as_spinbox = false;
$spinbox_attrs   = '';

if (
	$input_type_number &&
	( 'range_slider' === $display_type && 'input_field' === $display_values_as ) || 'range_number' === $display_type
) {
	$input_type      = 'number';
	$show_as_spinbox = true;

	$spinbox_attrs .= 'step="' . esc_attr( $step ) . '"';
	$spinbox_attrs .= 'min="' . esc_attr( $range_min_value ) . '"';
	$spinbox_attrs .= 'max="' . esc_attr( $range_max_value ) . '"';
}

// Do the formatting.
if ( $format_numbers ) {
	$min_value = number_format( $min_value, $decimal_places, $decimal_separator, $thousand_separator );
	$max_value = number_format( $max_value, $decimal_places, $decimal_separator, $thousand_separator );
}

// Range wrapper classes.
$range_wrapper_classes = 'wcapf-range-wrapper';

if ( $show_as_spinbox ) {
	$range_wrapper_classes .= ' wcapf-range-spinbox';
}

// Add slider preset class.
if ( 'range_slider' === $display_type ) {
	$range_wrapper_classes .= ' wcapf-range-slider';
	$range_wrapper_classes .= ! empty( $slider_style ) ? ' ' . $slider_style : ' style-1';
} else {
	$range_wrapper_classes .= ' wcapf-range-number';
}

// Wrapper classes.
$wrapper_classes = 'range-values';

if ( 'range_number' === $display_type ) {
	$display_values_as = 'input_field';
}

$wrapper_classes .= ' display-values-as-' . $display_values_as;

if ( $unit_position && ! $show_as_spinbox ) {
	$wrapper_classes .= ' unit-position-' . $unit_position;
}

if ( 'input_field' === $display_values_as || 'justified' === $alignment ) {
	$wrapper_classes .= ' justify-between';
} elseif ( 'centered' === $alignment ) {
	$wrapper_classes .= ' justify-center';
}
?>

<div class="<?php echo esc_attr( $range_wrapper_classes ); ?>" <?php echo $attrs; ?>>
	<div class="<?php echo esc_attr( $wrapper_classes ); ?>">
		<span class="wcapf-range-start">
			<?php if ( $text_before_min_value ) : ?>
				<span class="wcapf-min-value-prefix"><?php echo wp_kses_post( $text_before_min_value ); ?></span>
			<?php endif; ?>

			<?php if ( 'left' === $unit_position && ! $show_as_spinbox ) : ?>
				<span class="wcapf-range-unit"><?php echo sanitize_text_field( $value_unit ); ?></span>
			<?php endif; ?>

			<?php if ( 'plain_text' === $display_values_as ) : ?>
				<span class="min-value"><?php echo esc_html( $min_value ); ?></span>
			<?php else: ?>
				<input
					type="<?php echo esc_attr( $input_type ); ?>"
					class="min-value"
					id="<?php echo esc_attr( $filter_key ); ?>-<?php echo esc_attr( $filter_id ); ?>-min"
					value="<?php echo esc_attr( $min_value ); ?>"
					autocomplete="off"
					<?php echo $spinbox_attrs; ?>
				>
			<?php endif; ?>

			<?php if ( 'right' === $unit_position && ! $show_as_spinbox ) : ?>
				<span class="wcapf-range-unit"><?php echo sanitize_text_field( $value_unit ); ?></span>
			<?php endif; ?>
		</span>

		<?php if ( $values_separator ) : ?>
			<span class="wcapf-range-separator"><?php echo wp_kses_post( $values_separator ); ?></span>
		<?php endif; ?>

		<span class="wcapf-range-end">
			<?php if ( $text_before_max_value ) : ?>
				<span class="wcapf-max-value-prefix"><?php echo wp_kses_post( $text_before_max_value ); ?></span>
			<?php endif; ?>

			<?php if ( 'left' === $unit_position && ! $show_as_spinbox ) : ?>
				<span class="wcapf-range-unit"><?php echo sanitize_text_field( $value_unit ); ?></span>
			<?php endif; ?>

			<?php if ( 'plain_text' === $display_values_as ) : ?>
				<span class="max-value"><?php echo esc_html( $max_value ); ?></span>
			<?php else: ?>
				<input
					type="<?php echo esc_attr( $input_type ); ?>"
					class="max-value"
					id="<?php echo esc_attr( $filter_key ); ?>-<?php echo esc_attr( $filter_id ); ?>-max"
					value="<?php echo esc_attr( $max_value ); ?>"
					autocomplete="off"
					<?php echo $spinbox_attrs; ?>
				>
			<?php endif; ?>

			<?php if ( 'right' === $unit_position & ! $show_as_spinbox ) : ?>
				<span class="wcapf-range-unit"><?php echo sanitize_text_field( $value_unit ); ?></span>
			<?php endif; ?>
		</span>
	</div>

	<?php if ( 'range_slider' === $display_type ) : ?>
		<div id="<?php echo esc_attr( $slider_id ); ?>" class="wcapf-noui-slider"></div>
	<?php endif; ?>
</div>

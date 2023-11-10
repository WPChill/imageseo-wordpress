<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class FieldFactory {

	/**
	 * @param $option
	 *
	 * @return FieldFactory
	 */
	public static function make( $option ) {

		$field = null;

		// get value
		$value = imageseo_get_service( 'Option' )->getOption( $option['name'] );
		$default = ( ! empty( $option['std'] ) ) ? $option['std'] : '';

		// placeholder
		$placeholder = ( ! empty( $option['placeholder'] ) ) ? $option['placeholder'] : '';

		switch ( $option['type'] ) {
			case 'text':
				$field = new Text( $option['name'], $value, $placeholder );
				break;
			case 'hidden':
				$field = new Hidden( $option['name'], $default );
				break;
			case 'colorpicker':
				$field = new ColorPicker( $option['name'], $value, $placeholder );
				break;
			case 'email':
				$field = new Email( $option['name'], $value, $placeholder );
				break;
			case 'password':
				$field = new Password( $option['name'], $value, $placeholder );
				break;
			case 'textarea':
				$field = new Textarea( $option['name'], $value, $placeholder );
				break;
			case 'checkbox':
				$field = new Checkbox( $option['name'], $value, $option['cb_label'] );
				break;
			case 'radio':
				$field = new Radio( $option['name'], $value, $option['options'], $option['std'] );
				break;
			case 'format':
				$field = new Format( $option['name'], $value, $option['options'], $option['std'] );
				break;
			case 'select':
				$field = new Select( $option['name'], $value, $option['options'] );
				break;
			case 'multi_checkbox':
				$field = new MultiCheckbox( $option['name'], $value, $option['options'] );
				break;
			case 'sub_checkbox':
				$field = new SubCheckbox( $option['name'], $value, $option['options'], $option['parent'] );
				break;
			case 'action_button':
				$field = new ActionButton( $option['name'], $option['link'], $option['label'] );
				break;
			case 'title':
				$field = new Title( $option['title'] );
				break;
			case 'install_plugin':
				$field = new InstallPlugin( $option['name'], $option['link'], $option['label'] );
				break;
			default:
				/**
				 * do_filter: imageseo_setting_field_$type: (null) $field, (array) $option, (String) $value, (String) $placeholder
				 */
				$field = apply_filters( 'imageseo_setting_field_' . $option['type'], $field, $option, $value, $placeholder );
				break;
		}

		return $field;
	}

}

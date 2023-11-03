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
		$value = get_option( $option['name'], '' );

		// placeholder
		$placeholder = ( ! empty( $option['placeholder'] ) ) ? $option['placeholder'] : '';

		switch ( $option['type'] ) {
			case 'text':
				$field = new Text( $option['name'], $value, $placeholder );
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
			case 'select':
				$field = new Select( $option['name'], $value, $option['options'] );
				break;
			case 'action_button':
				$field = new ActionButton( $option['name'], $option['link'], $option['label'] );
				break;
			case 'install_plugin':
				$field = new InstallPlugin( $option['name'], $option['link'], $option['label'] );
				break;
			default:
				/**
				 * do_filter: dlm_setting_field_$type: (null) $field, (array) $option, (String) $value, (String) $placeholder
				 */
				$field = apply_filters( 'imageseo_setting_field_' . $option['type'], $field, $option, $value, $placeholder );
				break;
		}

		return $field;
	}

}

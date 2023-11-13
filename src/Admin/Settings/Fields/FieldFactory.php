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
		if ( ! empty( $option['parent'] ) ) {
			$value = imageseo_get_service( 'Option' )->getOption( $option['parent'] )[ $option['name'] ];
		} else {
			$value = imageseo_get_service( 'Option' )->getOption( $option['name'] );
		}

		switch ( $option['type'] ) {
			case 'text':
				$field = new Text( $option, $value );
				break;
			case 'file_picker':
				$field = new FilePicker( $option, $value );
				break;
			case 'hidden':
				$field = new Hidden( $option, $value );
				break;
			case 'colorpicker':
				$field = new ColorPicker( $option, $value );
				break;
			case 'email':
				$field = new Email( $option, $value );
				break;
			case 'password':
				$field = new Password( $option, $value );
				break;
			case 'textarea':
				$field = new Textarea( $option, $value );
				break;
			case 'checkbox':
				$field = new Checkbox( $option, $value );
				break;
			case 'radio':
				$field = new Radio( $option, $value );
				break;
			case 'format':
				$field = new Format( $option, $value );
				break;
			case 'select':
				$field = new Select( $option, $value );
				break;
			case 'multi_checkbox':
				$field = new MultiCheckbox( $option, $value );
				break;
			case 'sub_checkbox':
				$field = new SubCheckbox( $option, $value );
				break;
			case 'action_button':
				if ( ! isset( $option['link'] ) ) {
					$option['link'] = '#';
				}
				$field = new ActionButton( $option, $value );
				break;
			case 'title':
				$field = new Title( $option );
				break;
			case 'install_plugin':
				$field = new InstallPlugin( $option, $value );
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

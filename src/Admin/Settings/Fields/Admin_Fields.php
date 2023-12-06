<?php

namespace ImageSeoWP\Admin\Settings\Fields;

/**
 * Abstract Admin_Fields class.
 *
 * @abstract
 */
class Admin_Fields {
	/** @var String */
	private $name;
	/** @var String */
	private $value;
	/** @var String */
	private $placeholder;
	private $title;
	private $cb_label;
	private $options;
	private $default;
	private $parent;
	private $desc;
	private $label;
	private $link;
	private $id;

	/**
	 * Admin_Fields constructor.
	 *
	 * @param array $option
	 * @param mixed $value
	 */
	public function __construct( $option, $value = false ) {
		$this->name        = $option['name'];
		$this->value       = ! empty( $value ) ? $value : '';
		$this->placeholder = ! empty( $option['placeholder'] ) ? $option['placeholder'] : '';
		$this->options     = ! empty( $option['options'] ) ? $option['options'] : '';
		$this->default     = ! empty( $option['std'] ) ? $option['std'] : '';
		$this->parent      = ! empty( $option['parent'] ) ? $option['parent'] : '';
		$this->desc        = ! empty( $option['desc'] ) ? $option['desc'] : '';
		$this->label       = ! empty( $option['label'] ) ? $option['label'] : '';
		$this->cb_label    = ! empty( $option['cb_label'] ) ? $option['cb_label'] : '';
		$this->link        = ! empty( $option['link'] ) ? $option['link'] : '';
		$this->id          = $this->name;
		$this->title       = ! empty( $option['title'] ) ? $option['title'] : '';
	}

	public function get_id() {
		return $this->id;
	}

	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * @return String
	 */
	public function get_name() {
		if ( $this->parent ) {
			return $this->parent . '][' . $this->name;
		}

		return $this->name;
	}

	/**
	 * @param String $name
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * @return String
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * @param String $value
	 */
	public function set_value( $value ) {
		$this->value = $value;
	}

	/**
	 * @return String
	 */
	public function get_placeholder() {
		return $this->placeholder;
	}

	/**
	 * @param String $placeholder
	 */
	public function set_placeholder( $placeholder ) {
		$this->placeholder = $placeholder;
	}

	public function set_default( $default ) {
		$this->default = $default;
	}

	public function set_parent( $parent ) {
		$this->parent = $parent;
	}

	public function set_desc( $desc ) {
		$this->desc = $desc;
	}

	public function set_label( $label ) {
		$this->label = $label;
	}

	public function set_options( $options ) {
		$this->options = $options;
	}

	public function set_link( $link ) {
		$this->link = $link;
	}

	public function get_link() {
		return $this->link;
	}

	public function set_title( $title ) {
		$this->title = $title;
	}

	public function get_title() {
		return $this->title;
	}

	public function set_cb_label( $title ) {
		$this->cb_label = $title;
	}

	public function get_cb_label() {
		return $this->cb_label;
	}

	public function get_default() {
		return $this->default;
	}

	public function get_parent() {
		return $this->parent;
	}

	public function get_desc() {
		return $this->desc;
	}

	public function get_label() {
		return $this->label;
	}

	public function get_options() {
		return $this->options;
	}

	/**
	 * Echo the placeholder
	 */
	public function e_placeholder() {
		$placeholder = $this->get_placeholder();
		echo ( ! empty( $placeholder ) ) ? 'placeholder="' . esc_attr( $placeholder ) . '"' : '';
	}

	public function render() {
	}
}

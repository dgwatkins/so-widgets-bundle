<?php
/*
Widget Name: Button Grid
Description: Add multiple buttons in one go, customize individually, and present them in a neat grid layout.
Author: SiteOrigin
Author URI: https://siteorigin.com
Documentation: https://siteorigin.com/widgets-bundle/button-grid-widget/
*/

class SiteOrigin_Widget_Button_Grid_Widget extends SiteOrigin_Widget {
	private $settings;

	public function __construct() {
		parent::__construct(
			'sow-button-grid',
			__( 'SiteOrigin Button Grid', 'so-widgets-bundle' ),
			array(
				'description' => __( 'Add multiple buttons in one go, customize individually, and present them in a neat grid layout.', 'so-widgets-bundle' ),
				'help' => 'https://siteorigin.com/widgets-bundle/button-grid-widget/',
				'instance_storage' => true,
				'panels_title' => false,
			),
			array(),
			false,
			plugin_dir_path( __FILE__ )
		);
		add_filter( 'siteorigin_widgets_less_variables_sow-button', array( $this, 'override_button_less_variables' ), 10, 3 );

		add_filter( 'siteorigin_widgets_template_variables_sow-button', array( $this, 'override_button_variables' ), 10, 2 );
	}

	public function get_settings_form() {
		return array(
			'responsive_breakpoint' => array(
				'type' => 'measurement',
				'label' => __( 'Responsive Breakpoint', 'so-widgets-bundle' ),
				'default' => '780px',
				'description' => __( 'Device width, in pixels, to collapse into a mobile view.', 'so-widgets-bundle' ),
			),
		);
	}

	function get_widget_form() {
		return array(
			'buttons' => array(
				'type' => 'repeater',
				'label' => __( 'Buttons', 'so-widgets-bundle' ),
				'item_name' => __( 'Button', 'so-widgets-bundle' ),
				'item_label' => array(
					'selector' => "[id*='text']",
					'update_event' => 'change',
					'value_method' => 'val',
				),

				'fields' => array(
					'widget' => array(
						'type' => 'widget',
						'collapsible' => false,
						'class' => 'SiteOrigin_Widget_Button_Widget',
						'form_filter' => array( $this, 'filter_buttons_widget_form' ),
					),
				),
			),
			'layout' => array(
				'type' => 'section',
				'label' => __( 'Layout', 'so-widgets-bundle' ),
				'fields' => array(
					'desktop' => array(
						'type' => 'section',
						'label' => __( 'Desktop', 'so-widgets-bundle' ),
						'fields' => array(
							'system' => array(
								'type' => 'radio',
								'label' => __( 'Layout System', 'so-widgets-bundle' ),
								'default' => 'grid',
								'options' => array(
									'grid' => __( 'Grid', 'so-widgets-bundle' ),
									'flex' => __( 'Flex', 'so-widgets-bundle' ),
								),
								'state_emitter' => array(
									'callback' => 'select',
									'args' => array( 'system' )
								),
							),
							'alignment_flex' => array(
								'type' => 'select',
								'label' => __( 'Button Alignment', 'so-widgets-bundle' ),
								'default' => 'space-between',
								'state_handler' => array(
									'system[flex]' => array( 'show' ),
									'_else[system]' => array( 'hide' ),
								),
								'options' => array(
									'space-between' => __( 'Space Between Items', 'so-widgets-bundle' ),
									'space-evenly' => __( 'Items Spaced Evenly', 'so-widgets-bundle' ),
									'left' => __( 'Left', 'so-widgets-bundle' ),
									'center' => __( 'Center', 'so-widgets-bundle' ),
									'end' => __( 'Right', 'so-widgets-bundle' ),
								),
							),
							'columns' => array(
								'type' => 'number',
								'label' => __( 'Buttons Per Line', 'so-widgets-bundle' ),
								'default' => 3,
								'state_handler' => array(
									'system[grid]' => array( 'show' ),
									'_else[system]' => array( 'hide' ),
								),
							),
							'alignment_grid' => array(
								'type' => 'select',
								'label' => __( 'Button Alignment', 'so-widgets-bundle' ),
								'default' => 'center',
								'state_handler' => array(
									'system[grid]' => array( 'show' ),
									'_else[system]' => array( 'hide' ),
								),
								'options' => array(
									'left' => __( 'Left', 'so-widgets-bundle' ),
									'center' => __( 'Center', 'so-widgets-bundle' ),
									'right' => __( 'Right', 'so-widgets-bundle' ),
								),
							),
							'gap' => array(
								'type' => 'multi-measurement',
								'label' => __( 'Gap', 'so-widgets-bundle' ),
								'default' => '20px 20px',
								'measurements' => array(
									'row' => __( 'Row', 'so-widgets-bundle' ),
									'column' => __( 'Column', 'so-widgets-bundle' ),
								),
							),
						),
					),
					'mobile' => array(
						'type' => 'section',
						'label' => __( 'Mobile', 'so-widgets-bundle' ),
						'fields' => array(
							'system' => array(
								'type' => 'radio',
								'label' => __( 'Layout System', 'so-widgets-bundle' ),
								'default' => 'grid',
								'options' => array(
									'grid' => __( 'Grid', 'so-widgets-bundle' ),
									'flex' => __( 'Flex', 'so-widgets-bundle' ),
								),
								'state_emitter' => array(
									'callback' => 'select',
									'args' => array( 'system_mobile' )
								),
							),
							'alignment_flex' => array(
								'type' => 'select',
								'label' => __( 'Button Alignment', 'so-widgets-bundle' ),
								'default' => 'space-between',
								'state_handler' => array(
									'system_mobile[flex]' => array( 'show' ),
									'_else[system_mobile]' => array( 'hide' ),
								),
								'options' => array(
									'space-between' => __( 'Space Between Items', 'so-widgets-bundle' ),
									'space-evenly' => __( 'Items Spaced Evenly', 'so-widgets-bundle' ),
									'left' => __( 'Left', 'so-widgets-bundle' ),
									'center' => __( 'Center', 'so-widgets-bundle' ),
									'end' => __( 'Right', 'so-widgets-bundle' ),
								),
							),
							'columns' => array(
								'type' => 'number',
								'label' => __( 'Buttons Per Line', 'so-widgets-bundle' ),
								'default' => 3,
								'state_handler' => array(
									'system_mobile[grid]' => array( 'show' ),
									'_else[system_mobile]' => array( 'hide' ),
								),
							),
							'alignment_grid' => array(
								'type' => 'select',
								'label' => __( 'Button Alignment', 'so-widgets-bundle' ),
								'default' => 'center',
								'state_handler' => array(
									'system_mobile[grid]' => array( 'show' ),
									'_else[system_mobile]' => array( 'hide' ),
								),
								'options' => array(
									'left' => __( 'Left', 'so-widgets-bundle' ),
									'center' => __( 'Center', 'so-widgets-bundle' ),
									'right' => __( 'Right', 'so-widgets-bundle' ),
								),
							),
							'gap' => array(
								'type' => 'multi-measurement',
								'label' => __( 'Gap', 'so-widgets-bundle' ),
								'default' => '20px 20px',
								'measurements' => array(
									'row' => __( 'Row', 'so-widgets-bundle' ),
									'column' => __( 'Column', 'so-widgets-bundle' ),
								),
							),

						),
					),
				),
			),
		);
	}

	/**
	 * Adds a dedicated Grid option to the Button Widget. This allow users to override the Button Grid Alignment on a button by button basis.
	 *
	 * @param array $form_fields
	 *
	 * @return array $form_fields A modified Button Widget form options.
	 */
	function filter_buttons_widget_form( $form_fields ) {
		$form_fields['design']['fields']['align']['options']['grid'] = __( 'Grid', 'so-widgets-bundle' );
		$form_fields['design']['fields']['align']['default'] = 'grid';
		$form_fields['design']['fields']['align']['description'] = __( 'Grid align result in the Grid widget controlling the alignment.', 'so-widgets-bundle' );
		$form_fields['design']['fields']['mobile_align']['options']['grid'] = __( 'Grid', 'so-widgets-bundle' );
		$form_fields['design']['fields']['mobile_align']['default'] = 'grid';
		$form_fields['design']['fields']['mobile_align']['description'] = __( 'Grid align result in the Grid widget controlling the alignment.', 'so-widgets-bundle' );

		return $form_fields;
	}

	public function get_less_variables( $instance ) {
		if ( empty( $instance ) ) {
			return array();
		}

		$settings = array(
			'responsive_breakpoint' => $this->get_global_settings( 'responsive_breakpoint' ),
			'desktop_gap' => ! empty( $instance['layout']['desktop']['gap'] ) ? $instance['layout']['desktop']['gap'] : '20px',
			'mobile_gap' => ! empty( $instance['layout']['mobile']['gap'] ) ? $instance['layout']['mobile']['gap'] : '20px',
		);

		$settings = $this->generate_system_css(
			$settings,
			$instance['layout']['desktop'],
			'desktop'
		);

		$settings = $this->generate_system_css(
			$settings,
			$instance['layout']['mobile'],
			'mobile'
		);

		// Store $settings so we can access it in the override_button_less_variables method.
		$this->settings = $settings;

		return $settings;
	}

	private function generate_system_css( $settings, $context_settings, $context ) {
		if ( $context_settings['system'] === 'grid' ) {
			$settings[ $context . '_system' ] = 'grid';
			$settings[ $context . '_columns' ] = ! empty( $context_settings['columns'] ) ? (int) $context_settings['columns'] : 3;
			$settings[ $context . '_alignment' ] = ! empty( $context_settings['alignment_grid'] ) ? $context_settings['alignment_grid'] : 'center';
		} else {
			$settings[ $context . '_system' ] = 'flex';
			$settings[ $context . '_alignment' ] = ! empty( $context_settings['alignment_flex'] ) ? $context_settings['alignment_flex'] : 'space-between';
		}

		return $settings;
	}

	public function override_button_less_variables( $vars, $instance, $widget ) {
		if (
			$this->settings['desktop_system'] ==='grid' &&
			$instance['desktop']['align'] === 'grid'
		) {
			$vars['align'] = $this->settings['desktop_alignment'];
		}

		if (
			$this->settings['mobile_system'] ==='grid' &&
			$instance['mobile']['align'] === 'grid'
		) {
			$vars['mobile_align'] = $this->settings['mobile_alignment'];
		}

		return $vars;
	}

	// The Button Widget outputs the desktop alignment as a class so we need to override it.
	public function override_button_variables( $vars, $instance ) {
		$vars['align'] = $this->settings['desktop_alignment'];
		return $vars;
	}
}
siteorigin_widget_register( 'sow-button-grid', __FILE__, 'SiteOrigin_Widget_Button_Grid_Widget' );

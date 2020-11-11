<?php /** @noinspection PhpUndefinedClassInspection */
/**
 * Masker masks your images by creative & extra-ordinary custom shapes.
 * Exclusively on Envato Market: https://1.envato.market/maskerelementor
 *
 * @encoding     UTF-8
 * @version      1.0.0
 * @copyright    Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license      Envato License https://1.envato.market/KYbje
 * @author       {{code_author}}
 * @support      help@merkulov.design
 **/

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;
use Merkulove\MaskerElementor;

/** @noinspection PhpUnused */
/**
 * Masker - Custom Elementor Widget.
 *
 * @since 1.0.0
 *
 * @method start_controls_section( string $string, array $array )
 **/
final class masker_elementor extends Widget_Base {

    /**
     * Widget base constructor.
     *
     * Initializing the widget base class.
     *
     * @since 1.0.0
     * @access public
     *
     * @throws Exception If arguments are missing when initializing a full widget instance.
     *
     * @param array      $data Widget data. Default is an empty array.
     * @param array|null $args Optional. Widget default arguments. Default is null.
     **/
    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );

        wp_register_style( 'mdp-masker-css', MaskerElementor::$url . 'css/masker' . MaskerElementor::$suffix . '.css', [], MaskerElementor::$version );
        wp_register_style( 'mdp-masker-elementor-widget-css', MaskerElementor::$url . 'css/widget' . MaskerElementor::$suffix . '.css', [], MaskerElementor::$version );
    }

    /**
     * Return a widget name.
     *
     * @return string
     * @since 1.0.0
     **/
    public function get_name() {
        return 'mdp-masker-elementor';
    }

    /**
     * Return the widget title that will be displayed as the widget label.
     *
     * @return string
     * @since 1.0.0
     **/
    public function get_title() {
        return esc_html__( 'Masker', 'masker-elementor' );
    }

    /**
     * Set the widget icon.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_icon() {
        return 'mdp-masker-elementor-widget-icon';
    }

    /**
     * Set the category of the widget.
     *
     * @since 1.0.0
     *
     * @return array with category names
     **/
    public function get_categories() {
        return ['general'];
    }

    /**
     * Get widget keywords. Retrieve the list of keywords the widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget keywords.
     **/
    public function get_keywords() {
        return [ 'Merkulove', 'Masker', 'Mask', 'Clipping', 'Image', 'Clip', 'Crop'];
    }

    /**
     * Get style dependencies.
     *
     * Retrieve the list of style dependencies the widget requires.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget styles dependencies.
     **/
    public function get_style_depends() {
        return ['mdp-masker-css', 'mdp-masker-elementor-widget-css'];
    }

	/**
	 * Content for Image
	 */
    private function image_content() {

	    /** Start section */
	    $this->start_controls_section(
		    'image_content',
		    [
			    'label' => esc_html__( 'Image', 'masker-elementor' ),
			    'tab' => Controls_Manager::TAB_CONTENT,
		    ]
	    );

	    /** Image Source */
	    $this->add_control(
		    'image_source',
		    [
			    'label' => esc_html__( 'Source', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'default' => 'media',
			    'options' => [
				    'media' => esc_html__( 'Media library', 'masker-elementor' ),
				    'url'  => esc_html__( 'URL', 'masker-elementor' ),
			    ],
		    ]
	    );

	    /** Maintain aspect ratio */
	    $this->add_control(
		    'ratio',
		    [
			    'label' => esc_html__( 'Maintain aspect ratio', 'masker-elementor' ),
			    'type' => Controls_Manager::SWITCHER,
			    'label_on' => esc_html__( 'On', 'masker-elementor' ),
			    'label_off' => esc_html__( 'Off', 'masker-elementor' ),
			    'return_value' => 'yes',
			    'default' => ''
		    ]
	    );

	    /** Image URL */
	    $this->add_control(
		    'image_url',
		    [
			    'label' => __( 'Image URL', 'masker-elementor' ),
			    'type' => Controls_Manager::TEXTAREA,
			    'dynamic' => ['active' => true],
			    'placeholder' => __( 'Paste your image link here', 'masker-elementor' ),
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'background-image: url( {{value}} );',
			    ],
                'condition' => [ 'image_source' => 'url' ],
                'separator' => 'before'
		    ]
	    );

	    /** Image Media */
	    $this->add_control(
		    'image_media',
		    [
			    'label' => __( 'Choose Image', 'masker-elementor' ),
			    'type' => Controls_Manager::MEDIA,
			    'dynamic' => ['active' => true],
			    'default' => [
				    'url' => Utils::get_placeholder_image_src(),
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'background-image: url( {{url}} );',
                ],
			    'condition' => [ 'image_source' => 'media' ],
                'separator' => 'before'
		    ]
	    );

	    /** Image Position */
	    $this->add_responsive_control(
		    'image_position',
		    [
			    'label' => esc_html__( 'Image Position', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'default' => 'initial',
			    'options' => [
				    'initial'       => esc_html__( 'Default', 'masker-elementor' ),
				    'center center' => esc_html__( 'Center Center', 'masker-elementor' ),
				    'center left'   => esc_html__( 'Center Left', 'masker-elementor' ),
				    'center right'  => esc_html__( 'Center Right', 'masker-elementor' ),
				    'top center'    => esc_html__( 'Top Center', 'masker-elementor' ),
				    'top left'      => esc_html__( 'Top Left', 'masker-elementor' ),
				    'top right'     => esc_html__( 'Top Right', 'masker-elementor' ),
				    'bottom center' => esc_html__( 'Bottom Center', 'masker-elementor' ),
				    'bottom left'   => esc_html__( 'Bottom Left', 'masker-elementor' ),
				    'bottom right'  => esc_html__( 'Bottom Right', 'masker-elementor' ),
				    'unset'         => esc_html__( 'Custom', 'masker-elementor' ),
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'background-position: {{value}};',
			    ],
			    'condition' => [ 'ratio' => '' ],
		    ]
	    );

	    /** Mask Custom Position X */
	    $this->add_responsive_control(
		    'image_position_custom_x',
		    [
			    'label' => esc_html__( 'Horizontal Offset', 'masker-elementor' ),
			    'type'  => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%', 'em', 'vh' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 1000,
					    'step' => 1,
				    ]
			    ],
			    'default' => [
				    'unit' => '%',
				    'size' => 50,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-mage' => 'background-position: {{SIZE}}{{UNIT}} {{image_position_custom_y.size}}{{image_position_custom_y.unit}};',
			    ],
			    'condition' => [ 'image_position' => 'unset', 'ratio' => '' ]
		    ]
	    );

	    /** Mask Custom Position Y */
	    $this->add_responsive_control(
		    'image_position_custom_y',
		    [
			    'label' => esc_html__( 'Vertical Offset', 'masker-elementor' ),
			    'type'  => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%', 'em', 'vh' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 1000,
					    'step' => 1,
				    ]
			    ],
			    'default' => [
				    'unit' => '%',
				    'size' => 50,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'background-position: {{image_position_custom_x.size}}{{image_position_custom_x.unit}} {{SIZE}}{{UNIT}};',
			    ],
			    'condition' => [ 'image_position' => 'unset', 'ratio' => '' ]
		    ]
	    );

	    /** Image Attachment */
	    $this->add_responsive_control(
		    'image_attachment',
		    [
			    'label' => esc_html__( 'Image Attachment', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'default' => 'initial',
			    'options' => [
                    'initial'   => esc_html__( 'Default', 'masker-elementor' ),
				    'scroll'    => esc_html__( 'Scroll', 'masker-elementor' ),
				    'fixed'     => esc_html__( 'Fixed', 'masker-elementor' ),
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'background-attachment: {{value}};',
			    ],
			    'condition' => [ 'ratio' => '' ]
		    ]
	    );

	    /** Image Repeat */
	    $this->add_responsive_control(
		    'image_repeat',
		    [
			    'label' => esc_html__( 'Image Repeat', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'default' => 'initial',
			    'options' => [
				    'initial'    => esc_html__( 'Default', 'masker-elementor' ),
				    'no-repeat'    => esc_html__( 'No-repeat', 'masker-elementor' ),
				    'repeat'    => esc_html__( 'Repeat', 'masker-elementor' ),
				    'repeat-x'    => esc_html__( 'Repeat-x', 'masker-elementor' ),
				    'repeat-y'    => esc_html__( 'Repeat-y', 'masker-elementor' ),
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'background-repeat: {{value}};',
			    ],
			    'condition' => [ 'ratio' => '' ]
		    ]
	    );

	    /** Image Size */
	    $this->add_responsive_control(
		    'image_size',
		    [
			    'label' => esc_html__( 'Image Size', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'default' => 'initial',
			    'options' => [
				    'initial'    => esc_html__( 'Default', 'masker-elementor' ),
				    'cover'    => esc_html__( 'Cover', 'masker-elementor' ),
				    'contain'    => esc_html__( 'Contain', 'masker-elementor' ),
				    'unset'    => esc_html__( 'Custom', 'masker-elementor' ),
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'background-size: {{value}};',
			    ],
			    'condition' => [ 'ratio' => '' ]
		    ]
	    );

	    /** Image Custom Size */
	    $this->add_responsive_control(
		    'image_size_custom',
		    [
			    'label' => esc_html__( 'Custom Size', 'masker-elementor' ),
			    'type'  => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%', 'em', 'vh' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 1000,
					    'step' => 1,
				    ]
			    ],
			    'default' => [
				    'unit' => '%',
				    'size' => 100,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'background-size: {{SIZE}}{{UNIT}};',
			    ],
			    'condition' => [ 'image_size' => 'unset', 'ratio' => '' ]
		    ]
	    );

	    /** End section */
	    $this->end_controls_section();

    }

	/**
	 * Content for Mask
	 */
    private function mask_content() {

	    /** Start section properties */
	    $this->start_controls_section(
		    'mask_content',
		    [
			    'label' => esc_html__( 'Clipping Mask', 'masker-elementor' ),
			    'tab' => Controls_Manager::TAB_CONTENT,
		    ]
	    );

	    /** Mask image */
	    $this->add_control(
		    'mask',
		    [
			    'label' => esc_html__( 'Mask', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'default' => 'bubbles-1',
			    'options' => [
				    'bubbles-1'         => esc_html__( 'Bubbles-1', 'masker-elementor' ),
				    'bubbles-2'         => esc_html__( 'Bubbles-2', 'masker-elementor' ),
				    'bubbles-3'         => esc_html__( 'Bubbles-3', 'masker-elementor' ),
				    'bubbles-4'         => esc_html__( 'Bubbles-4', 'masker-elementor' ),
				    'liquid-1'          => esc_html__( 'Liquid-1', 'masker-elementor' ),
				    'liquid-2'          => esc_html__( 'Liquid-2', 'masker-elementor' ),
				    'liquid-3'          => esc_html__( 'Liquid-3', 'masker-elementor' ),
				    'liquid-4'          => esc_html__( 'Liquid-4', 'masker-elementor' ),
				    'balloon-1'         => esc_html__( 'Balloon-1', 'masker-elementor' ),
				    'balloon-2'         => esc_html__( 'Balloon-2', 'masker-elementor' ),
				    'balloon-3'         => esc_html__( 'Balloon-3', 'masker-elementor' ),
				    'balloon-4'         => esc_html__( 'Balloon-4', 'masker-elementor' ),
				    'balloon-5'         => esc_html__( 'Balloon-5', 'masker-elementor' ),
				    'balloon-6'         => esc_html__( 'Balloon-6', 'masker-elementor' ),
				    'balloon-7'         => esc_html__( 'Balloon-7', 'masker-elementor' ),
				    'watercolor-1'      => esc_html__( 'Watercolor-1', 'masker-elementor' ),
				    'watercolor-2'      => esc_html__( 'Watercolor-2', 'masker-elementor' ),
				    'watercolor-3'      => esc_html__( 'Watercolor-3', 'masker-elementor' ),
				    'watercolor-4'      => esc_html__( 'Watercolor-4', 'masker-elementor' ),
				    'triangles-1'       => esc_html__( 'Triangles-1', 'masker-elementor' ),
				    'triangles-2'       => esc_html__( 'Triangles-2', 'masker-elementor' ),
				    'triangles-3'       => esc_html__( 'Triangles-3', 'masker-elementor' ),
				    'brush-1'           => esc_html__( 'Brush-1', 'masker-elementor' ),
				    'brush-2'           => esc_html__( 'Brush-2', 'masker-elementor' ),
				    'brush-3'           => esc_html__( 'Brush-3', 'masker-elementor' ),
				    'brush-4'           => esc_html__( 'Brush-4', 'masker-elementor' ),
				    'hexagon-1'         => esc_html__( 'Hexagon-1', 'masker-elementor' ),
				    'arabic-square'     => esc_html__( 'Arabic-square', 'masker-elementor' ),
				    'arabic-rhombus'    => esc_html__( 'Arabic-rhombus', 'masker-elementor' ),
				    'arabic-ellipse'    => esc_html__( 'Arabic-ellipse', 'masker-elementor' ),
				    'seal'              => esc_html__( 'Seal', 'masker-elementor' ),
				    'custom'            => esc_html__( 'Custom', 'masker-elementor' ),
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-mask' => 'mask-image: url( "' . MaskerElementor::$url . 'images/{{value}}.svg" ); -webkit-mask-image: url( "' . MaskerElementor::$url . 'images/{{value}}.svg" );',
			    ]
		    ]
	    );

	    /** Custom mask */
	    $this->add_control(
		    'mask_custom',
		    [
			    'label' => __( 'Choose Mask', 'masker-elementor' ),
			    'description' => __( 'SVG Image Only', 'masker-elementor' ),
			    'type' => Controls_Manager::MEDIA,
			    'dynamic' => ['active' => true],
			    'default' => [
				    'url' => Utils::get_placeholder_image_src(),
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-mask' => 'mask-image: url( {{url}} ); -webkit-mask-image: url( {{url}} );',
			    ],
			    'condition' => [ 'mask' => 'custom' ],
		    ]
	    );

	    /** Mask Position */
	    $this->add_responsive_control(
		    'mask_position',
		    [
			    'label' => esc_html__( 'Mask Position', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'default' => 'unset',
			    'options' => [
				    'unset'         => esc_html__( 'Default', 'masker-elementor' ),
				    'center center' => esc_html__( 'Center Center', 'masker-elementor' ),
				    'center left'   => esc_html__( 'Center Left', 'masker-elementor' ),
				    'center right'  => esc_html__( 'Center Right', 'masker-elementor' ),
				    'top center'    => esc_html__( 'Top Center', 'masker-elementor' ),
				    'top left'      => esc_html__( 'Top Left', 'masker-elementor' ),
				    'top right'     => esc_html__( 'Top Right', 'masker-elementor' ),
				    'bottom center' => esc_html__( 'Bottom Center', 'masker-elementor' ),
				    'bottom left'   => esc_html__( 'Bottom Left', 'masker-elementor' ),
				    'bottom right'  => esc_html__( 'Bottom Right', 'masker-elementor' ),
				    'custom'        => esc_html__( 'Custom', 'masker-elementor' ),
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-mask' => 'mask-position: {{value}}; -webkit-mask-position: {{value}};',
			    ]
		    ]
	    );

	    /** Mask Custom Position X */
	    $this->add_responsive_control(
		    'mask_position_custom_x',
		    [
			    'label' => esc_html__( 'Horizontal Offset', 'masker-elementor' ),
			    'type'  => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%', 'em', 'vh' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 1000,
					    'step' => 1,
				    ]
			    ],
			    'default' => [
				    'unit' => '%',
				    'size' => 50,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-mask' => 'mask-position: {{SIZE}}{{UNIT}} {{mask_position_custom_y.size}}{{mask_position_custom_y.unit}}; -webkit-mask-position: {{SIZE}}{{UNIT}} {{mask_position_custom_y.size}}{{mask_position_custom_y.unit}};',
			    ],
			    'condition' => [ 'mask_position' => 'custom' ]
		    ]
	    );

	    /** Mask Custom Position Y */
	    $this->add_responsive_control(
		    'mask_position_custom_y',
		    [
			    'label' => esc_html__( 'Vertical Offset', 'masker-elementor' ),
			    'type'  => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%', 'em', 'vh' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 1000,
					    'step' => 1,
				    ]
			    ],
			    'default' => [
				    'unit' => '%',
				    'size' => 50,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-mask' => 'mask-position: {{mask_position_custom_x.size}}{{mask_position_custom_x.unit}} {{SIZE}}{{UNIT}}; -webkit-mask-position: {{mask_position_custom_x.size}}{{mask_position_custom_x.unit}} {{SIZE}}{{UNIT}};',
			    ],
			    'condition' => [ 'mask_position' => 'custom' ]
		    ]
	    );

	    /** Mask Repeat */
	    $this->add_responsive_control(
		    'mask_repeat',
		    [
			    'label' => esc_html__( 'Mask Repeat', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'default' => 'no-repeat',
			    'options' => [
				    'no-repeat' => esc_html__( 'No-repeat', 'masker-elementor' ),
				    'repeat'    => esc_html__( 'Repeat', 'masker-elementor' ),
				    'repeat-x'  => esc_html__( 'Repeat-x', 'masker-elementor' ),
				    'repeat-y'  => esc_html__( 'Repeat-y', 'masker-elementor' ),
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-mask' => 'mask-repeat: {{value}}; -webkit-mask-repeat: {{value}};',
			    ]
		    ]
	    );

	    /** Mask Size */
	    $this->add_responsive_control(
		    'mask_size',
		    [
			    'label' => esc_html__( 'Mask Size', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'default' => 'unset',
			    'options' => [
				    'unset'     => esc_html__( 'Default', 'masker-elementor' ),
				    'cover'     => esc_html__( 'Cover', 'masker-elementor' ),
				    'contain'   => esc_html__( 'Contain', 'masker-elementor' ),
				    'custom'    => esc_html__( 'Custom', 'masker-elementor' ),
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-mask' => 'mask-size: {{value}}; -webkit-mask-size: {{value}};',
			    ]
		    ]
	    );

	    /** Mask Custom Size */
	    $this->add_responsive_control(
		    'mask_size_custom',
		    [
			    'label' => esc_html__( 'Custom Size', 'masker-elementor' ),
			    'type'  => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%', 'em', 'vh' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 1000,
					    'step' => 1,
				    ]
			    ],
			    'default' => [
				    'unit' => '%',
				    'size' => 100,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-mask' => 'mask-size: {{SIZE}}{{UNIT}}; -webkit-mask-size: {{SIZE}}{{UNIT}};',
			    ],
                'condition' => [ 'mask_size' => 'custom' ]
		    ]
	    );

	    /** End section */
	    $this->end_controls_section();

    }

	/**
	 * Content for Description
	 */
    private function caption_content() {

	    /** Start section properties */
	    $this->start_controls_section(
		    'description_content',
		    [
			    'label' => esc_html__( 'Caption', 'masker-elementor' ),
			    'tab' => Controls_Manager::TAB_CONTENT,
		    ]
	    );

	    $this->add_control(
		    'header_heading',
		    [
			    'label' => __( 'Header', 'masker-elementor' ),
			    'type' => Controls_Manager::HEADING,
			    'condition' => [ 'show_header' => 'yes' ]
		    ]
	    );

	    /** Header. */
	    $this->add_control(
		    'header',
		    [
			    'label' => esc_html__( 'Header', 'masker-elementor' ),
			    'show_label' => false,
			    'type' => Controls_Manager::TEXTAREA,
			    'dynamic' => ['active' => true],
			    'rows' => 1,
			    'default' => esc_html__( 'Header', 'masker-elementor' ),
			    'placeholder' => esc_html__( 'Header', 'masker-elementor' ),
                'condition' => [ 'show_header' => 'yes' ]
		    ]
	    );

	    /** Header Position. */
	    $this->add_responsive_control(
		    'header_position',
		    [
			    'label' => esc_html__( 'Header Position', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'options' => [
				    'top' => 'Top',
				    'over' => 'Over',
				    'bottom' => 'Bottom',
			    ],
			    'default' => 'top',
			    'condition'   => [ 'show_header' => 'yes' ]
		    ]
	    );

	    /** HTML Tag. */
	    $this->add_control(
		    'masker_tag',
		    [
			    'label' => esc_html__( 'HTML Tag', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'options' => [
				    'h1' => 'H1',
				    'h2' => 'H2',
				    'h3' => 'H3',
				    'h4' => 'H4',
				    'h5' => 'H5',
				    'h6' => 'H6',
				    'div' => 'div',
				    'span' => 'span',
				    'p' => 'p',
			    ],
			    'default' => 'h2',
			    'condition' => [ 'show_header' => 'yes' ]
		    ]
	    );

	    /** Show header. */
	    $this->add_control(
		    'show_header',
		    [
			    'label' => esc_html__( 'Header', 'masker-elementor' ),
			    'type' => Controls_Manager::SWITCHER,
			    'label_on' => esc_html__( 'Show', 'masker-elementor' ),
			    'label_off' => esc_html__( 'Hide', 'masker-elementor' ),
			    'return_value' => 'yes',
			    'default' => '',
		    ]
	    );

	    $this->add_control(
		    'subheader_heading',
		    [
			    'label' => __( 'Subheader', 'masker-elementor' ),
			    'type' => Controls_Manager::HEADING,
			    'condition' => [ 'show_subheader' => 'yes', 'show_header' => 'yes' ],
			    'separator' => 'before',
		    ]
	    );

	    /** Subheader. */
	    $this->add_control(
		    'masker_sub_name',
		    [
			    'label' => esc_html__( 'Subheader', 'masker-elementor' ),
			    'show_label' => false,
			    'type' => Controls_Manager::TEXTAREA,
			    'dynamic' => ['active' => true],
			    'rows' => 1,
			    'default' => esc_html__( 'Subheader', 'masker-elementor' ),
			    'placeholder' => esc_html__( 'Subheader', 'masker-elementor' ),
			    'condition'   => [ 'show_subheader' => 'yes', 'show_header' => 'yes' ],
		    ]
	    );

	    /** Subheader Position. */
	    $this->add_control(
		    'subheader_position',
		    [
			    'label' => esc_html__( 'Subheader Position', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'options' => [
				    'top' => 'Above Header',
				    'bottom' => 'Under Header',
			    ],
			    'default' => 'bottom',
			    'condition'   => [ 'show_subheader' => 'yes', 'show_header' => 'yes' ]
		    ]
	    );

	    /** Show Subheader. */
	    $this->add_control(
		    'show_subheader',
		    [
			    'label' => esc_html__( 'Subheader', 'masker-elementor' ),
			    'type' => Controls_Manager::SWITCHER,
			    'label_on' => esc_html__( 'Show', 'masker-elementor' ),
			    'label_off' => esc_html__( 'Hide', 'masker-elementor' ),
			    'return_value' => 'yes',
			    'condition' => [ 'show_header' => 'yes' ],
		    ]
	    );

	    $this->add_control(
		    'description_heading',
		    [
			    'label' => __( 'Description', 'masker-elementor' ),
			    'type' => Controls_Manager::HEADING,
			    'condition' => [ 'show_description' => 'yes' ],
			    'separator' => 'before',
		    ]
	    );

	    /** Description. */
	    $this->add_control(
		    'description',
		    [
			    'label' => esc_html__( 'Description', 'masker-elementor' ),
			    'show_label' => false,
			    'type' => Controls_Manager::TEXTAREA,
			    'dynamic' => ['active' => true],
			    'rows' => 5,
			    'default' => esc_html__( 'Description', 'masker-elementor' ),
			    'placeholder' => esc_html__( 'Description', 'masker-elementor' ),
			    'condition' => [ 'show_description' => 'yes' ],
		    ]
	    );

	    /** Description Position. */
	    $this->add_responsive_control(
		    'description_position',
		    [
			    'label' => esc_html__( 'Description Position', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'options' => [
				    'header'    => esc_html__( 'Top', 'masker-elementor' ),
				    'over'      => esc_html__( 'Over', 'masker-elementor' ),
				    'footer'    => esc_html__( 'Bottom','masker-elementor' )
			    ],
			    'default' => 'footer',
			    'condition' => [ 'show_description' => 'yes' ],
		    ]
	    );

	    /** HTML Tag. */
	    $this->add_control(
		    'description_tag',
		    [
			    'label' => esc_html__( 'HTML Tag', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'options' => [
				    'div' => 'div',
				    'span' => 'span',
				    'p' => 'p',
			    ],
			    'default' => 'p',
			    'condition' => [ 'show_description' => 'yes' ],
		    ]
	    );

	    /** Show description. */
	    $this->add_control(
		    'show_description',
		    [
			    'label' => esc_html__( 'Description', 'masker-elementor' ),
			    'type' => Controls_Manager::SWITCHER,
			    'label_on' => esc_html__( 'Show', 'masker-elementor' ),
			    'label_off' => esc_html__( 'Hide', 'masker-elementor' ),
			    'return_value' => 'yes',
			    'default' => '',
		    ]
	    );

	    $this->add_control(
		    'link_heading',
		    [
			    'label' => __( 'Link', 'masker-elementor' ),
			    'type' => Controls_Manager::HEADING,
			    'separator' => 'before',
			    'condition' => [ 'show_link' => 'yes' ]
		    ]
	    );

	    /** Link Position. */
	    $this->add_control(
		    'link_position',
		    [
			    'label' => esc_html__( 'Link Position', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'options' => [
				    'image' => esc_html__( 'Image', 'masker-elementor' ),
				    'box'   => esc_html__( 'Box', 'masker-elementor' ),
			    ],
			    'default' => 'image',
			    'condition' => [ 'show_link' => 'yes' ]
		    ]
	    );

	    /** URL link. */
	    $this->add_control(
		    'link_url',
		    [
			    'label' => esc_html__( 'URL', 'masker-elementor' ),
			    'type' => Controls_Manager::URL,
			    'placeholder' => esc_html__( 'https://codecanyon.net/user/merkulove', 'masker-elementor' ),
                'condition' => [ 'show_link' => 'yes' ]
		    ]
	    );

	    /** Enable link. */
	    $this->add_control(
		    'show_link',
		    [
			    'label' => esc_html__( 'Enable link', 'masker-elementor' ),
			    'type' => Controls_Manager::SWITCHER,
			    'label_on' => esc_html__( 'Show', 'masker-elementor' ),
			    'label_off' => esc_html__( 'Hide', 'masker-elementor' ),
			    'return_value' => 'yes',
		    ]
	    );

	    /** Justify Content. */
	    $this->add_responsive_control(
		    'description_justify',
		    [
			    'label' => esc_html__( 'Justify Content', 'masker-elementor' ),
			    'description' => esc_html__( 'Only for "Over" position', 'masker-elementor' ),
			    'type' => Controls_Manager::SELECT,
			    'options' => [
				    'flex-start' => 'Start',
				    'center' => 'Center',
				    'flex-end' => 'End',
				    'space-around' => 'Space Around',
				    'space-between' => 'Space Between',
				    'space-evenly' => 'Space Evenly',
			    ],
			    'default' => 'flex-start',
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-over' => 'justify-content: {{value}}',
			    ],
			    'conditions' => [
				    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'header_position',
                            'operator' => '==',
                            'value' => 'over'
                        ],
                        [
                            'name' => 'description_position',
                            'operator' => '==',
                            'value' => 'over'
                        ]
			        ]
                ],
                'separator' => 'before'
		    ]
	    );

	    /** End section */
	    $this->end_controls_section();

    }

	/**
	 * Style for Image
	 */
    private function image_style() {

	    $this->start_controls_section(
		    'image_style',
		    [
			    'label' => esc_html__( 'Image', 'masker-elementor' ),
			    'tab' => Controls_Manager::TAB_STYLE,
		    ]
        );

	    /** Margin. */
	    $this->add_responsive_control(
		    'image_margin',
		    [
			    'label' => esc_html__( 'Margin', 'masker-elementor' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'margin: {{top}}{{unit}} {{right}}{{unit}} {{bottom}}{{unit}} {{left}}{{unit}};',
			    ],
			    'toggle' => true,
		    ]
	    );

	    /** Padding. */
	    $this->add_responsive_control(
		    'image_padding',
		    [
			    'label' => esc_html__( 'Padding', 'masker-elementor' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'padding: {{top}}{{unit}} {{right}}{{unit}} {{bottom}}{{unit}} {{left}}{{unit}};',
			    ],
			    'toggle' => true,
			    'separator' => 'after'
		    ]
	    );

	    /** Width */
	    $this->add_responsive_control(
		    'image_width',
		    [
			    'label' => esc_html__( 'Width', 'masker-elementor' ),
			    'type'  => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%', 'vw' ],
			    'default' => [
				    'unit' => '%',
				    'size' => 100,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'width: {{SIZE}}{{UNIT}};',
			    ],
		    ]
	    );

	    /** Height */
	    $this->add_responsive_control(
		    'image_height',
		    [
			    'label' => esc_html__( 'Height', 'masker-elementor' ),
			    'type'  => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%', 'vh' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 1000,
					    'step' => 1,
				    ]
			    ],
			    'default' => [
				    'unit' => 'px',
				    'size' => 500,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'height: {{SIZE}}{{UNIT}};',
			    ],
			    'separator' => 'after'
		    ]
	    );

	    /** Image Background */
	    /** @noinspection PhpUndefinedClassInspection */
	    $this->add_control(
		    'image_background',
		    [
			    'label' => esc_html__( 'Background color', 'masker-elementor' ),
			    'type' => Controls_Manager::COLOR,
			    'scheme' => [
				    'type' => Scheme_Color::get_type(),
				    'value' => Scheme_Color::COLOR_1,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-image' => 'background-color: {{VALUE}}',
			    ],
		    ]
	    );

	    /** End style. */
	    $this->end_controls_section();

    }

	/**
	 * Style for Mask
	 */
	private function mask_style() {

		/** Mask style section. */
		$this->start_controls_section(
			'style_content',
			[
				'label' => esc_html__( 'Clipping Mask', 'masker-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			] );

		/** Margin. */
		$this->add_responsive_control(
			'mask_margin',
			[
				'label' => esc_html__( 'Margin', 'masker-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .mdp-masker-mask' => 'margin: {{top}}{{unit}} {{right}}{{unit}} {{bottom}}{{unit}} {{left}}{{unit}};',
				],
				'toggle' => true,
			]
		);

		/** Padding. */
		$this->add_responsive_control(
			'mask_padding',
			[
				'label' => esc_html__( 'Padding', 'masker-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .mdp-masker-mask' => 'padding: {{top}}{{unit}} {{right}}{{unit}} {{bottom}}{{unit}} {{left}}{{unit}};',
				],
				'toggle' => true,
			]
		);

		/** Rotation. */
		$this->add_responsive_control(
			'mask_rotate',
			[
				'label' => esc_html__( 'Rotation', 'masker-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
                    'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .mdp-masker-mask' => 'transform: rotate( {{SIZE}}{{UNIT}} );',
					'{{WRAPPER}} .mdp-masker-image' => 'transform: rotate( calc( -1 * {{SIZE}}{{UNIT}} ) );',
				]
			]
		);

		/** End animation content style. */
		$this->end_controls_section();

	}

	/** Style for Header */
	private function header_style() {

		/** Header style. */
		$this->start_controls_section( 'style_header',
			[
				'label' => esc_html__( 'Header', 'masker-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_header' => 'yes' ],
			]
        );

		/** Margin. */
		$this->add_responsive_control(
			'masker_margin_header',
			[
				'label' => esc_html__( 'Margin', 'masker-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'selectors' => [
					'{{WRAPPER}} .mdp-masker-heading' => 'margin: {{top}}{{unit}} {{right}}{{unit}} {{bottom}}{{unit}} {{left}}{{unit}};',
				],
				'toggle' => true,
			]
		);

		/** Padding. */
		$this->add_responsive_control(
			'masker_padding_header',
			[
				'label' => esc_html__( 'Padding', 'masker-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'selectors' => [
					'{{WRAPPER}} .mdp-masker-heading' => 'padding: {{top}}{{unit}} {{right}}{{unit}} {{bottom}}{{unit}} {{left}}{{unit}};',
				],
				'toggle' => true,
			]
		);

		/** Color. */
		/** @noinspection PhpUndefinedClassInspection */
		$this->add_control(
			'color_header',
			[
				'label' => esc_html__( 'Color', 'masker-elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .mdp-masker-header' => 'color: {{VALUE}}',
				],
			]
		);

		/** Typography. */
		/** @noinspection PhpUndefinedClassInspection */
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_typography',
				'label' => esc_html__( 'Typography', 'masker-elementor' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .mdp-masker-header',
			]
		);

		/** Shadow. */
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'header_shadow',
				'label' => esc_html__( 'Shadow', 'masker-elementor' ),
				'selector' => '{{WRAPPER}} .mdp-masker-header',
			]
		);

		/** Background */
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'header_background',
				'label' => esc_html__( 'Background', 'masker-elementor' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .mdp-masker-heading',
			]
		);

		/** Alignment. */
		$this->add_responsive_control(
			'header_align',
			[
				'label' => esc_html__( 'Alignment', 'masker-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'masker-elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'masker-elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'masker-elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .mdp-masker-heading' => 'text-align: {{header_align}};',
				],
				'toggle' => true,
			]
		);

		/** Subheader. */
		$this->add_control(
			'text_animation_header',
			[
				'label' => esc_html__( 'Subheader', 'masker-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => ['show_subheader' => 'yes']
			]
		);

		/** Margin. */
		$this->add_responsive_control(
			'masker_margin_subheader',
			[
				'label' => esc_html__( 'Margin', 'masker-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'selectors' => [
					'{{WRAPPER}} .mdp-masker-subheader' => 'margin: {{top}}{{unit}} {{right}}{{unit}} {{bottom}}{{unit}} {{left}}{{unit}};',
				],
				'toggle' => true,
				'condition'   => ['show_subheader' => 'yes']
			]
		);

		/** Padding. */
		$this->add_responsive_control(
			'masker_padding_subheader',
			[
				'label' => esc_html__( 'Padding', 'masker-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'selectors' => [
					'{{WRAPPER}} .mdp-masker-subheader' => 'padding: {{top}}{{unit}} {{right}}{{unit}} {{bottom}}{{unit}} {{left}}{{unit}};',
				],
				'toggle' => true,
				'condition'   => ['show_subheader' => 'yes']
			]
		);

		/** Color. */
		/** @noinspection PhpUndefinedClassInspection */
		$this->add_control(
			'color_subheader',
			[
				'label' => esc_html__( 'Color', 'masker-elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .mdp-masker-subheader' => 'color: {{VALUE}}',
				],
				'condition'   => ['show_subheader' => 'yes']
			]
		);

		/** Typography. */
		/** @noinspection PhpUndefinedClassInspection */
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'subheader_typography',
				'label' => esc_html__( 'Typography', 'masker-elementor' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .mdp-masker-subheader',
				'condition'   => ['show_subheader' => 'yes']
			]
		);

		/** Shadow. */
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'subheader_shadow',
				'label' => esc_html__( 'Shadow', 'masker-elementor' ),
				'selector' => '{{WRAPPER}} .mdp-masker-subheader',
				'condition'   => ['show_subheader' => 'yes']
			]
		);

		/** End header style. */
		$this->end_controls_section();

    }

    /** Style for Description */
    private function description_style() {

	    /** Description style. */
	    $this->start_controls_section( 'style_description',
		    [
			    'label' => esc_html__( 'Description', 'masker-elementor' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			    'condition' => [ 'show_description' => 'yes' ],
		    ]
        );

	    /** Margin. */
	    $this->add_responsive_control(
		    'masker_margin_description',
		    [
			    'label' => esc_html__( 'Margin', 'masker-elementor' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'devices' => [ 'desktop', 'tablet', 'mobile' ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-description' => 'margin: {{top}}{{unit}} {{right}}{{unit}} {{bottom}}{{unit}} {{left}}{{unit}};',
			    ],
			    'toggle' => true,
		    ]
	    );

	    /** Padding. */
	    $this->add_responsive_control(
		    'masker_padding_description',
		    [
			    'label' => esc_html__( 'Padding', 'masker-elementor' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'devices' => [ 'desktop', 'tablet', 'mobile' ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-description' => 'padding: {{top}}{{unit}} {{right}}{{unit}} {{bottom}}{{unit}} {{left}}{{unit}};',
			    ],
			    'toggle' => true,
		    ]
	    );

	    /** Color. */
	    /** @noinspection PhpUndefinedClassInspection */
	    $this->add_control(
		    'color_description',
		    [
			    'label' => esc_html__( 'Color', 'masker-elementor' ),
			    'type' => Controls_Manager::COLOR,
			    'scheme' => [
				    'type' => Scheme_Color::get_type(),
				    'value' => Scheme_Color::COLOR_3,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-description' => 'color: {{VALUE}}',
			    ],
		    ]
	    );

	    /** Typography. */
	    /** @noinspection PhpUndefinedClassInspection */
	    $this->add_group_control(
		    Group_Control_Typography::get_type(),
		    [
			    'name' => 'description_typography',
			    'label' => esc_html__( 'Typography', 'masker-elementor' ),
			    'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			    'selector' => '{{WRAPPER}} .mdp-masker-description',
		    ]
	    );

	    /** Shadow. */
	    $this->add_group_control(
		    Group_Control_Text_Shadow::get_type(),
		    [
			    'name' => 'description_shadow',
			    'label' => esc_html__( 'Shadow', 'masker-elementor' ),
			    'selector' => '{{WRAPPER}} .mdp-masker-description',
		    ]
	    );

	    /** Background */
	    $this->add_group_control(
		    Group_Control_Background::get_type(),
		    [
			    'name' => 'description_background',
			    'label' => esc_html__( 'Background', 'masker-elementor' ),
			    'types' => [ 'classic', 'gradient' ],
			    'selector' => '{{WRAPPER}} .mdp-masker-description',
		    ]
	    );

	    /** Alignment. */
	    $this->add_responsive_control(
		    'description_align',
		    [
			    'label' => esc_html__( 'Alignment', 'masker-elementor' ),
			    'type' => Controls_Manager::CHOOSE,
			    'options' => [
				    'left' => [
					    'title' => esc_html__( 'Left', 'masker-elementor' ),
					    'icon' => 'fa fa-align-left',
				    ],
				    'center' => [
					    'title' => esc_html__( 'Center', 'masker-elementor' ),
					    'icon' => 'fa fa-align-center',
				    ],
				    'right' => [
					    'title' => esc_html__( 'Right', 'masker-elementor' ),
					    'icon' => 'fa fa-align-right',
				    ],
				    'justify' => [
					    'title' => esc_html__( 'Justify', 'masker-elementor' ),
					    'icon' => 'fa fa-align-justify',
				    ],
			    ],
			    'default' => 'center',
			    'selectors' => [
				    '{{WRAPPER}} .mdp-masker-description' => 'text-align: {{header_align}};',
			    ],
			    'toggle' => true,
		    ]
	    );

	    /** End description style. */
	    $this->end_controls_section();

    }

    /**
     * Add the widget controls.
     *
     * @since 1.0.0
     * @access protected
     *
     * @return void with category names
     **/
    protected function _register_controls() {

	    /** Content -> Image */
	    $this->image_content();

	    /** Content -> Mask */
	    $this->mask_content();

	    /** Content -> Description */
        $this->caption_content();

	    /** Style -> Image */
	    $this->image_style();

	    /** Style -> Mask */
	    $this->mask_style();

	    /** Style -> Header */
        $this->header_style();

        /** Style -> Description */
        $this->description_style();

    }

    /** Render Header */
    private function header_render() {
	    $settings = $this->get_settings_for_display();
        ?>

	    <<?php echo esc_attr( $settings[ 'masker_tag' ] ) ?> class="mdp-masker-heading">
	    <?php

            /** Display the subheader above the title. */
            if( $settings[ 'show_subheader' ] === 'yes' and $settings[ 'subheader_position' ] === 'top' ) {
                $this->add_inline_editing_attributes( 'masker_sub_name', 'basic' );
                ?><span <?php echo $this->get_render_attribute_string( 'masker_sub_name' ); ?>><?php echo wp_kses_post( $settings[ 'masker_sub_name' ] ); ?></span><?php
            }

            /** Display the header. */
            $this->add_inline_editing_attributes( 'header', 'basic' );
            ?><span <?php echo $this->get_render_attribute_string( 'header' ); ?>><?php echo wp_kses_post( $settings[ 'header' ] ); ?></span><?php


            /** Display the subheader below the title. */
            if($settings[ 'show_subheader' ] === 'yes' and $settings[ 'subheader_position' ] === 'bottom' ) {
                $this->add_inline_editing_attributes( 'masker_sub_name', 'basic' );
                ?><span <?php echo $this->get_render_attribute_string( 'masker_sub_name' ); ?>><?php echo wp_kses_post( $settings[ 'masker_sub_name' ] ); ?></span><?php
            }

        ?>
        </<?php echo esc_attr( $settings['masker_tag'] ) ?>>

        <?php
    }

    /**
     * Render Frontend Output. Generate the final HTML on the frontend.
     *
     * @since 1.0.0
     * @access protected
     **/
    protected function render() {

        /** We get all the values from the admin panel. */
        $settings = $this->get_settings_for_display();
        $idSection = $this->get_id();
	    $target = isset($settings['link_url']['is_external']) ? ' target="_blank"' : '';
	    $nofollow = isset($settings['link_url']['nofollow']) ? ' rel="nofollow"' : '';

        /** Sub Header. */
        $this->add_render_attribute(
            [
                'masker_sub_name' => [
                    'class' => [ 'mdp-masker-subheader' ]
                ],
            ]
        );

        /** Header */
        $this->add_render_attribute(
            [
                'header' => [
                    'class' => [ 'mdp-masker-header' ]
                ],
            ]
        );

        /** Description. */
        $this->add_render_attribute(
            [
                'description' => [
                    'class' => [ 'mdp-masker-description' ]
                ],
            ]
        );

        ?>

        <div id="mdp-masker-<?php echo esc_attr($idSection); ?>" class="mdp-masker">

	        <?php
            /** Header on top */
            if( $settings[ 'show_header' ] === 'yes' and $settings[ 'header_position' ] === 'top' ): $this->header_render(); endif;

            /** Displays a brief description after the title. */
            if ( $settings[ 'description_position' ] === 'header' and $settings[ 'show_description' ] === 'yes' ):
                $this->add_inline_editing_attributes( 'description', 'basic' );
                echo '<' . esc_attr( $settings[ 'description_tag' ] ) . ' ' . $this->get_render_attribute_string( 'description' ) . ' >' . wp_kses_post( $settings[ 'description' ] ) . '</' . esc_attr( $settings[ 'description_tag' ] ) . '>';
            endif;

            /** Display link for the image */
            if( $settings[ 'show_link' ] === 'yes' and $settings[ 'link_position' ] === 'image' ): ?>
                <a href="<?php echo esc_attr($settings['link_url']['url']); ?>" <?php echo esc_attr( $target ) .' '. esc_attr($nofollow);  ?>>
            <?php endif; ?>

            <div class="mdp-masker-mask">
                <?php
                /** Image */
                if ( 'yes' === $settings[ 'ratio' ] ) { ?>
                    <img class="mdp-masker-image" src="<?php echo ( $settings[ 'image_source' ] === 'media' ) ? esc_attr( $settings[ 'image_media' ][ 'url' ] ) : esc_attr( $settings[ 'image_url' ] )  ?>" alt="<?php echo wp_kses_post( $settings[ 'description' ] ) ?>"/>
                <?php } else { ?>
                    <div class="mdp-masker-image"></div>
                <?php } ?>
            </div>

            <?php
            /** Display caption over the image */
            if( $settings[ 'header_position' ] === 'over'  or $settings[ 'description_position' ] === 'over' ): ?>
            <div class="mdp-masker-over">
                <?php
                /** Display Header */
	            if ( $settings[ 'header_position' ] === 'over' and $settings[ 'show_header' ] === 'yes' ):
                $this->header_render();
	            endif;
	            /** Display Description */
                if ( $settings[ 'description_position' ] === 'over' and $settings[ 'show_description' ] === 'yes' ):
	                $this->add_inline_editing_attributes( 'description', 'basic' );
	                echo '<' . esc_attr( $settings[ 'description_tag' ] ) . ' ' . $this->get_render_attribute_string( 'description' ) . ' >' . wp_kses_post( $settings[ 'description' ] ) . '</' . esc_attr( $settings[ 'description_tag' ] ) . '>';
                endif;
                ?>
            </div>
            <?php endif; ?>

            <?php if( $settings['show_link'] === 'yes' and $settings['link_position'] === 'image' ): ?>
                </a>
            <?php endif;

            /** Header on bottom */
            if( $settings[ 'show_header' ] === 'yes' and $settings[ 'header_position' ] === 'bottom' ): $this->header_render(); endif;

            /** We display a short description if you want to display it at the end of the section. */
            if ( $settings[ 'description_position' ] === 'footer' and $settings[ 'show_description' ] === 'yes' ):
                $this->add_inline_editing_attributes( 'description', 'basic' );
                echo '<' . esc_attr( $settings['description_tag'] ) . ' ' . $this->get_render_attribute_string( 'description' ) . '>' . wp_kses_post( $settings['description'] ) . '</' . esc_attr( $settings['description_tag'] ) . '>';
            endif;

            /** Link for the whole box */
            if( $settings[ 'show_link' ] === 'yes' and $settings[ 'link_position' ] === 'box' ): ?>
                <a href="<?php echo esc_attr( $settings[ 'link_url' ][ 'url' ] ); ?>" class="mdp-masker-link" <?php echo esc_attr( $target ) .' '. esc_attr( $nofollow );  ?> ></a>
            <?php endif; ?>

        </div>

    <?php
    }

    /**
     * Return link for documentation.
     *
     * Used to add stuff after widget.
     *
     * @since 1.0.0
     * @access public
     **/
    public function get_custom_help_url() {
        return 'https://docs.merkulov.design/tag/masker/';
    }

}
<?php
/**
 * Gravity Wiz // Calculate Number of Days Between Two Gravity Form Date Fields
 *
 * Allows you to calculated the number of days between two Gravity Form date fields and populate that number into a
 * field on your Gravity Form.
 *
 * @version   1.1
 * @author    David Smith <david@gravitywiz.com>
 * @license   GPL-2.0+
 * @link      http://gravitywiz.com/calculate-number-of-days-between-two-dates/
 * @copyright 2013 Gravity Wiz
 */
class GWDayCount {
	
	private static $script_output;
	
	public $form_id;
	public $start_field_id;
	public $end_field_id;
	public $count_field_id;
	public $count_adjust;
	public $form;
	
	function __construct( $args ) {
		
		extract( wp_parse_args( $args, array(
				'form_id'          => false,
				'start_field_id'   => false,
				'end_field_id'     => false,
				'count_field_id'   => false,
				'include_end_date' => true,
		) ) );
		
		$this->form_id        = $form_id;
		$this->start_field_id = $start_field_id;
		$this->end_field_id   = $end_field_id;
		$this->count_field_id = $count_field_id;
		$this->count_adjust   = $include_end_date ? 1 : 0;
		
		add_filter( "gform_pre_render_{$form_id}", array( &$this, 'load_form_script') );
		add_action( "gform_pre_submission_{$form_id}", array( &$this, 'override_submitted_value') );
		
	}
	
	function load_form_script( $form ) {
		
		// workaround to make this work for < 1.7
		$this->form = $form;
		add_filter( 'gform_init_scripts_footer', array( &$this, 'add_init_script' ) );
		
		return $form;
    }

    function add_init_script( $return ) {

        $start_field_format = false;
        $end_field_format = false;

        foreach( $this->form['fields'] as &$field ) {

            if( $field['id'] == $this->start_field_id )
                $start_field_format = $field['dateFormat'] ? $field['dateFormat'] : 'mdy';

            if( $field['id'] == $this->end_field_id )
                $end_field_format = $field['dateFormat'] ? $field['dateFormat'] : 'mdy';

        }

        $script = "new gwdc({
                formId:             {$this->form['id']},
                startFieldId:       {$this->start_field_id},
                startDateFormat:    '$start_field_format',
                endFieldId:         {$this->end_field_id},
                endDateFormat:      '$end_field_format',
                countFieldId:       {$this->count_field_id},
                countAdjust:        {$this->count_adjust}
            });";

        $slug = implode( '_', array( 'gw_display_count', $this->start_field_id, $this->end_field_id, $this->count_field_id ) );
        GFFormDisplay::add_init_script( $this->form['id'], $slug, GFFormDisplay::ON_PAGE_RENDER, $script );

        // remove filter so init script is not output on subsequent forms
        remove_filter( 'gform_init_scripts_footer', array( &$this, 'add_init_script' ) );

        return $return;
    }

    function override_submitted_value( $form ) {

        $start_date = false;
        $end_date = false;

        foreach( $form['fields'] as &$field ) {

            if( $field['id'] == $this->start_field_id )
                $start_date = self::parse_field_date( $field );

            if( $field['id'] == $this->end_field_id )
                $end_date = self::parse_field_date( $field );

        }

        if( $start_date > $end_date ) {

            $day_count = 0;

        } else {

            $diff = $end_date - $start_date;
            $day_count = $diff / ( 60 * 60 * 24 ); // secs * mins * hours
            $day_count = round( $day_count ) + $this->count_adjust;

        }

        $_POST["input_{$this->count_field_id}"] = $day_count;

    }

    static function parse_field_date( $field ) {

        $date_value = rgpost("input_{$field['id']}");
        $date_format = empty( $field['dateFormat'] ) ? 'mdy' : esc_attr( $field['dateFormat'] );
        $date_info = GFCommon::parse_date( $date_value, $date_format );
        if( empty( $date_info ) )
            return false;

        return strtotime( "{$date_info['year']}-{$date_info['month']}-{$date_info['day']}" );
    }

}

# Configuration

// Electrical Apprenticeship Configuration
/*
new GWDayCount( array(
	'form_id'        => APPRENTICE_APPLICATION_FORM,
    'start_field_id' => 11, // DOB
    'end_field_id'   => 142, // Today
    'count_field_id' => 143 // Age
) );
*/


// Electrical Apprenticeship Configuration
new GWDayCount( array(
'form_id'        => PRE_APPRENTICE_APPLICATION_FORM,
'start_field_id' => 11, // DOB
'end_field_id'   => 136, // Today
'count_field_id' => 137 // Age
) );


// UEE30820 Application Form Configuration
new GWDayCount( array(
'form_id'        => UEE30820_APPLICATION_FORM,
'start_field_id' => 11,
'end_field_id'   => 142,
'count_field_id' => 143
) );
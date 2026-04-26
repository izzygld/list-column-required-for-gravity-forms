<?php
/**
 * List Column Frontend Handler
 *
 * handles the front-end rendering of required indicators on list field columns
 * adds asterisks to column headers and required attributes to inputs
 *
 * @package LCR_GF
 */

// dont let anyone access this directly
defined( 'ABSPATH' ) || exit;

/**
 * LCR_GF_Frontend class
 *
 * modifies the front-end list field output to show required indicators
 * hooks into gform_column_input_content for input attributes
 * and gform_field_content for column header asterisks
 */
class LCR_GF_Frontend {

    /**
     * hookin up our frontend filters
     * this gets called from the main addon init_frontend()
     *
     * @return void
     */
    public function hookup() {
        // addin required attribute to column inputs
        add_filter( 'gform_column_input_content', array( $this, 'add_required_to_input' ), 10, 6 );

        // addin asterisk to required column headers
        add_filter( 'gform_field_content', array( $this, 'add_required_to_headers' ), 10, 5 );
    }

    /**
     * registerin the render-time filters that flag list fields with required columns
     * as required, so GF emits its standard label asterisk + "indicates required fields"
     * legend. these need to fire in both frontend and admin contexts (entry view, etc),
     * so they get hooked separately from hookup() which only runs on the frontend.
     *
     * @return void
     */
    public function hookup_render_flags() {
        // virtually flag list fields with required columns as required at render time
        // this makes GF show the field-label asterisk + the "* indicates required fields"
        // legend at top of the form. validation is unaffected (handled by validator class).
        add_filter( 'gform_pre_render', array( $this, 'flag_field_required_for_render' ) );
        add_filter( 'gform_admin_pre_render', array( $this, 'flag_field_required_for_render' ) );
    }

    /**
     * flippin isRequired = true on list fields that have any column-level required set
     * but only at render time, so the field-label asterisk and the form-level required
     * legend ("* indicates required fields") appear for accessibility/consistency.
     *
     * note: this does NOT affect validation. our LCR_GF_Validator handles the actual
     * column-level required checks. GF's own list-field "isRequired" validation (which
     * just needs one non-empty row) would only kick in if field-level required was
     * actually set in the editor, which is not the case here.
     *
     * @param array $form the form object being rendered
     * @return array modified form
     */
    public function flag_field_required_for_render( $form ) {
        if ( empty( $form['fields'] ) || ! is_array( $form['fields'] ) ) {
            return $form;
        }

        foreach ( $form['fields'] as $da_field ) {
            if ( ! is_object( $da_field ) || $da_field->type !== 'list' ) {
                continue;
            }

            if ( empty( $da_field->enableColumns ) || ! is_array( $da_field->choices ) ) {
                continue;
            }

            // if user already set field-level required, leave it alone
            if ( ! empty( $da_field->isRequired ) ) {
                continue;
            }

            // check if any column is marked required
            foreach ( $da_field->choices as $da_choice ) {
                if ( ! empty( $da_choice['isColumnRequired'] ) ) {
                    $da_field->isRequired = true;
                    break;
                }
            }
        }

        return $form;
    }

    /**
     * addin required and aria-required attributes to inputs in required columns
     * fires via the gform_column_input_content filter for each column input
     *
     * @param string   $input       the input html markup
     * @param array    $input_info  info about the input type
     * @param GF_Field $field       the list field object
     * @param string   $column_text the column header text
     * @param mixed    $value       the current value
     * @param int      $form_id     the form id
     * @return string modified input html
     */
    public function add_required_to_input( $input, $input_info, $field, $column_text, $value, $form_id ) {
        // only process list fields with columns enabled
        if ( $field->type !== 'list' || empty( $field->enableColumns ) || ! is_array( $field->choices ) ) {
            return $input;
        }

        // check if this column is marked as required
        if ( ! $this->is_column_required( $field, $column_text ) ) {
            return $input;
        }

        // add required and aria-required attributes to the input/select element
        // look for existing aria-invalid attribute as an anchor point
        if ( strpos( $input, 'aria-required' ) === false ) {
            $input = str_replace(
                "aria-invalid=",
                "aria-required='true' aria-invalid=",
                $input
            );
        }

        return $input;
    }

    /**
     * addin asterisk indicators to required column headers in the rendered form
     * fires via the gform_field_content filter for the whole field markup
     *
     * @param string   $content   the field html content
     * @param GF_Field $field     the field object
     * @param mixed    $value     the field value
     * @param int      $lead_id   the entry id (0 for new submissions)
     * @param int      $form_id   the form id
     * @return string modified content with asterisks on required column headers
     */
    public function add_required_to_headers( $content, $field, $value, $lead_id, $form_id ) {
        // only process list fields with columns enabled
        if ( $field->type !== 'list' || empty( $field->enableColumns ) || ! is_array( $field->choices ) ) {
            return $content;
        }

        // check each column and add asterisks to required ones
        foreach ( $field->choices as $da_choice ) {
            if ( empty( $da_choice['isColumnRequired'] ) ) {
                continue;
            }

            $da_col_text = esc_html( rgar( $da_choice, 'text', '' ) );

            if ( empty( $da_col_text ) ) {
                continue;
            }

            // find the column header div and append an asterisk
            // the header markup looks like: <div class="gform-field-label gfield_header_item gform-grid-col">Column Name</div>
            $da_search  = 'gfield_header_item gform-grid-col">' . $da_col_text . '</div>';
            $da_replace = 'gfield_header_item gform-grid-col">' . $da_col_text . '<span class="gfield_required gfield_required_asterisk"> *</span></div>';

            $content = str_replace( $da_search, $da_replace, $content );
        }

        return $content;
    }

    /**
     * checkin if a specific column is marked as required
     *
     * @param GF_Field $field       the list field object
     * @param string   $column_text the column header text to check
     * @return bool true if this column is required
     */
    private function is_column_required( $field, $column_text ) {
        foreach ( $field->choices as $da_choice ) {
            if ( rgar( $da_choice, 'text' ) === $column_text && ! empty( $da_choice['isColumnRequired'] ) ) {
                return true;
            }
        }
        return false;
    }
}

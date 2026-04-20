/**
 * List Column Required for Gravity Forms - Form Editor JS
 *
 * injects a "Required" checkbox next to each column in the list field editor
 * uses gf's built-in callback system: window["gform_append_field_choice_option_list"]
 * which gets called by GetFieldChoices() for list-type fields
 *
 * the isColumnRequired property is stored on each choice object in field.choices[]
 * and gets saved automatically when the form is saved (its part of the field json)
 *
 * @package LCR_GF
 */

(function ($) {
    'use strict';

    /**
     * this callback is called by GetFieldChoices() in gravityforms/js.php
     * for each column choice when rendering the columns list in the form editor
     * it receives the field object and the choice index
     * returns HTML string that gets appended after each column row's inputs
     */
    window['gform_append_field_choice_option_list'] = function (field, index) {
        if (!field || !field.choices || !field.choices[index]) {
            return '';
        }

        var isRequired = field.choices[index].isColumnRequired ? 'checked="checked"' : '';

        var html = '<span class="lcr-gf-required-wrap">';
        html += '<input type="checkbox" ';
        html += 'id="lcr_gf_col_required_' + index + '" ';
        html += 'class="lcr-gf-col-required" ';
        html += 'data-index="' + index + '" ';
        html += isRequired + ' ';
        html += 'onclick="gfLcrSetColumnRequired(this, ' + index + ');" ';
        html += '/>';
        html += '<label for="lcr_gf_col_required_' + index + '" class="lcr-gf-required-label">';
        html += 'Required';
        html += '</label>';
        html += '</span>';

        return html;
    };

    /**
     * called when the "Required" checkbox is toggled for a column
     * sets the isColumnRequired property on the choice object
     * GF saves this as part of the field JSON when the form is saved
     */
    window.gfLcrSetColumnRequired = function (checkbox, index) {
        var field = GetSelectedField();

        if (!field || !field.choices || !field.choices[index]) {
            return;
        }

        field.choices[index].isColumnRequired = checkbox.checked;
    };

    /**
     * when the main field-level "Required" checkbox is toggled ON,
     * auto-check all column required checkboxes for list fields.
     * this saves the admin from having to check each column individually
     * when they want the whole thing required.
     *
     * we listen on the #field_required checkbox which calls
     * SetFieldRequired(this.checked) via onclick in form_detail.php.
     * use 'change' (not 'click') so we run after GF's inline onclick handler
     * has already updated the field model.
     */
    $(document).on('change click', '#field_required', function () {
        var checkbox = this;

        // defer so we run after GF's inline onclick=SetFieldRequired() finishes
        setTimeout(function () {
            if (!checkbox.checked) {
                return;
            }

            // if there are no column-required checkboxes rendered, nothing to do
            var $cols = $('.lcr-gf-col-required');
            if (!$cols.length) {
                return;
            }

            // update the field model so the state persists on save
            if (typeof GetSelectedField === 'function') {
                var field = GetSelectedField();
                if (field && field.choices) {
                    for (var i = 0; i < field.choices.length; i++) {
                        field.choices[i].isColumnRequired = true;
                    }
                }
            }

            // visually check all column-required boxes
            $cols.prop('checked', true);
        }, 0);
    });

})(jQuery);

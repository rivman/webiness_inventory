<?php
/**
 * WsForm
 * Generates HTML form and form widgets.
 *
 * Example usage:
 *
 * <code>
 * $form = new WsForm(WsUrl::link('controller', 'form_action_script'));
 *
 * // widgets
 * $form->textInput(array('name' => 'text_input'));
 * $form->textInput(array(
 *     'name' => 'date_input',
 *     'type' => 'date',
 *     'placeholder' => 'enter date'
 * ));
 *
 * $form->show();
 * </code>
 *
 */
class WsForm
{
    /**
     * @var string $_action Url of form action
     *
     */
    private $_action = '';
    /**
     * @var string $_id ID of form
     *
     */
    private $_id = '';
    /**
     * @var string $_validationRules Form validation rules
     *
     */
    private $_validationRules = '';
    /**
     * @var string $submitButtonText Text for submit button
     *
     */
    public $submitButtonText = '';
    /**
     * @var string $_form Form body
     *
     */
    protected $_form = '';
    /**
     * @var boolean $_formEnded Ensure that submit button is shown only once
     *
     */
    private $_formEnded = false;


    function __construct($action = '')
    {
        $this->_action = $action;
        $this->_id = 'WsForm_'.uniqid();

        $this->_form = '<div class="uk-grid">';
        $this->_form .= '<div class="uk-width-small-1-1 uk-width-medium-9-10">';
        $this->_form .= '<form id="'.$this->_id.'" ';
        $this->_form .= 'class="uk-form uk-form-horizontal" ';
        $this->_form .= 'method="POST" enctype="multipart/form-data" ';
        $this->_form .= 'role="form" action="'.$this->_action.'">';

        // prevent CSRF attack
        if (isset($_SESSION['ws_auth_token'])) {
            $this->_form .= '<input type="hidden" name="csrf" value="';
            $this->_form .= $_SESSION["ws_auth_token"];
            $this->_form .= '">';
        }
    }


    public function __toString()
    {
        if (!$this->_formEnded) {
            $this->formEnd();
        }

        return $this->_form;
    }


    /**
     * Append submit button and validation function at the form end
     *
     */
    private function formEnd()
    {
        $this->_form .= '<button type="submit"'
            .' class="uk-button uk-button-success" id="';
        $this->_form .= $this->_id.'_submit">';
        $this->_form .= $this->submitButtonText.'</button>';

        $this->_form .= '</form></div></div>';

        $this->_form .= '<script>';
        // for form validation
        $this->_form .= '$("#'.$this->_id.'").validate({';
        $this->_form .= 'submitHandler: function(form) {form.submit();}';
        $this->_form .= '});';
        $this->_form .= '</script>';

        // append this only once
        $this->_formEnded = true;
    }

    /**
     * Display generated form on screen
     *
     */
    public function show()
    {
        if (!$this->_formEnded) {
            $this->formEnd();
        }

        echo $this->_form;
    }

    /**
     * Add hidden input element to form
     *
     * @param array $params HTML parameters for <input type="hidden">
     *
     */
    public function hiddenInput($params = array())
    {
        // name of the widget
        if (isset($params['name'])) {
            $name = $params['name'];
        } else {
            $name = uniqid('WsFormUIInput_').uniqid();
        }
        // id of widget
        if (isset($params['id'])) {
            $id = $params['id'];
        } else {
            $id = $this->_id.'_'.$name;
        }
        // value
        if (isset($params['value']) and !empty($params['value'])) {
            $value = $params['value'];
        } else {
            $value = '';
        }

        // add element
        $this->_form .= '<input type="hidden"
            name="'.$name.'" value="'.$value.'"
            id="'.$id.'"
        />';
        unset($name, $id, $value, $params);
    }


    /**
     * Add text input element to form
     *
     * @param array $params HTML parameters for <input type="text">
     *
     */
    public function textInput($params = array())
    {
        // type of input field
        if (isset($params['type'])) {
            $type = $params['type'];
        } else {
            $type = 'text';
        }

        // name of the widget
        if (isset($params['name'])) {
            $name = $params['name'];
        } else {
            $name = uniqid('WsFormUIInput_').uniqid();
        }
        // id of widget
        if (isset($params['id'])) {
            $id = $params['id'];
        } else {
            $id = $this->_id.'_'.$name;
        }

        // value of vidget
        if (isset($params['value'])) {
            $value = $params['value'];
        } else {
            $value = '';
        }

        // label
        if (isset($params['label'])) {
            $label = $params['label'];
        } else {
            $label = '';
        }

        // custom class
        if (isset($params['class'])) {
            $class .= ' '.$params['class'];
        } else {
            $class = '';
        }

        // max length
        if (isset($params['maxlength'])) {
            $maxlength = $params['maxlength'];
        } else {
            switch ($type) {
                case 'date':
                    $maxlength = 11;
                    $class .= ' webiness_datepicker';
                    $type = 'date';
                    break;
                case 'time':
                    $class .= ' webiness_timepicker';
                    $maxlength = 8;
                    $type = 'time';
                    break;
                case 'datetime-local':
                    $class .= ' webiness_datetimepicker';
                    $maxlength = 20;
                    $type = 'datetime-local';
                    break;
                case 'number':
                    $class .= ' webiness_numericinput';
                    $maxlength = 32;
                    $type = 'number';
                    break;
                case 'file':
                    $class .= ' inputfile';
                    break;
                case 'url':
                    $type = 'url';
                    $maxlength = 60;
                    break;
                default:
                    $maxlength = 60;
            }
        }

        // placeholder
        if (isset($params['placeholder'])) {
            $placeholder = $params['placeholder'];
        } else {
            $placeholder = '';
        }

        // readonly widget
        if (isset($params['readonly']) and ($params['readonly'] == true)) {
            $ro = 'readonly';
        } else {
            $ro = '';
        }

        // value is required
        if (isset($params['required']) and ($params['required'] == true)) {
            $rq = 'required';
        } else {
            $rq = '';
        }

        // add text input element
        $this->_form .= '<div class="uk-form-row">';
        if ($label !== '') {
            $this->_form .= '<label class="uk-form-label" for="'.$id.'">';
            $this->_form .= $label;
            $this->_form .= '</label>';
        }

        $this->_form .= '<div class="uk-form-controls">';

        // display link to file if type is file and picture thumbnail if file is
        // picture
        if ($type === 'file') {            
            $this->_form .= '<input type="file"'
                .' name="'.$name.'" '
                .' id="'.$id.'"'
                .' class="'.$class.'"'
                .' placeholder="'.$placeholder.'"'
                .' '.$ro.' '.$rq.'/>';
            
            if (get_called_class() === 'WsModelForm') {
                $file = 'runtime/'.$this->getModelName().'/'.$value;
                $file_url = WsSERVER_ROOT.'/runtime/'.$this->getModelName().'/'
                    .$value;
                if (file_exists(WsROOT.'/'.$file) && is_file(WsROOT.'/'.$file)){
                    // if file is image then show it
                    $img = new WsImage();
                    if ($img->read($file)) {
                        $this->_form .= '<img width=100 height=100 '
                            .'src="'.$file_url.'" />';
                    } else {
                        $this->_form .= '<a href="'
                            .WsUrl::link(WsSERVER_ROOT.'/'.$file_url).'">';
                        $this->_form .= $value;
                        $this->_form .= '</a>';
                    }
                    unset ($img, $file, $file_url);
                }
            }
            
        } else {
            $this->_form .= '<input type="'.$type.'"'
                .' name="'.$name.'" value="'.$value.'"'
                .' id="'.$id.'"'
                .' class="uk-form-width-large '.$class.'"'
                .' placeholder="'.$placeholder.'"'
                .' maxlength='.$maxlength.' '.$ro.' '.$rq.'/>';
        }

        $this->_form .= '</div>';
        $this->_form .= '</div>';
        unset($ro, $rq, $class, $maxlength, $placeholder, $id, $value, $name,
            $type, $label, $params);
    }


    /**
     * Add multiline text input to form
     *
     * @param array $params HTML parameters for <textarea>
     *
     */
    public function textareaInput($params = array())
    {
        // name of the widget
        if (isset($params['name'])) {
            $name = $params['name'];
        } else {
            $name = uniqid('WsFormUIInput_').uniqid();
        }
        // id of widget
        if (isset($params['id'])) {
            $id = $params['id'];
        } else {
            $id = $this->_id.'_'.$name;
        }

        // value of vidget
        if (isset($params['value'])) {
            $value = $params['value'];
        } else {
            $value = '';
        }

        // label
        if (isset($params['label'])) {
            $label = $params['label'];
        } else {
            $label = '';
        }

        // placeholder
        if (isset($params['placeholder'])) {
            $placeholder = $params['placeholder'];
        } else {
            $placeholder = '';
        }

        // custom class
        if (isset($params['class'])) {
            $class = 'webiness_textarea '.$params['class'];
        } else {
            $class = 'webiness_textarea';
        }

        // readonly widget
        if (isset($params['readonly']) and ($params['readonly'] == true)) {
            $ro = 'readonly';
        } else {
            $ro = '';
        }

        // value is required
        if (isset($params['required']) and ($params['required'] == true)) {
            $rq = 'required';
        } else {
            $rq = '';
        }

        // add text area element
        $this->_form .= '<div class="uk-form-row">';
        if ($label != '') {
            $this->_form .= '<label class="text-left" for="'.$id.'">';
            $this->_form .= $label;
            $this->_form .= '</label>';
        }
        $this->_form .= '<div class="uk-form-controls">';
        $this->_form .= '
            <textarea rows=5
                name="'.$name.'"
                id="'.$id.'"
                class="uk-form-width-large '.$class.'"
                placeholder="'.$placeholder.'"
                '.$ro.' '.$rq.'>';
        $this->_form .= $value;
        $this->_form .= '</textarea>';
        $this->_form .= '</div>';
        $this->_form .= '</div>';

        unset($rq, $ro, $placeholder, $class, $id, $name, $label, $params);
    }


    /**
     * Add checkbox to the form
     *
     * @param array $params HTML parameters for <input type="checkbox">
     *
     */
    public function booleanInput($params = array())
    {
        // name of the widget
        if (isset($params['name'])) {
            $name = $params['name'];
        } else {
            $name = uniqid('WsFormUIInput_').uniqid();
        }
        // id of widget
        if (isset($params['id'])) {
            $id = $params['id'];
        } else {
            $id = $this->_id.'_'.$name;
        }

        // label
        if (isset($params['label'])) {
            $label = $params['label'];
        } else {
            $label = '';
        }

        // custom class
        if (isset($params['class'])) {
            $class = ' '.$params['class'];
        } else {
            $class = '';
        }

        // readonly widget
        if (isset($params['readonly']) and ($params['readonly'] == true)) {
            $ro = 'readonly';
        } else {
            $ro = '';
        }

        // is checked
        if (isset($params['checked']) and ($params['checked'] == true)) {
            $ch = 'checked';
        } else {
            $ch = '';
        }

        // add boolean element
        $this->_form .= '<div class="uk-form-row">';
        $this->_form .= '<div class="uk-form-controls">';

        if ($label != '') {
            $this->_form .= '<label for="'.$id.'">';
        }

        $this->_form .= '<input type="hidden" value="false" name="'.$name.'"/>';
        $this->_form .= '<input type="checkbox"
            name="'.$name.'"
            id="'.$id.'"
            value="true"
            data-val="true"
            class="uk-form-width-large '.$class.'"
            '.$ro.'
            '.$ch.' />';

        if ($label != '') {
            $this->_form .= ' '.$label;
            $this->_form .= '</label>';
        }
    
        $this->_form .= '</div></div>';

        unset($label, $class, $ch, $ro, $id, $name, $params);
    }


    /**
     * Add selection box to the form
     *
     * @param array $list List of selections
     * @param array $params HTML parameters for <select> element
     *
     */
    public function selectInput($list, $params = array())
    {
        // name of the widget
        if (isset($params['name'])) {
            $name = $params['name'];
        } else {
            $name = uniqid('WsFormUIInput_').uniqid();
        }
        // id of widget
        if (isset($params['id'])) {
            $id = $params['id'];
        } else {
            $id = $this->_id.'_'.$name;
        }

        // label
        if (isset($params['label'])) {
            $label = $params['label'];
        } else {
            $label = '';
        }

        // value of vidget
        if (isset($params['value'])) {
            $value = $params['value'];
        } else {
            $value = '';
        }

        // custom class
        if (isset($params['class'])) {
            $class = $params['class'];
        } else {
            $class = '';
        }

        // value is required
        if (isset($params['required']) and ($params['required'] == true)) {
            $rq = 'required';
        } else {
            $rq = '';
        }

        // add select element
        $this->_form .= '<div class="uk-form-row">';
        if ($label != '') {
            $this->_form .= '<label class="text-left" for="'.$id.'">';
            $this->_form .= $label;
            $this->_form .= '</label>';
        }
        $this->_form .= '<div class="uk-form-controls">';
        $this->_form .= '
            <select
                style="width: 100%"
                name="'.$name.'"
                id="'.$id.'"
                class="uk-form-width-large '.$class.'"
                '.$rq.' >';

        foreach ($list as $l) {
            if ($l['display'] === $value or $l['option'] === $value) {
                $this->_form .= '<option value="'.$l['option']
                    .'" selected>'.$l['display'].'</option>';
            } else {
                $this->_form .= '<option value="'.$l['option'].'">'
                    .$l['display'].'</option>';
            }
        }
        $this->_form .= '</select>';
        $this->_form .= '</div>';
        $this->_form .= '</div>';

        unset($l, $list, $rq, $value, $class, $id, $name, $label, $params);
    }


    /**
     * Append custom HTML to form
     *
     * @param string $html HTML to append to form
     *
     */
    public function appendHTML($html)
    {
        $this->_form .= $html;
    }
}

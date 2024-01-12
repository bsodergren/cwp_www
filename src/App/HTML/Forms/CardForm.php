<?php

namespace CWP\HTML\Forms;

use CWP\HTML\HTMLForms;
use CWP\Template\Template;

class CardForm
{
    private $templatePath = 'elements/form';
    private $cardParams = [];
    private $form_id;
    private $form_process;
    private $form_name;
    public function __construct($form_name, $form_process = '', $form_id = 'myForm', $form_action = '')
    {
        $this->cardParams['FORM_NAME'] = $form_name;
        $this->cardParams['FORM_ID'] = $form_id;
        $this->cardParams['HIDDEN'] = '';

        if($form_process != '') {
            $this->cardParams['HIDDEN'] .= HTMLForms::draw_hidden('FORM_PROCESS', $form_process);
        }

        if($form_action != '') {
            $this->cardParams['HIDDEN'] .= HTMLForms::draw_hidden('action', $form_action);
        }
    }

    public function __set($name, $value)
    {

        $key = "FORM_".strtoupper($name);
        $this->cardParams[$key] = $name .'="'.$value.'"';
    }

    public function cardHeader($text)
    {
        $this->cardParams['CARD_HEADER'] = Template::GetHTML($this->templatePath.'/header', ['HEADER_TEXT' => $text]);
    }

    public function cardBody($cardBody, $button = false)
    {
        if($button !== false) {
            $button = Template::GetHTML($this->templatePath.'/button', ['CARD_BUTTON_TEXT' => $button]);
        }
        $this->cardParams['CARD_BODY'] = Template::GetHTML($this->templatePath.'/body', ['CARD_HTML' => $cardBody,'CARD_BUTTON' => $button]);
    }

    public function card()
    {
        return  Template::GetHTML($this->templatePath.'/card', $this->cardParams);
    }


    public static function formCard($form_name, $form_process, $form_id = 'myForm', $options = [])
    {
        $header_text = 'Card Header';
        $button =  false;
        $body_text = 'Card Body';

        if(array_key_exists('header', $options)) {
            $header_text = $options['header'];
        }
        if(array_key_exists('body', $options)) {
            $body_text = $options['body'];
        }
        if(array_key_exists('$button', $options)) {
            $button = $options['$button'];
        }

        $card = new self($form_name, $form_process, $form_id);
        $card->cardHeader($header_text);
        $card->cardBody($body_text, $button);
        return $card->card();


    }



}

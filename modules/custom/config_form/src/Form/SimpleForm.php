<?php

namespace Drupal\config_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class SimpleForm extends FormBase {
  /**
   * @return string
   */
  public function getFormId() {
    return 'config_form';
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array|void
   */
  public function buildForm(array $form, FormStateInterface $form_state)  {

    $form['number_1'] = [
      '#type' => 'textfield',
      'title' => $this->t('Number of results per page')
    ];
      $form['submit']=[
        '#type' => 'submit',
        '#value' => $this->t('Submit'),

    ];

      return $form;
  }
  public function submitForm(array &$form, FormStateInterface $form_state)
  {

  }

}
<?php
/**
 * @file
 * Contains \Drupal\simple\Form\ModuleConfigForm.
 */
namespace Drupal\movies\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfigForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'movies_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#theme'] = 'form';
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('movies.settings');
    $form['content_per_page'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Number of contents per page'),
      '#default_value' => $config->get('movies.content_per_page'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('movies.settings');
    $config->set('movies.content_per_page', $form_state->getValue('content_per_page'));
    $config->save();

    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'movies.settings',
    ];
  }
}
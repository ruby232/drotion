<?php

namespace Drupal\paragraphs_add;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a helper for paragraph forms.
 */
trait ParagraphFormHelperTrait {

  protected $lineageRevisioner;

  protected $rootParent;
  protected $parentFieldName;
  protected $paragraphsType;


  /**
   * Returns the lineage revisioner service.
   *
   * @return \Drupal\paragraphs_add\ParagraphLineageRevisioner|null
   *   The lineage revisioner service data.
   */
  protected function lineageRevisioner() {
    if (!isset($this->lineageRevisioner)) {
      $this->lineageRevisioner = \Drupal::service('paragraphs_add.lineage.revisioner');
    }
    return $this->lineageRevisioner;
  }

  protected function getParentFieldName() {
    return $this->parentFieldName;
  }

  protected function getParagraphsType() {
    return $this->paragraphsType;
  }

  /**
   * {@inheritdoc}
   *
   * Overridden to store the root parent entity.
   */
  public function buildForm(array $form, FormStateInterface $form_state, EntityInterface $root_parent = NULL, $parent_field_name = NULL, $paragraphs_type = NULL) {
    $this->rootParent = $root_parent;
    $this->parentFieldName = $parent_field_name;
    $this->paragraphsType = $paragraphs_type;

    return parent::buildForm($form, $form_state);
  }

}

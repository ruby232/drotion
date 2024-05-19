<?php

namespace Drupal\paragraphs_add\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\paragraphs_add\ParagraphFormHelperTrait;

/**
 * ParagraphEditForm class.
 */
class ParagraphAddForm extends ContentEntityForm {

  use ParagraphFormHelperTrait;

  /**
   * {@inheritdoc}
   */
  protected function init(FormStateInterface $form_state) {
    if (!$this->entity->isTranslatable()) {
      parent::init($form_state);
      return;
    }

    $langcode = \Drupal::languageManager()
      ->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)
      ->getId();
    $form_state->set('langcode', $langcode);

    if (!$this->entity->hasTranslation($langcode)) {
      $manager = \Drupal::service('content_translation.manager');

      $translation_source = $this->entity;

      $host = $this->entity->getParentEntity();
      $host_source_langcode = $host->language()->getId();
      if ($host->hasTranslation($langcode)) {
        $host = $host->getTranslation($langcode);
        $host_source_langcode = $manager->getTranslationMetadata($host)
          ->getSource();
      }

      if ($this->entity->hasTranslation($host_source_langcode)) {
        $translation_source = $this->entity->getTranslation($host_source_langcode);
      }

      $this->entity = $this->entity->addTranslation($langcode, $translation_source->toArray());
      $manager->getTranslationMetadata($this->entity)
        ->setSource($translation_source->language()->getId());
    }

    parent::init($form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $parent_field = $this->rootParent?->get($this->getParentFieldName());
    $parent_field_label = $parent_field->getFieldDefinition()->getLabel();
    $lineage = $this->rootParent->label() . ' > ' . $parent_field_label . ' > ' . $this->getParagraphsType()->label();

    $form['#title'] = $this->t('Add @lineage', [
      '@lineage' => $lineage,
    ]);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->rootParent->get($this->parentFieldName)->appendItem($this->entity);
    if ($this->lineageRevisioner()
      ->shouldCreateNewRevision($this->rootParent)) {
      return $this->lineageRevisioner()
        ->saveNewRevision($this->rootParent, $this->parentFieldName);
    }

    return $this->entity->save();
  }

}

<?php

namespace Drupal\paragraphs_add;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\RevisionableEntityBundleInterface;
use Drupal\paragraphs\ParagraphInterface;

/**
 * ParagraphLineageRevisioner class.
 */
class ParagraphLineageRevisioner {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Construct a new ParagraphLineageRevisioner object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Provides  service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Saves all of given entity's lineage as new revisions.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity whose lineage to save as new revisions.
   *
   * @return int
   *   Either SAVED_NEW or SAVED_UPDATED, depending on the operation performed.
   */
  public function saveNewRevision(ContentEntityInterface $entity, $parentFieldName) {
    try {
      $entity->setNewRevision();
    }
    catch (\LogicException $e) {
      // A content entity not necessarily supports revisioning.
    }

    return $entity->save();
  }


  /**
   * Checks if a given entity should be saved as a new revision by default.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check for default new revision.
   *
   * @return bool
   *   TRUE if this entity is set to be saved as a new revision by default,
   *   FALSE otherwise.
   */
  public function shouldCreateNewRevision(EntityInterface $entity) {
    $new_revision_default = FALSE;
    $bundle_entity_type = $entity->getEntityType()->getBundleEntityType();
    if (!$bundle_entity_type) {
      return $new_revision_default;
    }

    $bundle_entity = $this->entityTypeManager
      ->getStorage($bundle_entity_type)
      ->load($entity->bundle());

    if ($bundle_entity instanceof RevisionableEntityBundleInterface) {
      $new_revision_default = $bundle_entity->shouldCreateNewRevision();
    }

    return $new_revision_default;
  }

}

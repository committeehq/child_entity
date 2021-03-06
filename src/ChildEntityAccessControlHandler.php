<?php

namespace Drupal\child_entity;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Access controller for the paragraphs entity.
 *
 * @see \Drupal\child_entity\ChildEntityTrait.
 */
class ChildEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    // Allowed when the operation is not view or the status is true.
    /** @var \Drupal\child_entity\Entity\ChildEntityInterface $entity */

    if ($operation === 'view' && is_subclass_of($entity, EntityPublishedInterface::class)) {
      $access_result = AccessResult::allowedIf($entity->isPublished());
    }
    else {
      $access_result = AccessResult::allowed();
    }

    if ($entity->getParentEntity() != NULL) {
      $parent_access = $entity->getParentEntity()->access($operation, $account, TRUE);
      $access_result = $access_result->andIf($parent_access);
    }

    return $access_result;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    // Allow paragraph entities to be created in the context of entity forms.
    if (\Drupal::requestStack()->getCurrentRequest()->getRequestFormat() === 'html') {
      return AccessResult::allowed()->addCacheContexts(['request_format']);
    }
    return AccessResult::neutral()->addCacheContexts(['request_format']);
  }

}

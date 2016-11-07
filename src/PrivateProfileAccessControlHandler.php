<?php

namespace Drupal\userprofiles;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\user\Entity\User;
use Drupal\userprofiles\Entity\PrivateProfile;
use Drupal\userprofiles\Entity\PublicProfile;

/**
 * Access controller for the Private Profile entity.
 *
 * @see \Drupal\userprofiles\Entity\PrivateProfile.
 */
class PrivateProfileAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    $uid = \Drupal::currentUser()->id();
    $user = User::load($uid);

    /** @var \Drupal\userprofiles\PrivateProfileInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished private profile entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published private profile entities');

      case 'update':
        if (!$user->hasRole('administrator')) {
          $private_ref = $user->field_privref->entity;
          return AccessResult::allowedIf(($private_ref->id() == $entity->id()));
        }

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete private profile entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add private profile entities');
  }

}

<?php

namespace Drupal\EntitySync;
  
  
class ControllerBase extends \EntityAPIController {
  
  
  public function save($entity, DatabaseTransaction $transaction = NULL) {
    $transaction = isset($transaction) ? $transaction : db_transaction();
    if (!isset($entity->is_new)) {
      $entity->is_new = empty($entity->key_id);
      $entity->created = time();
    } else {
      $entity->is_new_revision = TRUE;
    }
    if (!empty($entity->{$this->idKey}) && !isset($entity->original)) {
      // In order to properly work in case of name changes, load the original
      // entity using the id key if it is available.
      $entity->original = entity_load_unchanged($this->entityType, $entity->{$this->idKey});
    }
    if (!isset($entity->uid)) {
      $entity->uid = $GLOBALS['user']->uid;
    }
    // The changed timestamp is always updated for bookkeeping purposes.
    $entity->changed = time();
    try {      
      $this->invoke('presave', $entity);
      xdebug_break();
      if ($entity->is_new) {
        $entity->created = 
        $return = drupal_write_record($this->entityInfo['base table'], $entity);
        if ($this->revisionKey) {
          $this->saveRevision($entity);
        }
        xdebug_break();
        $this->invoke('insert', $entity);
        xdebug_break();
      }
      else {
        // Update the base table if the entity doesn't have revisions or
        // we are updating the default revision.
        if (!$this->revisionKey || !empty($entity->{$this->defaultRevisionKey})) {
          $return = drupal_write_record($this->entityInfo['base table'], $entity, $this->idKey);
        }
        if ($this->revisionKey) {
          $return = $this->saveRevision($entity);
        }
        $this->resetCache(array($entity->{$this->idKey}));
        $this->invoke('update', $entity);

        // Field API always saves as default revision, so if the revision saved
        // is not default we have to restore the field values of the default
        // revision now by invoking field_attach_update() once again.
        if ($this->revisionKey && !$entity->{$this->defaultRevisionKey} && !empty($this->entityInfo['fieldable'])) {
          field_attach_update($this->entityType, $entity->original);
        }
      }

      // Ignore slave server temporarily.
      db_ignore_slave();
      unset($entity->is_new);
      unset($entity->is_new_revision);
      unset($entity->original);

      return $return;
    }
    catch (Exception $e) {
      xdebug_break();
      $transaction->rollback();
      watchdog_exception($this->entityType, $e);
      throw $e;
    }
  }
  
  
}



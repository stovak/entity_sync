#Entity Sync

Attempting to solve the age-old problem of syncing entities across Drupal installations. This modules creates a new entity called "deployment". In the deployment entity is entityreference field to point to entities in the deployment. 

##Submodules:
1. entity_sync_deployment - defines the "deployment" entity. Represents a group of entities that can be pushed
  1. "entity_sync_deployment" entity holds the information for the deployment
  1. "enttiy_sync_deployment_revision" holds revision information for deployments
  1. "field_deployment_item" entityreference field on deployment to hold references to entities in the deployment
1. entity_sync_push - does the work of pushing an entity to a remote source
  1. "entity_sync_push" entity holds remote url and login and Deployment group reference.
1. entity_sync_yaml - looks up entities by UUID and creates YAML to supply the push module

###TODO:
1. UI work for entity_deployment CRUD
1. Field_Api work for entityreference field to reference entities in the deployment
1. Push CRUD
1. Push diff
1. Push processing
1. Test cases

##Workflow
1. Create a deployment group of entities
1. Create a Push request for those entities
1. To remove entity, unpublish or disable and add to deployment request (e.g. nodes & views)
1. User approves push diff. 
  1. Diff logs into remote, 
  2. looks up current entity values
  3. uses diff module to show differences between local and remote
1. Submit Push request to queue from diff screen
1. Entities are published to remote

##Assumptions
1. Entity type exists on both local and remote
1. This module exists and is enabled on local and remote
1. No entity should ever be "deleted" from remote. "Unpublish" or "disable" only.
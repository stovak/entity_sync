(function($) {
  Drupal.behaviors.EntitySyncPull = {
    "attach": function(context) {
      jQuery("#entity-sync-remote-pull-bundle .entity-sync-remote-entity", context).not(".entity-sync-remote-entity-processed").each(function(idx, elm){
        jQuery(".entity-sync-remote-diff", elm).load("/entity/sync/diff/"+$(elm).attr("data-entity-type-id")+"/"+$(elm).attr("data-uuid"));
        jQuery(elm).addClass("entity-sync-remote-entity-processed");
      });
    }
  }
})(jQuery)


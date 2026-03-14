var orders = (function () {
  
  var l10n = x5engine.l10n.get;

  var evadeOrder = function(elem) {
    var evade_url = elem.href;
    var enable_tracking  = elem.dataset.enableTracking;

    if (enable_tracking === 'true') {
      var tracking_code = window.prompt(l10n('admin_confirm_process', 'Are you sure?') + "\n\n" + l10n('admin_confirm_process_tracking_code', 'Tracking code'));
      if (tracking_code === null) {
        return false;
      } else if (tracking_code !== '') {
        evade_url += '&track_code=' + tracking_code;
      } 
    } else {
      var confirm = window.confirm(l10n('admin_confirm_process', 'Are you sure?'));
      if (confirm === false) {
        return false;
      }
    }
  
    window.location.href = evade_url;
    return false;
  };

  // public methods
  return {
    evadeOrder: evadeOrder
  };

})();

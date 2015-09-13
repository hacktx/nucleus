$(document).keypress(function(e) {
  if(e.which == 13) {
    if($("#member-search").val()){
      var query = $("#member-search").val(),
          base = window.location.pathname,
          sep = (base.indexOf('?') > -1) ? '&' : '?';

      window.location.replace( base + sep + "search=" + query );
    }
  }
});
$('head').append('<link rel="stylesheet" type="text/css" href="/css/bootstrap-sortable.css" />');

function makeCall(url, postdata) {
  $.post(url, postdata, function( data ) {
    newStatus = "Pending";
    switch(postdata['status']) {
      case 0: newStatus = "Pending"; break;
      case 1: newStatus = "Accepted"; break;
      case 2: newStatus = "Waitlisted"; break;
      case 3: newStatus = "Rejected"; break;
    }

    $("#" + postdata['user'] + "status").find("span").find(".text").text(newStatus);
    $("#" + postdata['user'] + "status").find("span").find(".circle").attr('class', 'circle ' + newStatus);
  });
}

function makeRolesCall(url, postdata) {
  $.post(url, postdata, function() {} );
}

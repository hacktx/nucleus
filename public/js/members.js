function makeCall(url, postdata) {
  $.post(url, postdata, function( data ) {
    if(postdata['status'] === null) {
      return;
    }

    newStatus = "Pending";
    switch(postdata['status']) {
      case 0: newStatus = "Pending"; break;
      case 1: newStatus = "Accepted"; break;
      case 2: newStatus = "Waitlisted"; break;
      case 3: newStatus = "Rejected"; break;
      case 4: newStatus = "Confirmed"; break;
    }

    $("#" + postdata['user'] + "status").find("span").find(".text").text(newStatus);
    $("#" + postdata['user'] + "status").find("span").find(".circle").attr('class', 'circle ' + newStatus);
  });
}

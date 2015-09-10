function optOut() {
  $(".checkbox.accept-sub-layer:not(.opt-out)").each(function(i, e) {
    $(this).find("input").attr("disabled", !$(this).find("input").attr("disabled"));
    $(this).find("input").attr('checked', false);
  });
}

$("#question").click(function () {
  openNav();
});

$("#closebtn").click(function () {
  closeNav();
});

function openNav() {
  $(".hideShow").css("display", "flex");
}

/* Set the width of the side navigation to 0 */
function closeNav() {
  $(".hideShow").css("display", "none");
}

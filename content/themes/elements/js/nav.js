$(document).ready( function() {

  var nav = $('header nav');
  var navOpen = $('.nav-open');
  var navClose = $('.nav-close');

  navOpen.on('click', function() {
    nav.addClass("is-active");
  });

  navClose.on('click', function() {
    nav.removeClass("is-active");
  });

});
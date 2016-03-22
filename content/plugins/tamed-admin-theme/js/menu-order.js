jQuery(document).ready( function($) {
  function itemsUpdate() {
    var items = [];

    $( "#tamed-menu-order li p" ).each(function() {
      var name = $(this).text().replace(/[0-9]/g, '');
      var page = $(this).parent().data("page");

      items.push({
        "name": name,
        "page": page
      });
    });

    var stringed = JSON.stringify(items);
    $("#tamed_menu_list_input").val(stringed);
  };

  itemsUpdate();

  $('#tamed-menu-order').sortable({
    update: itemsUpdate
  });

  $('#remove_menuOrder_button').click( function() {
    $('#tamed_menu_list_input').val('');
    $('#tamed_menu_removals_input').val('');
    $('#tamed-menu-order').addClass('reset');

    var name_items = $('#tamed-menu-order li');

    for(i = 0; i < name_items.length; i++){
      var item = $(name_items[i]).find('input');
      item.attr('value', '');
    };
  });
});
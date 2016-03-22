jQuery(document).ready( function($) {

  var item = $('#tamed-menu-order li');
  var item_edit = item.find('.menu-item-edit');

  item_edit.click( function() {
    var parent = $(this).parent();

    var edit = parent.find('.menu-item-edit');
    var name = parent.find('p');
    var remove = parent.find('.menu-item-remove');
    var input = parent.find('input');
    var save = parent.find('.menu-item-save');

    edit.hide();
    name.hide();
    remove.hide();
    input.show();
    save.show();

    save.click( function() {
      edit.show();
      name.show();
      remove.show();
      input.hide();
      save.hide();

      var value = input.val();
      name.html(value);
      $(input)[0].setAttribute('value', value);
    });
  });

});
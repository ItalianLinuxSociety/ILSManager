$(document).ready (function () {
	$('.showallhidden').click (function () {
		$('tr.hide').toggle ();
	});

  $('.radioselector').each (function () {
    var val = $(this).find ('input[type=radio]:checked').val ();
    $(this).find ('p').hide ();
    $(this).find ('p[class=selectable_' + val + ']').show ();
  });

  $('.radioselector input[type=radio]').click (function () {
    var val = $(this).val ();
    row = $(this).parent ().parent ();
    row.find ('p').hide ();
    row.find ('p[class=selectable_' + val + ']').show ();
  });

  $('.tablesorter').tablesorter ();

  $('#listmembers').hide ();

  $('input#socio').autocomplete ({
    source: '?function=async&action=socio',
    minLenght: 2,
    select: function (event, ui) {
      $('#listmembers tbody').empty ().append ('<tr><td>' + ui.item.label + '</td><td>' + ui.item.form + '</td></tr>');
      $('#listmembers').show ();
    }
  });

  $('#listaccounts').hide ();

  $('input#conto').autocomplete ({
    source: '?function=async&action=conto',
    minLenght: 2,
    select: function (event, ui) {
      $('#listaccounts tbody').empty ().append ('<tr><td>' + ui.item.label + '</td><td>' + ui.item.form + '</td></tr>');
      $('#listaccounts tbody input[name=dare]').val ($('input[type=hidden][name=nd]').val ());
      $('#listaccounts tbody input[name=avere]').val ($('input[type=hidden][name=na]').val ());
      $('#listaccounts').show ();
    }
  });

  $('.modal-footer .save-button').click (function () {
    $(this).parent ().parent ().find ('.modal-body form').submit ();
  });
});

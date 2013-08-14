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

  $('input#ownername').autocomplete ({
    source: '?function=async&action=nomi',
    minLenght: 2,
    select: function (event, ui) {
      $('input#ownername').val (ui.item.label);
      $('input[name=owner]').val (ui.item.id);
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

  if ($('select[name=type]').length != 0) {
    $('select[name=type]').change (function () {
      if ($(this).find ('option:selected').val () == 'associazione') {
        $('#members').parents ('.control-group').show ();
      }
      else {
        $('#members').val ('0').parents ('.control-group').hide ();
      }
    });

    $('select[name=type]').change ();
  }
});


jQuery( function($) { // In here $ is jQuery

bbSortForums = {
	handleText: 'drag',
	handle: '',
	sortCfg: {
		accept: 'forum',
		handle: 'strong.sort-handle',
		opacity: .3,
		helperclass: 'helper',
		onStop: function() {
			bbSortForums.place = null;
			bbSortForums.recolor();
		}
	},
	editText: 'Edit Forum Order &#187;',
	saveText: 'Save Forum Order &#187;',
	place: null,  // The id of the list item it's currently hovering before
	placed: null, // The id of the list item it's been made a child of
	rtl: 'rtl' == $('html').attr( 'dir' ),

	recolor: function() {
		$('#the-list li:gt(0)').css( 'background-color', '' ).filter(':even').removeClass('alt').end().filter(':odd').addClass('alt');
	},

	checkHover: function(el, doit) {
		if ( this.place == el.id && doit )
			return;

		if ( !doit ) {
			this.place = null;
			return;
		}

		this.place = el.id;
		if ( $('#' + this.place).children('ul[li:visible]').size() ) // Don't shift over if there's already a UL with stuff in it
			return;

		var id = this.place.split('-')[1];
		$('#' + this.place).not('[ul]').append("<ul id='forum-root-" + id + "' class='list-block holder'></ul>").end().children('ul').append(jQuery.iSort.helper.get(0)); // Place in shifted box
		this.placed = 'forum-' + id;
	},

	serialize: function () {
		h = '';
		$('#the-list, #the-list ul').each( function() {
			var i = this.id;
			$('#' + i + '> .forum').each( function () {
				if (h.length > 0)
					h += '&';
				var root = 'the-list' == i ? 0 : i.split('-')[2];
				h += 'root[' + root + '][]=' + this.id.split('-')[1];
			} );
		} );
		return h;
	},

	init: function() {
		this.handle = "<strong class='sort-handle'>[" + this.handleText + "]&nbsp;</strong>";
		var div = document.createElement('div');
		div.innerHTML = this.saveText; // Save the raquo!
		this.saveText = div.childNodes[0].nodeValue;
		div.innerHTML = this.editText; // Save the raquo!
		this.editText = div.childNodes[0].nodeValue;
		div = null;
		$('#the-list').after("<p class='submit'><input type='button' id='forum-order-edit' value='" + this.editText + "' /></p>");

		$('#forum-parent-row, #forum-position-row').remove();

		$('#add-forum').submit( function() {
			theList.alt = 'alt';
			theList.showLink = 0;
			theList.addComplete = function() {
				if ( bbSortForums.saveText == $('#forum-order-edit').val() ) {
					var last = $('#the-list li:last').find('div.alignright').after(bbSortForums.handle).end()[0];
					$('#the-list').SortableAddItem(last);
				}
			}
			theList.ajaxAdder( 'forum', 'add-forum' );
			return false;
		} );

		$('#forum-order-edit').toggle( function() {
			$(this).val(bbSortForums.saveText);
			$('#the-list li:gt(0) div.alignright').after(bbSortForums.handle);
			$('#the-list').Sortable( bbSortForums.sortCfg );
		}, function() {
			$(this).val(bbSortForums.editText);
			$('.sort-handle').remove();

			var hash = bbSortForums.serialize();
			hash += '&' + $.SortSerialize('the-list').hash.replace(/the-list/g, 'order').replace(/forum-/g, '')
			$('#the-list').SortableDestroy();

			$.post(
				'admin-ajax.php',
				'action=order-forums&_ajax_nonce=' +  $('#add-forum input[name=order-nonce]').val() + '&' + hash
			);
		} );
	}
}

// overwrite with more advanced function
jQuery.iSort.checkhover = function(e,o) {
	if (!jQuery.iDrag.dragged)
		return;

	if ( e.dropCfg.el.size() > 0 ) {
		var bottom = jQuery.grep(e.dropCfg.el, function(i) { // All the list items whose bottom edges are inside the draggable
			var x = bbSortForums.rtl ? i.pos.x + i.pos.wb > jQuery.iDrag.dragged.dragCfg.nx + jQuery.iDrag.dragged.dragCfg.oC.wb : i.pos.x < jQuery.iDrag.dragged.dragCfg.nx;
			return i.pos.y + i.pos.hb > jQuery.iDrag.dragged.dragCfg.ny && i.pos.y + i.pos.hb < jQuery.iDrag.dragged.dragCfg.ny + 30 && x;
		} );

		if ( bottom.length > 0 ) { // Use the lowest one one the totem pole
			var x = bbSortForums.rtl ? bottom[bottom.length-1].pos.x + bottom[bottom.length-1].pos.wb - 30 > jQuery.iDrag.dragged.dragCfg.nx + jQuery.iDrag.dragged.dragCfg.oC.wb : bottom[bottom.length-1].pos.x + 30 < jQuery.iDrag.dragged.dragCfg.nx;
			if ( bbSortForums.placed != bottom[bottom.length-1].id || !x ) { // Testing to see if still placed in shifted box
				bbSortForums.placed = null;
				jQuery(bottom[bottom.length-1]).after(jQuery.iSort.helper.get(0));
			}
			bbSortForums.checkHover(bottom[bottom.length-1], x); // If far enough right, shift it over
			return;
		}

		// Didn't find anything by checking bottems.  Look at tops
		var top = jQuery.grep(e.dropCfg.el, function(i) { // All the list items whose top edges are inside the draggable
			var x = bbSortForums.rtl ? i.pos.x + i.pos.wb > jQuery.iDrag.dragged.dragCfg.nx : i.pos.x < jQuery.iDrag.dragged.dragCfg.nx;
			return i.pos.y > jQuery.iDrag.dragged.dragCfg.ny && i.pos.y < jQuery.iDrag.dragged.dragCfg.ny + 30 && x;
		} );

		if ( top.length ) { // Use the highest one (should be only one)
			jQuery(top[0]).before(jQuery.iSort.helper.get(0));
			bbSortForums.checkHover(top[0], false);
			return;
		}
	}
	jQuery.iSort.helper.get(0).style.display = 'block';
}

if ( 'undefined' != typeof bbSortForumsL10n )
	$.extend( bbSortForums, bbSortForumsL10n );

bbSortForums.init();

} );

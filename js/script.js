$(document).ready(function () {
	window.file_is_open = 0;
	window.file_open_id = 1;
	var docs_open = 0;
	$('.linenos .number').show();
	$('.linenos .current').hide();
	$(
		'.linenos[data-row-number=1] .number')
		.hide();
	$(
		'.linenos[data-row-number=1] .current')
		.show();
	$('#dropdown_area').hide();
	$('#help_modal').hide();
	$(
		'.projects_container .projects_file_list')
		.hide();
	$('.hover').hide();
	$('li .current').hover(function () {
		// Hover
		$(this).find('.default').hide();
		$(this).find('.hover').show();
	}, function () {
		// Leave
		$(this).find('.hover').hide();
		$(this).find('.default').show();
	});
	$('#menu_search').click(function () {
		if(docs_open === 0) {
			$('#docs').animate({
				left: 200
			}, function () {
				$('#menu_search').html(
					'Close Docs&nbsp;&laquo;');
			});
			docs_open = 1;
		}
		else {
			$('#docs').animate({
				left: -200
			}, function () {
				$('#docs').css('left',
					'-webkit-calc(-100% + 500px)');
				$('#docs').css('left',
					'-moz-calc(-100% + 500px)');
				$('#menu_search').html(
					'View Docs&nbsp;&raquo;');
			});
			docs_open = 0;
		}
	});
	window.status_text_progress = false;
	setInterval(function () {
		if(window.status_text_progress ===
			false) {
			$('.status_text').text('Dormant').css(
				'color', 'grey');
		}
		window.status_text_progress = false;
	}, 1500);
	$('#textarea').bind(
		'input propertychange', function () {
		window.file_unsaved = 1;
	});
	setInterval(function () {
		if(window.current_file_id !== null) {
			if(window.file_unsaved == 1) {
				saveFile(window.current_file_id);
			}
		}
	}, 5000);
	$('textarea:input').keydown(function (
		e) {
		// If user presses tab key
		if(e.keyCode == 9) {
			// Add a tab, remain focus on textarea and prevent default behaviour of moving to next input field.
			var myValue = "    ";
			var startPos = this.selectionStart;
			var endPos = this.selectionEnd;
			var scrollTop = this.scrollTop;
			this.value = this.value.substring(0,
				startPos) + myValue + this.value.substring(
				endPos, this.value.length);
			this.focus();
			this.selectionStart = startPos +
				myValue.length;
			this.selectionEnd = startPos +
				myValue.length;
			this.scrollTop = scrollTop;
			e.preventDefault();
		}
	});
	$('#textarea').scroll(function () {
		// When the textarea is scrolled, scroll the line numbers too.
		document.getElementById(
			'linenosblock').scrollTop = this.scrollTop;
	});
	// Open and close the sidebar list.
	$('.projects_container').click(function () {
		$(this).find('.projects_file_list').click(function () {
			return false; // Messy workaround to not close the list when the list is clicked.
		});
		var rotated = $(this).attr(
			'data-visibility');
		if(rotated == 1) {
			// CSS animation, rotate chevron
			$(this).find('.icon').css({
				'transition': 'rotate 1s ease',
				WebkitTransform: 'rotate(0deg)'
			});
			$(this).find('.projects_file_list').hide();
			$(this).attr('data-visibility', '0');
		}
		else {
			$(this).find('.icon').css({
				'transition': 'rotate 1s ease',
				WebkitTransform: 'rotate(90deg)'
			});
			$(this).find('.projects_file_list').show();
			$(this).attr('data-visibility', '1');
		}

	});
});
// Update the line numbers list

function placeNewLineNumbers(data) {
	$('#linenosblock').empty();
	for(i = 1; i < countLines(); i++) {
		$('#linenosblock').append(
			'<div class="linenos" data-row-number="' +
			(i) + '"><span class="number">' + (i) +
			'</span><span class="current" style="display: none;">&rarr;</span></div>');
	}
	$('#linenosblock').attr(
		'data-total-rows', countLines());
}
// Replicate PHPs in array function.

function in_array(needle, haystack) {
	for(var key in haystack) {
		if(haystack[key] == needle) {
			return true;
		}
		else {
			return false;
		}
	}
}

function countLines() {
	var val = $('#textarea').val();
	var lines = val.split(/\r|\r\n|\n/);
	var count = lines.length;
	return count + 1;
}
// Gets the current line number and changes that number to current line indicator arrow.

function getLineNumber(textarea) {
	value = textarea.value.substr(0,
		textarea.selectionStart).split(
		/\r|\r\n|\n/).length;
	window.currentLine = value + 1;
	$('.linenos .number').show();
	$('.linenos .current').hide();
	$('.linenos[data-row-number=' + value +
		'] .number').hide();
	$('.linenos[data-row-number=' + value +
		'] .current').show();
}
// jQuery ajax call to save file

function saveFile(file_id) {
	$('.status_text').html(
		'Saving&hellip;').css('color',
		'white');
	// get the code from the textarea
	code = $('#textarea').val();
	$.ajax({
		type: 'POST',
		url: 'ajax.php?save',
		dataType: 'JSON',
		data: {
			file_id: file_id,
			code: code
		}
	}).done(function (result) {
		$('.status_text').html(result.txt).css(
			'color', result.clr);
	}).fail(function (result) {
		$('.status_text').html(
			'Failed Save: ' + result).css(
			'color', 'red');
	});
	window.status_text_progress = true;
	window.file_unsaved = 0;
	return true;
}
// Check if file needs saving. Then send request to open a different file

function file_open(new_file_id) {
	// If a file is open
	if(window.file_is_open == 1) {
		// and unsaved
		if(window.file_unsaved == 1) {
			//save file
			window.status_text_progress = true;
			$('.status_text').text(
				'Saving Open File').css('color',
				'white');
			if(saveFile(window.current_file_id) ===
				true) {
				// Open file
				window.current_file_id =
					new_file_id;
				opening_file(new_file_id);
			}
			else {
				window.status_text_progress = true;
				$('.status_text').text(
					'Error Saving').css('color', 'red');
			}
		}
		else {
			// Open file
			window.current_file_id = new_file_id;
			opening_file(new_file_id);
		}
	}
	else {
		// Open file
		window.file_is_open = 1;
		window.current_file_id = new_file_id;
		opening_file(new_file_id);
	}
	return true;
}
// jQuery open file ajax call

function opening_file(file_id) {
	$.ajax({
		url: 'ajax.php?open',
		type: 'POST',
		dataType: 'json',
		data: {
			file_id: file_id
		},
		timeout: 3000
	}).done(function (result) {
		window.status_text_progress = true;
		if(result.data !== false) {
			$('#textarea').val(result.data.code);
			window.current_filename = result.data
				.filename;
			window.current_file_id = result.data
				.file_id;
			// Update page title to name of new file
			document.title = window.current_filename +
				" | Pseudify";
			// Update line numbers for the file
			placeNewLineNumbers();
		}
		$('.status_text').html(result.txt).css(
			'color', result.clr);
	}).fail(function (xhr, text, error) {
		window.status_text_progress = true;
		$('.status_text').text(
			'Cannot open that file.').css(
			'color', 'red');
	});
}
// Load the modal window for file rename

function prep_file_rename(file_id) {
	window.prep_file_rename_id = file_id;
	$('#renameFile').modal();
}
// jQuery ajax call to rename the file

function file_rename(filename) {
	file_id = window.prep_file_rename_id;
	$.ajax({
		url: 'ajax.php?rename',
		type: 'POST',
		dataType: 'json',
		data: {
			file_id: file_id,
			filename: filename
		},
		timeout: 1000
	}).done(function (data) {
		reload_page();
	}).fail(function (xhr, text, error) {
		console.log('XHR: ' + xhr);
		console.log('TEXT: ' + text);
		console.log('ERROR: ' + error);
	});
	// Refresh the page to show changes.
	reload_page();
}
// Load the modal window for the file delete

function prep_file_delete(file_id) {
	window.prep_file_delete_id = file_id;
	$('#deleteFileConfirm').modal();
}
// jQuery ajax call to delete the file

function file_delete() {
	file_id = window.prep_file_delete_id;
	$.ajax({
		url: 'ajax.php?delete',
		type: 'POST',
		dataType: 'json',
		data: {
			file_id: file_id
		},
		timeout: 1000
	});
	// Refresh the page to show changes.
	reload_page();
}
// Angular JS controller for new file

function CtrlNewFileForm($scope) {
	$scope.filename = "Untitled";
}
// Angular JS controller for rename file

function CtrlRenameFileForm($scope) {
	$scope.filename = "Index";
}
// Angular JS controller for download file

function CtrlDownloadListForm($scope) {
	$scope.filename = "Untitled";
	$scope.language = "php";
	$scope.debug = 'false';

}
// jQuery ajax call to check for new file creation

function checkNewFileFormFileName(
	filename) {
	$.ajax({
		url: 'ajax.php?newfile',
		type: 'POST',
		dataType: 'json',
		data: {
			filename: filename
		}
	}).done(function (result) {
		window.status_text_progress = true;
		$('.status_text').html(result.txt).css(
			'color', result.clr);
		reload_page();
	}).fail(function (a, b, c) {
		console.log(a);
		console.log(b);
		console.log(c);
	});
}
// Manual save button

function save() {
	saveFile(window.current_file_id);
}
// Refresh the page, clearing cache.

function reload_page() {
	$('.status_text').html(
		'Refreshing page').css('color',
		'white');
	window.location.reload(true);
}

// JavaScript Document
(function() {
    tinymce.PluginManager.add('tm_rating', function(editor, url) {
		editor.addButton('tm_rating', {
			text: '',
			tooltip: 'Insert Review',
			icon: 'icon-review',
			onclick: function() {
				editor.insertContent('[tmreview]<br class="nc"/>');
			}
		});
	});
})();

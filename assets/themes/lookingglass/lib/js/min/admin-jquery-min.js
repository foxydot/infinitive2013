jQuery(document).ready(function($){$("textarea").blur(function(){var t=$(this).val(),e=/href="([(?!https?:\/\/)|(\#)].*)"/i;t.match(e).length})});